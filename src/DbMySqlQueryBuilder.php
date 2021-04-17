<?php

namespace Haci;

const PLACEHOLDER = '?';

class DbMySqlQueryBuilder implements IDbQueryBuilder {

  function placeHolder(mixed $v = null): string {
    return PLACEHOLDER;
  }

  function makeSelectQuery(
    string $table,
    string $fields = '*',
    string $extras = '',
    array $paramsArr = [],
  ): DbQuery {
    return new DbQuery(
      "SELECT $fields FROM $table $extras",
      $paramsArr,
    );
  }

  function makeInsertQuery(string $table, mixed $data): DbQuery {
    $dataArr   = is_object($data) ? get_object_vars($data): $data;
    $fieldsArr = array_keys($dataArr);
    $fields    = implode(', ', $fieldsArr);
    $paramsArr = array_values($dataArr);
    $valuesArr = array_map([$this, 'placeHolder'], $paramsArr);
    $values    = implode(', ', $valuesArr);

    return new DbQuery(
      "INSERT INTO $table ($fields) VALUES ($values)",
      $paramsArr,
    );
  }

  function makeUpdateQuery(
    string $table,
    mixed $data,
    string $extras = 'WHERE id = ' . PLACEHOLDER,
    array $extraParams = [],
  ): DbQuery {
    $dataArr   = is_object($data) ? get_object_vars($data): $data;
    $paramsArr = array_values($dataArr);
    foreach($extraParams as $v) {
      $paramsArr[] = $v;
    }
    $assignArr = [];
    foreach($dataArr as $k => $v) {
      $assignArr[] = "$k = " . PLACEHOLDER;
    }
    $assignments = implode(', ', $valuesArr);

    return new DbQuery(
      "UPDATE $table SET ($assignments) $extras",
      $paramsArr,
    );
  }

  function makeDeleteQuery(
    string $table,
    string $extras = 'WHERE id = ' . PLACEHOLDER,
    array $paramsArr = [],
  ): DbQuery {
    return new DbQuery(
      "DELETE FROM $table $extras",
      $paramsArr,
    );
  }

}
