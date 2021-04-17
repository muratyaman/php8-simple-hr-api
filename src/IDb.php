<?php

namespace Haci;

use \Haci\Domain\DepartmentModel;
use \Haci\Domain\EmployeeModel;

interface IDb {

  function newQueryBuilder(): IDbQueryBuilder;
  
  function runQuery(DbQuery $query);

  function selectFromTable(string $table);

  function selectFromDepartments();

  function selectFromEmployees();

  function newDepartmentModel(): DepartmentModel;

  function newEmployeeModel(): EmployeeModel;

  function selectDepartmentsWithMaxSalary(ApiInput $input);

  function selectDepartmentsWithEmployeesOverSalary(ApiInput $input);
  
}
