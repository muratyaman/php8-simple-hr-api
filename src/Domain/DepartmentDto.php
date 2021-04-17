<?php

namespace Haci\Domain;

use \Haci\IDto;

class DepartmentDto implements IDto {

  public function __construct(
    public ?int $id = null,
    public ?string $name = null,
  ) {
    // do nothing
  }

  function getId(): string|int {
    return $this->id;
  }
  
  function getDataForInsert(): array {
    return get_object_vars($this);
  }

  function getDataForUpdate(): array {
    $data = get_object_vars($this);
    unset($data['id']);
    return $data;
  }
}
