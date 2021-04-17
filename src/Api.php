<?php

namespace Haci;

use \DateTime;
use \DateTimeInterface;
use \Exception;
use \PDOException;

const PATTERN_DUPLICATE_ENTRY = 'Duplicate entry';
const PATTERN_CANNOT_BE_NULL  = 'cannot be null';

const ERR_UNKNOWN        = 'unexpected error';
const ERR_UNAUTHORIZED   = 'unauthorized';
const ERR_INVALID_ACTION = 'invalid action';
const ERR_DB_UNKNOWN     = 'unexpected database error';
const ERR_DB_DUPLICATE   = 'duplicate value given';
const ERR_DB_FIELD_NIL   = 'null given for a required field';
const ERR_INVALID_INPUT  = 'invalid input';

/**
 * Api class behaves like a proxy/router/controller for actions possible in this service
 * It can be used by various adapters e.g. HTTP, WebSocket and Command Line
 */
class Api {

  private array $apiKeys = [];

  public function __construct(
    private IDb $db,
    private mixed $config = [],
  ) {
    $apiKeysCsv = isset($config['API_KEYS']) ? $config['API_KEYS'] : '';
    $this->apiKeys = explode(';', $apiKeysCsv);
  }

  /**
   * Main entry point to handle all requests
   */
  public function handle(ApiInput $input) {
    error_log(__METHOD__ . ' ' . json_encode($input));
    try {
      if (!in_array($input->apiKey, $this->apiKeys)) {
        throw new ApiException(ERR_UNAUTHORIZED);
      }
      $action = isset($input->action) ? $input->action : null;
      $output = [ 'data' => null, 'meta' => null ];
      $error = null;

      if (($action == null) or ($action === 'handle') or !method_exists($this, $action)) {
        throw new ApiException(ERR_INVALID_ACTION);
      }
      
      $output = call_user_func_array([$this, $action], [$input]);

    } catch (ApiException $ex) {
      $error = $ex->getMessage(); // no need to log business logic errors

    } catch (PDOException $ex) {
      $errMsg = $ex->getMessage(); // localize
      // "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '1' for key 'tbl_department.PRIMARY'"
      // "SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'created_at' cannot be null"
      error_log('PDO ERROR: ' . $errMsg);
      $error = ERR_DB_UNKNOWN;
      if (false !== stripos($errMsg, PATTERN_DUPLICATE_ENTRY)) {
        $error = ERR_DB_DUPLICATE; // clue for end-user
      } else if (false !== stripos($errMsg, PATTERN_CANNOT_BE_NULL)) {
        // TODO: extract field name
        $error = ERR_DB_FIELD_NIL; // clue for end-user
      }

    } catch (Exception $ex) {
      error_log('ERROR: ' . $ex->getMessage());
      $error = ERR_UNKNOWN;
    }

    return [
      'data'  => isset($output['data']) ? $output['data'] : null,
      'meta'  => isset($output['meta']) ? $output['meta'] : null,
      'error' => $error,
    ];
  }

  function department_search(ApiInput $input) {
    $data = $this->db->newDepartmentModel()->search($input->options);
    $meta = [ 'total' => count($data) ];
    return ['data' => $data, 'meta' => $meta ];
  }

  function department_create(ApiInput $input) {
    $model = $this->db->newDepartmentModel();
    // TODO: validate
    try {
      $model->setId($input->dataVal('id', required: true))
        ->setName($input->dataVal('name', required: true));
    } catch (ApiException $ex) {
      throw $ex;
    } catch (Exception $ex) {
      error_log('ERROR: ' . $ex->getMessage());
      throw new ApiException(ERR_INVALID_INPUT);
    }
    return [ 'data' => $model->create() ];
  }
  
  function employee_search(ApiInput $input) {
    $data = $this->db->newEmployeeModel()->search($input->options);
    $meta = [ 'total' => count($data) ];
    return ['data' => $data, 'meta' => $meta ];
  }

  function employee_create(ApiInput $input) {
    $model = $this->db->newEmployeeModel();
    // TODO: validate
    try {
      $model->setId($input->dataVal('id', required: true))
        ->setFirstName($input->dataVal('first_name', required: true))
        ->setLastName($input->dataVal('last_name', required: true))
        ->setDepartmentId($input->dataVal('dept_id', required: true))
        ->setSalary($input->dataVal('salary', 0, required: true))
        ->setDateOfBirth($input->dataVal('dob'));
    } catch (ApiException $ex) {
      throw $ex;
    } catch (Exception $ex) {
      error_log('ERROR: ' . $ex->getMessage());
      throw new ApiException(ERR_INVALID_INPUT);
    }
    return ['data' => $model->create() ];
  }

  /**
   * Show all departments along with the highest salary within each department.
   * A department with no employees should show 0 as the highest salary.
   */
  function report_department_with_max_salary(ApiInput $input) {
    $data = $this->db->selectDepartmentsWithMaxSalary($input);
    $meta = [ 'total' => count($data) ];
    return ['data' => $data, 'meta' => $meta ];
  }

  /**
   * List just those departments that have more than '2' employees that earn over '50000'.
   */
  function report_department_with_employees_over_salary(ApiInput $input) {
    $data = $this->db->selectDepartmentsWithEmployeesOverSalary($input);
    $meta = [ 'total' => count($data) ];
    return ['data' => $data, 'meta' => $meta ];
  }

}
