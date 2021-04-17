<?php

namespace Haci;

interface IDbQueryBuilder {

  function placeHolder(mixed $v = null): string;

  function makeSelectQuery(
    string $table,
    string $fields = '*',
    string $extras = '',
    array $paramsArr = [],
  ): DbQuery;

  function makeInsertQuery(
    string $table,
    mixed $data,
  ): DbQuery;

  function makeUpdateQuery(
    string $table,
    mixed $data,
    string $condition = 'id = ?',
    array $extraParams = [],
  ): DbQuery;

  function makeDeleteQuery(
    string $table,
    string $condition = 'id = ?',
    array $paramsArr = [],
  ): DbQuery;

}
