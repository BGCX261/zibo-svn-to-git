<?php

namespace zibo\library\database\sqlite;

use zibo\core\Zibo;

use zibo\library\database\definition\definer\AbstractDefiner;
use zibo\library\database\definition\Field;
use zibo\library\database\definition\Index;
use zibo\library\database\definition\Table;
use zibo\library\database\exception\DatabaseException;
use zibo\library\database\sqlite\exception\SqliteException;

/**
 * Sqlite implementation of the database definer
 */
class SqliteDefiner extends AbstractDefiner {

    /**
     * Configuration key for the predefined field types
     * @var string
     */
    const CONFIG_FIELD_TYPES = 'database.sqlite.types';

    /**
     * Array with the predefined field types
     * @var array
     */
    private $fieldTypes;

    /**
     * Gets the table definition of an existing table in the database
     * @param string $name Name of the table
     * @return zibo\library\database\definition\Table Table definition
     * @throws zibo\library\database\sqlite\exception\SqliteException
     */
    public function getTable($name) {
        throw new SqliteException('getTable is currently unsupported');
//        $this->validateName($name);
//
//        $tableName = $this->connection->quoteIdentifier($name);
//
//        $sql = 'SHOW FIELDS FROM ' . $tableName;
//        $result = $this->connection->execute($sql);
//
//        if (!$result) {
//            throw new SqliteException('Could not find a definition for table ' . $name . '. Does it exist?');
//        }
//
//        $table = new Table($name);
//        foreach ($result as $data) {
//            $field = new Field($data['Field'], $data['Type']);
//            $field->setDefaultValue($data['Default']);
//
//            if (!$data['Key']) {
//                $table->addField($field);
//                continue;
//            }
//
//            if ($data['Key'] == 'PRI') {
//                $field->setIsPrimaryKey(true);
//                if ($data['Extra']) {
//                    $field->setIsAutoNumbering(true);
//                    $field->setDefaultValue(0);
//                }
//            } elseif ($data['Key'] == 'UNI') {
//                $field->setIsUnique(true);
//            }
//
//            $table->addField($field);
//        }
//
//        $sql = 'SHOW INDEX FROM ' . $tableName;
//        $result = $this->connection->execute($sql);
//
//        $indexes = array();
//
//        foreach ($result as $data) {
//            if ($data['Key_name'] == 'PRIMARY') {
//                continue;
//            }
//
//            if (!array_key_exists($data['Key_name'], $indexes)) {
//                $indexes[$data['Key_name']] = array();
//            }
//
//            $indexes[$data['Key_name']][$data['Seq_in_index']] = $data['Column_name'];
//        }
//
//        foreach ($indexes as $indexName => $indexFields) {
//            $fields = array();
//            foreach ($indexFields as $fieldName) {
//                $fields[$fieldName] = $table->getField($fieldName);
//            }
//
//            $index = new Index($indexName, $fields);
//
//            $table->addIndex($index);
//        }
//
//        return $table;
    }

    /**
     * Alters an existing table
     * @param zibo\library\database\definition\Table $table Table definition of the altered table
     * @return null
     */
    protected function alterTable(Table $table) {
        throw new SqliteException('alterTable is currently unsupported');
//        $tableName = $this->connection->quoteIdentifier($table->getName());
//
//        $databaseTable = $this->getTable($table->getName());
//        $databaseFields = $databaseTable->getFields();
//        $foundDatabaseFields = array();
//
//        $previousFieldName = null;
//
//        $primaryKeys = array();
//        $uniques = array();
//        $indexesToAdd = array();
//        $indexesToDrop = array();
//
//        $sqls = array();
//
//        $fields = $table->getFields();
//        foreach ($fields as $field) {
//            $fieldName = $this->connection->quoteIdentifier($field->getName());
//            $fieldType = $this->getFieldType($field);
//
//            $foundField = false;
//
//            foreach ($databaseFields as $databaseFieldIndex => $databaseField) {
//                if ($field->getName() != $databaseField->getName()) {
//                    continue;
//                }
//
//                $previousFieldName = $fieldName;
//
//                try {
//                    $databaseFieldType = $this->getFieldType($databaseField);
//                } catch (DatabaseException $e) {
//                    $databaseFieldType = $databaseField->getType();
//                }
//
//                if ($fieldType != $databaseFieldType || $field->getDefaultValue() != $databaseField->getDefaultValue()) {
//                    $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $fieldName . ' ' . $fieldName . ' ' . $fieldType;
//
//                    if (!$field->isPrimaryKey()) {
//                        $sql .= ' DEFAULT ' . $this->getDefaultValue($field);
//                    }
//
//                    $sqls [] = $sql;
//                }
//
//                if ($field->isPrimaryKey() != $databaseField->isPrimaryKey() && $field->isPrimaryKey()) {
//                    $primaryKeys[] = $fieldName;
//                }
//
//                if ($field->isUnique() != $databaseField->isUnique()) {
//                    if ($field->isUnique()) {
//                        $uniques[] = $fieldName;
//                    } else {
//                        $indexesToDrop[] = $fieldName;
//                    }
//                }
//
//                $foundField = true;
//                $foundDatabaseFields[$databaseFieldIndex] = true;
//
//                break;
//            }
//
//            if (!$foundField) {
//                $sql = 'ALTER TABLE ' . $tableName . ' ADD ' . $fieldName . ' ' . $fieldType;
//
//                if (!$field->isPrimaryKey()) {
//                    $sql .= ' DEFAULT ' . $this->getDefaultValue($field);
//                }
//
//                if (!$previousFieldName) {
//                    ' FIRST';
//                } else {
//                    ' AFTER ' . $previousFieldName;
//                }
//
//                $sqls[] = $sql;
//
//                if ($field->isPrimaryKey()) {
//                    $primaryKeys[] = $fieldName;
//                }
//
//                $previousFieldName = $field->getName();
//            }
//        }
//
//        $indexes = $table->getIndexes();
//
//        foreach ($indexes as $indexName => $index) {
//            if (!$databaseTable->hasIndex($indexName)) {
//                continue;
//            }
//
//            $databaseIndex = $databaseTable->getIndex($indexName);
//            if ($index->equals($databaseIndex)) {
//                unset($indexes[$indexName]);
//            } else {
//                $indexesToDrop[] = $this->connection->quoteIdentifier($indexName);
//            }
//        }
//
//        foreach ($databaseFields as $databaseFieldIndex => $databaseField) {
//            if (array_key_exists($databaseFieldIndex, $foundDatabaseFields)) {
//                continue;
//            }
//
//            $databaseFieldName = $this->connection->quoteIdentifier($databaseField->getName());
//
//            if ($databaseField->isIndexed()) {
//                $indexesToDrop[] = $databaseFieldName;
//            }
//
//            $sqls[] = 'ALTER TABLE ' . $tableName . ' DROP ' . $databaseFieldName;
//        }
//
//        foreach ($indexesToDrop as $index) {
//            $this->connection->execute('ALTER TABLE ' . $tableName . ' DROP INDEX ' . $index);
//        }
//
//        if ($primaryKeys) {
//            $this->connection->execute('ALTER TABLE ' . $tableName . ' DROP PRIMARY KEY');
//        }
//
//        foreach ($sqls as $sql) {
//            $this->connection->execute($sql);
//        }
//
//        if ($primaryKeys) {
//            $this->connection->execute('ALTER TABLE ' . $tableName . ' ADD PRIMARY KEY (' . implode(', ', $primaryKeys) . ')');
//        }
//
//        if ($uniques) {
//            $this->connection->execute('ALTER TABLE ' . $tableName . ' ADD UNIQUE (' . implode(', ', $uniques) . ')');
//        }
//
//        foreach ($indexesToAdd as $index) {
//            $this->addIndexFromFieldName($tableName, $fieldName);
//        }
//
//        foreach ($indexes as $index) {
//            $this->addIndex($tableName, $index);
//        }
    }

    /**
     * Creates a new table
     * @param zibo\library\database\definition\Table $table Table definition for the new table
     * @return null
     */
    protected function createTable(Table $table) {
        $tableName = $this->connection->quoteIdentifier($table->getName());
        $fields = $table->getFields();

        $primaryKeys = array();
        $indexes = array();
        $uniques = array();

        $sql = '';
        foreach ($fields as $field) {
            $fieldName = $this->connection->quoteIdentifier($field->getName());

            $sql .= $sql == '' ? '' : ', ';
            $sql .= $fieldName;
            $sql .= ' ' . $this->getFieldType($field);

            if ($field->isPrimaryKey()) {
                $primaryKeys[] = $fieldName;
            } else {
                $sql .= ' DEFAULT ' . $this->getDefaultValue($field);
                if ($field->isUnique()) {
                    $uniques[] = $fieldName;
                } elseif ($field->isIndexed()) {
                    $indexes[] = $field->getName();
                }
            }
        }

        if ($primaryKeys) {
            $sql .= ', PRIMARY KEY (' . implode(', ', $primaryKeys) . ')';
        }
        if ($uniques) {
            $sql .= ', UNIQUE (' . implode(', ', $uniques) . ')';
        }

        $sql = 'CREATE TABLE ' . $tableName . ' (' . $sql . ')';
        $this->connection->execute($sql);

        foreach ($indexes as $fieldName) {
            $this->addIndexFromFieldName($table->getName(), $fieldName);
        }

        $indexes = $table->getIndexes();
        foreach ($indexes as $index) {
            $this->addIndex($table->getName(), $index);
        }
    }

    /**
     * Defines the foreign keys for the provided table
     * @param Table $table table definition
     * @return null
     */
    public function defineForeignKeys(Table $table) {

    }

    /**
     * Adds a index for a field to the provided table
     * @param string $tableName Plain name of the table
     * @param string $fieldName Plain name of the field to index
     * @return null
     */
    private function addIndexFromFieldName($tableName, $fieldName) {
        $sql = 'CREATE INDEX ' . $this->connection->quoteIdentifier('index' . ucFirst($tableName) . ucfirst($fieldName)) . ' ON ' . $this->connection->quoteIdentifier($tableName) . ' (' . $this->connection->quoteIdentifier($fieldName) . ')';

        $this->connection->execute($sql);
    }

    /**
     * Adds a index to the provided table
     * @param string $tableName Plain name of the table
     * @param zibo\library\database\definition\Index $index Index to add
     * @return null
     */
    private function addIndex($tableName, Index $index) {
        $fields = $index->getFields();
        foreach ($fields as $fieldName => $field) {
            $fields[$fieldName] = $this->connection->quoteIdentifier($fieldName);
        }

        $sql = 'CREATE INDEX ' . $this->connection->quoteIdentifier('index' . ucFirst($tableName) . ucfirst($index->getName())) . ' ON ' . $this->connection->quoteIdentifier($tableName) . ' (';
        $sql .= implode(', ', $fields) . ')';

        $this->connection->execute($sql);
    }

    /**
     * Checks if a table exists
     * @param string $name name of the table to check
     * @return boolean true if the table exists, false otherwise
     * @throws zibo\library\database\Exception\DatabaseException when the name is empty or not a string
     */
    public function tableExists($name) {
        $this->validateName($name);

        $sql = 'SELECT name FROM sqlite_master WHERE type = ' . $this->connection->quoteValue('table') . ' AND name = ' . $this->connection->quoteValue($name);

        $result = $this->connection->execute($sql);

        return $result->getRowCount() == 0 ? false : true;
    }

    /**
     * Gets a list of the tables in the database connection
     * @return array Array with table names
     */
    public function getTableList() {
        $sql = 'SELECT name FROM sqlite_master WHERE type = ' . $this->connection->quoteValue('table');

        $result = $this->connection->execute($sql);

        $tables = array();
        foreach ($result as $row) {
            $tables[] = $row['name'];
        }

        return $tables;
    }

    /**
     * Gets the predefined types for this definer
     * @return array Array with the name of the predefined type as key and the database type as value
     */
    public function getFieldTypes() {
        if (!isset($this->fieldTypes)) {
            $this->fieldTypes = Zibo::getInstance()->getConfigValue(self::CONFIG_FIELD_TYPES);
        }

        return $this->fieldTypes;
    }

}