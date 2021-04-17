<?php

namespace Haci\Domain;

use \Haci\IDb;
use \Haci\IDto;
use \Haci\DbModel;

class EmployeeModel extends DbModel {

  public function __construct(
    protected IDb $db,
    protected string $tableName,
    protected EmployeeDto $dto,
  ) {
    // do nothing
  }

  function getDto(): IDto {
    return $this->dto;
  }

  public function getId(): string|int {
    return $this->dto->getId();
  }
  
  /**
   * Set ID and provide a 'fluent' interface for easy chaining of 'setters'
   */
  public function setId(string $val): EmployeeModel {
    $this->dto->id = $val; // TODO validate or generate uuid()
    return $this;
  }

  public function getFirstName(): string {
    return $this->dto->first_name;
  }

  public function setFirstName(string $val): EmployeeModel {
    $this->dto->first_name = $val; // TODO validate
    return $this;
  }
  
  public function getLastName(): string {
    return $this->dto->last_name;
  }

  public function setLastName(string $val): EmployeeModel {
    $this->dto->last_name = $val; // TODO validate
    return $this;
  }
  
  public function getDepartmentId(): string {
    return $this->dto->dept_id;
  }

  // TODO
  // public function getDepartment(): DepartmentModel {
  //   return $this->dto->dept_id;
  // }

  public function setDepartment(DepartmentModel $department) {
    $this->dto->dept_id = $department->getId();
  }

  public function setDepartmentId(int $val): EmployeeModel {
    $this->dto->dept_id = $val; // TODO validate
    return $this;
  }

  public function getDateOfBirth(): DateTime {
    return new DateTime($this->dto->dob);
  }

  public function setDateOfBirth(?string $val = null): EmployeeModel {
    $this->dto->dob = $val; // TODO validate
    return $this;
  }

  public function getSalary(): float {
    return $this->dto->salary;
  }

  public function setSalary(float $val = 0): EmployeeModel {
    $this->dto->salary = $val; // TODO validate
    return $this;
  }

  public function getCreatedAt(): DateTime {
    return new DateTime($this->dto->created_at);
  }

  public function getUpdatedAt(): DateTime {
    return new DateTime($this->dto->update_at);
  }

  public function toJson(): string {
    return json_encode($this->dto);
  }

  function search(mixed $options = []): array {
    // TODO: use options e.g. filter, sort, paginate
    return $this->db->selectFromEmployees(
      fields: 'last_name, first_name, salary, dob, dept_id, id',
      extras: 'ORDER BY 1, 2',
    );
  }
}
