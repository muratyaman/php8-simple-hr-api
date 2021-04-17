<?php

namespace Haci;

class ApiUser {

  function __construct(
    private ?string $id = null,
    private ?string $username = null,
  ) {

  }

}
