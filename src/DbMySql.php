<?php

namespace Haci;

use \PDO;
use \Haci\Domain\DepartmentDto;
use \Haci\Domain\DepartmentModel;
use \Haci\Domain\EmployeeDto;
use \Haci\Domain\EmployeeModel;

const TBL_DEPARTMENT = 'tbl_department';
const TBL_EMPLOYEE   = 'tbl_employee';

const DEFAULT_FETCH = PDO::FETCH_OBJ;

class DbMySql extends PDO implements IDb {

  public static function newInstance($config) {
    $db = new DbMySql(...$config);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }

  function newQueryBuilder(): IDbQueryBuilder {
    return new DbMySqlQueryBuilder();
  }

  function runQuery(DbQuery $qry) {
    error_log(__METHOD__ . ' SQL: ' . $qry->sql);
    if (count($qry->params) > 0) {
      // TODO: keep a cache of prepared queries and reuse if possible
      $stmt = $this->prepare($qry->sql);
      $stmt->execute($qry->params);
    } else {
      $stmt = $this->query($qry->sql);
    }
    $rows = [];
    if ($qry->isSelectQuery()) {
      $rows = $stmt->fetchAll(DEFAULT_FETCH);
    }
    error_log(__METHOD__ . ' rows: ' . json_encode($rows));
    return $rows;
  }

  // helper functions for ease of use ===============================

  public function selectFromTable(string $table, string $fields = '*', string $extras = '') {
    $qry = $this->newQueryBuilder()->makeSelectQuery($table, $fields, $extras);
    return $this->runQuery($qry);
  }

  public function selectFromDepartments(string $fields = '*', string $extras = '') {
    return $this->selectFromTable(TBL_DEPARTMENT, $fields, $extras);
  }

  public function selectFromEmployees(string $fields = '*', string $extras = '') {
    return $this->selectFromTable(TBL_EMPLOYEE, $fields, $extras);
  }

  public function newDepartmentModel(): DepartmentModel {
    return new DepartmentModel($this, TBL_DEPARTMENT, new DepartmentDto());
  }

  public function newEmployeeModel(): EmployeeModel {
    return new EmployeeModel($this, TBL_EMPLOYEE, new EmployeeDto());
  }

  /**
   * Show all departments along with the highest salary within each department.
   * A department with no employees should show 0 as the highest salary.
   */
  function selectDepartmentsWithMaxSalary(ApiInput $input) {
    $tblDept = TBL_DEPARTMENT;
    $tblEmp = TBL_EMPLOYEE;
    $sql = <<<SQL1
SELECT
  d.name,
  COALESCE(e2.max_emp_salary, 0) AS max_emp_salary,
  COALESCE(e2.emp_count, 0) AS emp_count,
  d.id
FROM $tblDept d
LEFT JOIN (
  SELECT
    e.dept_id,
    MAX(e.salary) AS max_emp_salary,
    COUNT(*) AS emp_count
  FROM $tblEmp e
  GROUP BY e.dept_id
  ORDER BY 2 DESC
) e2
ON d.id = e2.dept_id
ORDER BY 1 ASC
SQL1;
    return $this->runQuery(new DbQuery($sql));
  }

  function selectDepartmentsWithEmployeesOverSalary(ApiInput $input) {
    $ec = $input->data['employee_count'];
    $st = $input->data['salary_threshold'];
    $tblDept = TBL_DEPARTMENT;
    $tblEmp = TBL_EMPLOYEE;
    $sql = <<<SQL2
SELECT
  d.name,
  e2.max_emp_salary,
  e2.emp_count,
  d.id
FROM $tblDept d
INNER JOIN (
  SELECT
    e.dept_id,
    MAX(e.salary) AS max_emp_salary,
    COUNT(*) AS emp_count
  FROM $tblEmp e
  WHERE ? <= e.salary
  GROUP BY e.dept_id
  HAVING ? <= COUNT(*)
  ORDER BY 2 DESC
) e2
ON d.id = e2.dept_id
ORDER BY 1 ASC
SQL2;
    return $this->runQuery(new DbQuery($sql, [ $st, $ec ]));    
  }

}
