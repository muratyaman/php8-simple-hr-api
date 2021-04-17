<?php

namespace Haci\Domain;

use \Haci\IDto;

class EmployeeDto implements IDto {

  public function __construct(
    public ?string $id = null,
    public ?string $first_name = null,
    public ?string $last_name = null,
    public ?int    $dept_id = null,
    public ?float  $salary = null,
    public ?string $dob = null,
    public ?string $created_at = null,
    public ?string $updated_at = null,
  ) {
    // do nothing
  }

  function getId(): string|int {
    return $this->id;
  }

  function getDataForInsert(): array {
    $data = get_object_vars($this);
    unset($data['created_at']);
    unset($data['updated_at']);
    return $data;
  }
  
  function getDataForUpdate(): array {
    $data = get_object_vars($this);
    unset($data['id']);
    unset($data['created_at']);
    unset($data['updated_at']);
    return $data;
  }
}
