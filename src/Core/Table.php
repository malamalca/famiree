<?php
declare(strict_types=1);

namespace App\Core;

class Table
{
    public $entityName;
    public $tableName;
    public $fieldList = [];
    public $idField = 'id';

    public $lastError;

    public function __construct()
    {
        $classVars = get_class_vars($this->entityName);

        $this->fieldList = array_keys($classVars);
    }

    public function __call($method, $args)
    {
        if (substr($method, 0, 6) == 'findBy') {
            return call_user_func([$this, 'findByField'], substr($method, 6), $args);
        }
    }

    public function query($sql, $args = [], $options = []) {
        $pdo = DB::getInstance()->connect();

        $stmt = $pdo->prepare($sql);

        foreach ($args as $fieldName => $fieldValue) {
            $stmt->bindValue(':' . $fieldName, $fieldValue);
        }

        $result = $stmt->execute();

        if ($result) {
            $ret = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                if (!isset($options['hydrate']) || $options['hydrate'] === true) {
                    $entity = self::newEntity($row);
                } else {
                    $entity= $row;
                }

                if (!empty($options['group']) && isset($row[$options['group']])) {
                    $ret[$row[$options['group']]][] = $entity;
                } else {
                    $ret[] = $entity;
                }
            }

            return $ret;
        } else {
            $this->lastError = $stmt->errorInfo();
        }
    }

    /**
     * Magic Method For Finding records by name
     *
     * @param string $fields Fields part of method name
     * @param array $args Arhuments with field values
     * @return array
     */
    private function findByField($fields, $args) {
        $pdo = DB::getInstance()->connect();

        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ';
        $fields = explode('And', $fields);

        // convert from camel case to snake case
        foreach ($fields as $i => $fieldName) {
            $fields[$i] = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $fieldName));
        }

        // build fields sql part
        $fieldsSql = '';
        foreach ($fields as $fieldName) {
            if (!empty($fieldsSql)) {
                $fieldsSql .= ' AND ';
            }
            
            $fieldsSql .= $fieldName . '=:' . $fieldName;
        }

        $stmt = $pdo->prepare($sql . $fieldsSql);

        foreach ($fields as $i => $fieldName) {
            $stmt->bindValue(':' . $fieldName, $args[$i]);
        }

        $result = $stmt->execute();


        if ($result) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                return self::newEntity($row);
            }
        } else {
            $this->lastError = $stmt->errorInfo();
        }
    }

    /**
     * Create new entity from data
     *
     * @param array $data Data to fill entity with
     * @return object
     */
    public function newEntity($data = [])
    {
        $entity = new $this->entityName();

        if (is_array($data)) {
            foreach ($this->fieldList as $field) {
                if (isset($data[$field])) {
                    $entity->{$field} = $data[$field];
                }
            }
        } else {
            $entity->{$this->idField} = $data;
        }

        return $entity;
    }

    /**
     * Fetch entity by id
     *
     * @param string $id Entity id
     * @return object|null
     */
    public function get($id)
    {
        $pdo = DB::getInstance()->connect();

        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE ' . $this->idField . '=:' . $this->idField;
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':' . $this->idField, $id, \PDO::PARAM_STR);

        $result = $stmt->execute();

        if ($result) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                return self::newEntity($row);
            }
        } else {
            $this->lastError = $stmt->errorInfo();
        }

        return null;
    }

    /**
     * Check if entity exists
     *
     * @param string $id Entity id
     * @return bool
     */
    public function exists($id)
    {
        $pdo = DB::getInstance()->connect();

        $sql = 'SELECT COUNT(id) AS cnt FROM ' . $this->tableName . ' WHERE ' . $this->idField . '=:' . $this->idField;
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':' . $this->idField, $id, \PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchColumn();

        return (int)$result > 0;
    }

    /**
     * Save entity
     *
     * @param object $entity Entity object
     * @param array $fieldFilter Specify fields to save. Save all if empty
     * @return bool
     */
    public function save($entity, $fieldFilter = [])
    {
        $pdo = DB::getInstance()->connect();

        $exists = $this->exists($entity->{$this->idField});

        try {
            if ($exists) {
                // UPDATE statement
                $fieldNameValue = '';
                foreach ($this->fieldList as $field) {
                    if (empty($fieldFilter) || in_array($field, $fieldFilter)) {
                        if ($field != $this->idField) {
                            if ($fieldNameValue != '') {
                                $fieldNameValue .= ', ';
                            }

                            $fieldNameValue .= $field . '=:' . $field;
                        }
                    }
                }

                $sql = sprintf(
                    'UPDATE %1$s SET %2$s WHERE %3$s=:%3$s',
                    $this->tableName,
                    $fieldNameValue,
                    $this->idField
                );
            } else {
                // INSERT statement
                //$fieldList = implode(', ', $this->fieldList);
                //$valuesList = ':' . implode(', :', $this->fieldList);
                $fieldList = '';
                $valuesList = '';

                foreach ($this->fieldList as $field) {
                    if (empty($fieldFilter) || in_array($field, $fieldFilter)) {
                        if ($fieldList != '') {
                            $fieldList .= ', ';
                            $valuesList .= ', ';
                        }

                        $fieldList .= $field;
                        $valuesList .= ':' . $field;
                    }
                }

                $sql = sprintf(
                    'INSERT INTO %1$s (%2$s) VALUES (%3$s)',
                    $this->tableName,
                    $fieldList,
                    $valuesList
                );
            }

            // prepare parameter values
            $stmt = $pdo->prepare($sql);
            foreach ($this->fieldList as $field) {
                if (empty($fieldFilter) || in_array($field, $fieldFilter)) {
                    $stmt->bindValue(':' . $field, $entity->{$field});
                }
            }

            // bind id field on update even if not specified in fieldlist
            if (!empty($fieldFilter) && !in_array($this->idField, $fieldFilter) && $exists) {
                $stmt->bindValue(':' . $this->idField, $entity->{$this->idField});
            }

            // execute query
            $result = (bool)$stmt->execute();

            if (!$result) {
                $this->lastError = $stmt->errorInfo();
            }

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete entity
     *
     * @param object $entity Entity object
     * @return bool
     */
    public function delete($entity)
    {
        $pdo = DB::getInstance()->connect();

        try {
            $stmt = $pdo->prepare(sprintf(
                'DELETE FROM %1$s WHERE %2$s = :%2$s',
                $this->tableName,
                $this->idField
            ));
            $stmt->bindValue(':' . $this->idField, $entity->{$this->idField}, \PDO::PARAM_STR);

            return (bool)$stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
