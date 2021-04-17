<?php
require_once __DIR__ . '/vendor/autoload.php';

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Pimple\Container;
use Dotenv\Dotenv;
use Haci\Api;
use Haci\ApiInput;
use Haci\DbMySql;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$httpServer = new Server('127.0.0.1', 9090);

$httpServer->on('start', function (Server $server) {
  echo 'Swoole http server is started at http://127.0.0.1:9090' . PHP_EOL;
});

$httpServer->on('request', function (Request $request, Response $response) {
  $t0 = $request->server['request_time_float'];
  $di = new Container();

  $di['config']   = $_ENV;
  $di['dbConfig'] = [$_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS']];
  
  $di['db'] = DbMySql::newInstance($di['dbConfig']);

  $api = new Api($di['db'], $di['config']); // it cannot be outside this function

  $reqHeaders = $request->header;
  $reqServer  = $request->server;
  error_log('HEADERS: ' . json_encode($request));

  // TODO: get headers 'x-api-key' and 'authorization'
  $reqUri    = isset($reqServer['request_uri']) ? $reqServer['request_uri'] : null;
  $apiKey    = isset($reqHeaders['x-api-key']) ? $reqHeaders['x-api-key'] : null;
  $authToken = isset($reqHeaders['authorization']) ? $reqHeaders['authorization'] : null; // TODO
  $post      = json_decode($request->getContent(), true);
  $input     = ApiInput::fromPost($post, $reqUri, $apiKey, $authToken);

  $output = $api->handle($input);
  $json   = json_encode($output);

  $t1 = microtime(true);
  $delta = $t1 - $t0; // ignores time to transport data

  $response->header('content-type', 'application/json');
  $response->header('x-response-time', $delta);
  $response->end($json);
});

$httpServer->start();
