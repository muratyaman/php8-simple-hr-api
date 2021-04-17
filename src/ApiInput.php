<?php

namespace Haci;

class ApiInput {

  function __construct(
    public ?string $action = null,
    public mixed $data = null,
    public mixed $options = null,
    public string | int | null $id = null,
    public ?string $apiKey = null,
    public ?ApiUser $user = null,
  ) {

  }

  static function fromPost(
    mixed $body = null,
    ?string $reqUri = null,
    ?string $apiKey = null,
    ?string $authToken = null,
  ): ApiInput {
    $arr = is_object($body) ? get_object_vars($body) : $body;
    $action = 'unknown';
    if ($reqUri and (1 < strlen($reqUri))) {
      $action = substr($reqUri, 1); // ignore '/department_search' ==> 'department_search'
    }
    if (isset($arr['action']) and(1 < strlen($arr['action']))) {
      $action = $arr['action'];
    }
    return new ApiInput(
      $action,
      isset($arr['data']) ? $arr['data'] : null,
      isset($arr['options']) ? $arr['options'] : null,
      isset($arr['id']) ? $arr['id'] : null,
      $apiKey,
      new ApiUser(), // TODO e.g. decode authToken (JWT)
    );
  }

  /**
   * Get data entry for given key
   * Basic validation for missing keys 
   */
  function dataVal(string $key, mixed $defaultVal = null, bool $required = false): mixed {
    $val = $defaultVal;
    if (isset($this->data[$key])) $val = $this->data[$key];
    if ($required and ($val === null)) {
      throw new ApiException("$key is required");
    }
    return $val;
  }

}
