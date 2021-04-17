<?php
namespace Haci;

const DEFAULT_ID_NAME = 'id';

abstract class DbModel implements IDbModel {

  protected IDb $db;
  protected string $tableName;

  function getDb(): IDb {
    return $this->db;
  }

  function getTableName(): string {
    return $this->tableName;
  }

  function getIdName(): string {
    return DEFAULT_ID_NAME;
  }

  function getId(): string|int {
    return $this->dto->getId();
  }

  function search(mixed $options = []): array {
    // TODO: use options e.g. filter, sort, paginate
    return $this->db->selectFromTable($this->tableName);
  }

  function create(): bool {
    $qry = $this->db->newQueryBuilder()->makeInsertQuery(
      $this->tableName,
      $this->dto->getDataForInsert(),
    );
    $rows = $this->db->runQuery($qry);
    return true; // no error, assume success
  }

  function retrieve(): mixed {
    $id     = $this->dto->getId();
    $idName = $this->dto->getIdName();
    $qry = $this->db->newQueryBuilder()->makeSelectQuery(
      $this->tableName,
      "WHERE $idName = ?",
      [ $id ],
    );
    return $this->db->runQuery($qry);
  }

  function update(): bool {
    $id     = $this->dto->getId();
    $idName = $this->dto->getIdName();
    $data   = get_object_vars($this->dto->getDataForUpdate());
    unset($data[$idName]); // exclude id field
    $qry = $this->db->newQueryBuilder()->makeUpdateQuery(
      $this->tableName,
      $data,
      "WHERE $idName = ?",
      [ $id ],
    );
    return $this->db->runQuery($qry);
  }

  function delete(): bool {
    $id     = $this->dto->getId();
    $idName = $this->dto->getIdName();
    $qry = $this->db->newQueryBuilder()->makeDeleteQuery(
      $this->tableName,
      "WHERE $idName = ?",
      [ $id ],
    );
    return $this->db->runQuery($qry);
  }

}
