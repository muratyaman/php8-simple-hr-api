<?php

namespace Haci\Domain;

use \Haci\IDb;
use \Haci\IDto;
use \Haci\DbModel;

class DepartmentModel extends DbModel {

  public function __construct(
    protected IDb $db,
    protected string $tableName,
    protected DepartmentDto $dto,
  ) {
    // do nothing
  }

  function getDto(): IDto {
    return $this->dto;
  }

  /**
   * Set ID and provide a 'fluent' interface for easy chaining of 'setters'
   */
  function setId(int $id): DepartmentModel {
    $this->dto->id = $id;
    return $this;
  }

  public function getId(): int {
    return $this->dto->id;
  }

  function setName(string $name): DepartmentModel {
    $this->dto->name = $name;
    return $this;
  }

  public function getName(): string {
    return $this->dto->name;
  }

  public function getEmployees() {
    // TODO
    return [];
  }

  public function toJson(): string {
    return json_encode($this->dto);
  }

  function search(mixed $options = []): array {
    // TODO: use options e.g. filter, sort, paginate
    return $this->db->selectFromDepartments(
      fields: 'name, id',
      extras: 'ORDER BY 1',
    );
  }

}
