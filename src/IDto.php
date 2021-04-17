<?php

namespace Haci;

/**
 * Interface for Data Transfer Object
 */
interface IDto {
  function getId(): string|int;
  function getDataForInsert(): array;
  function getDataForUpdate(): array;
}
