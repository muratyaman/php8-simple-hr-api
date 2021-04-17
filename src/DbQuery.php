<?php

namespace Haci;

class DbQuery {
  function __construct(
    public string $sql,
    public array $params = [],
  ) {
    // do nothing
  }

  function isSelectQuery(): bool {
    return substr($this->sql, 0, 6) === 'SELECT';
  }
}
