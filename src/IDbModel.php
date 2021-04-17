<?php
namespace Haci;

interface IDbModel {

  function getDb(): IDb;

  function getTableName(): string;

  function getDto(): IDto;

  function getIdName(): string; // return PK field name

  function getId(): string|int; // return id

  function search(mixed $options): array;

  function create(): bool; // TODO: if ID auto-generated return ID of new record

  function retrieve(): mixed; // return model

  function update(): bool;

  function delete(): bool;

}
