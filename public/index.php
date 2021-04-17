<?php
// HANDLE INCOMING HTTP REQUESTS

$t0 = $_SERVER['REQUEST_TIME_FLOAT'];

error_reporting(-1); // report all PHP errors
ini_set('display_errors', 'off'); // do not let end-user know

function myShutDownFunction() {
  $error = error_get_last(); // handle fatal errors
  error_log('myShutDownFunction: LAST ERROR: ' . json_encode($error));
  if (isset($error['type']) and ($error['type'] === E_ERROR)) {
    panic(true); // maybe we can still inform end-user
  }
}
register_shutdown_function('myShutDownFunction');

function myErrHandler($errno, $errstr, $errfile, $errline) {
  error_log("\nERROR #$errno, file: $errfile line: $errline\n$errstr\n");
  if ($errno === E_USER_ERROR) return panic(); // there is a BUG * * *  
  return true; // do not execute PHP internal error handler
}

$oldErrHandler = set_error_handler('myErrHandler');

function panic($ending = false) {
  if (!$ending) set_error_handler($oldErrHandler); // just in case
  echo json_encode(['error' => 'unexpected server error']);
  header('content-type: application/json');
  http_response_code(500);
  exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';

use Pimple\Container;
use Dotenv\Dotenv;
use Haci\Api;
use Haci\ApiInput;
use Haci\DbMySql;

function getPost(): array {
  error_log(json_encode($_SERVER));
  if (!empty($_POST)) return $_POST;
  $post = json_decode(file_get_contents('php://input'), true);
  if (json_last_error() == JSON_ERROR_NONE) return $post;
  return [];
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$di = new Container(); // TODO: use Container for Dependency Injection purposes

$di['config']   = $_ENV;
$di['dbConfig'] = [$_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS']];

$di['db'] = DbMySql::newInstance($di['dbConfig']);

$api = new Api($di['db'], $di['config']);

$reqUri    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$apiKey    = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null;
$authToken = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null; // TODO
$input     = ApiInput::fromPost(getPost(), $reqUri, $apiKey, $authToken);

$output = $api->handle($input);

$t1 = microtime(true);
echo json_encode($output);
$delta = $t1 - $t0; // ignores time to transport data

header('content-type: application/json');
header("x-response-time: $delta");
http_response_code(200);
