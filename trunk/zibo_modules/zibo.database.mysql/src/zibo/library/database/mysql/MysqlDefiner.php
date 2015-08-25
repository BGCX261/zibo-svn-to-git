<?php

namespace zibo\library\database\mysql;

use zibo\core\Zibo;

use zibo\library\database\definition\definer\AbstractDefiner;
use zibo\library\database\definition\Field;
use zibo\library\database\definition\ForeignKey;
use zibo\library\database\definition\Index;
use zibo\library\database\definition\Table;
use zibo\library\database\driver\Driver;
use zibo\library\database\exception\DatabaseException;
use zibo\library\database\mysql\exception\MysqlException;

/**
 * Mysql implementation of the database definer
 */
class MysqlDefiner extends AbstractDefiner {

    /**
     * Configuration key for the predefined field types
     * @var string
     */
    const CONFIG_FIELD_TYPES = 'database.mysql.types';

    /**
     * Array with the predefined field types
     * @var array
     */
    private $fieldTypes;

    /**
     * Array with the loaded table definitions of the database
     * @var array
     */
    private $tables;

    /**
     * Constructs a new definer
     * @param zibo\library\database\driver\Driver $connection
     * @return null
     */
    public function __construct(Driver $driver) {
        parent::__construct($driver);

        $this->tables = array();
    }

    /**
     * Gets the table definition of an existing table in the database
     * @param string $name Name of the table
     * @return zibo\library\database\definition\Table Table definition
     * @throws zibo\library\database\mysql\exception\MysqlException
     */
    public function getTable($name) {
        $this->validateName($name);

        if (array_key_exists($name, $this->tables)) {
            return $this->tables[$name];
        }

        $table = new Table($name);

        $fields = $this->getTableFields($name);
        foreach ($fields as $field) {
            $table->addField($field);
        }

        $foreignKeys = $this->getTableForeignKeys($name);
        foreach ($foreignKeys as $foreignKey) {
            $table->setForeignKey($foreignKey);
        }

        $indexes = $this->getTableIndexes($table);
        foreach ($indexes as $index) {
            $table->addIndex($index);
        }

        $this->tables[$name] = $table;

        return $table;
    }

    /**
     * Gets the foreign keys of a table
     * @param string $name Name of the table
     * @return array Array with ForeignKey objects
     */
    protected function getTableFields($name) {
        $tableName = $this->connection->quoteIdentifier($name);

        $sql = 'SHOW FIELDS FROM ' . $tableName;
        $result = $this->connection->execute($sql);

        if (!$result) {
            throw new MysqlException('Could not find any fields for table ' . $name . '. Does it exist?');
        }

        $fields = array();

        foreach ($result as $data) {
            $field = new Field($data['Field'], $data['Type']);
            $field->setDefaultValue($data['Default']);

            $fields[] = $field;

            if (!$data['Key']) {
                continue;
            }

            if ($data['Key'] == 'PRI') {
                $field->setIsPrimaryKey(true);
                if ($data['Extra']) {
                    $field->setIsAutoNumbering(true);
                    $field->setDefaultValue(0);
                }
            } elseif ($data['Key'] == 'UNI') {
                $field->setIsUnique(true);
            }
        }

        return $fields;
    }

    /**
     * Gets the foreign keys of a table
     * @param string $name Name of the table
     * @return array Array with ForeignKey objects
     */
    protected function getTableForeignKeys($name) {
        $foreignKeys = array();

        $tableName = $this->connection->quoteIdentifier($name);

        $sql = 'SHOW CREATE TABLE ' . $tableName;
        $result = $this->connection->execute($sql);

        $data = $result->getFirst();
        $sql = str_replace("\n", '', $data['Create Table']);
        $sql = substr($sql, strpos($sql, '('));
        $sql = substr($sql, 0, strrpos($sql, ')'));

        $lines = explode(',', $sql);
        foreach ($lines as $line) {
            $line = trim($line);

            if (strpos($line, 'CONSTRAINT') !== 0) {
                continue;
            }

            $nameStart = strpos($line, 'CONSTRAINT `') + 12;
            $nameStop = strpos($line, '`', $nameStart);
            $name = substr($line, $nameStart, $nameStop - $nameStart);

            $fieldNameStart = strpos($line, 'FOREIGN KEY (`') + 14;
            $fieldNameStop = strpos($line, '`)', $fieldNameStart);
            $fieldName = substr($line, $fieldNameStart, $fieldNameStop - $fieldNameStart);

            $referenceTableNameStart = strpos($line, 'REFERENCES `') + 12;
            $referenceTableNameStop = strpos($line, '`', $referenceTableNameStart);
            $referenceTableName = substr($line, $referenceTableNameStart, $referenceTableNameStop - $referenceTableNameStart);

            $referenceFieldNameStart = strpos($line, '(`', $referenceTableNameStop) + 2;
            $referenceFieldNameStop = strpos($line, '`', $referenceFieldNameStart);
            $referenceFieldName = substr($line, $referenceFieldNameStart, $referenceFieldNameStop - $referenceFieldNameStart);

            $foreignKeys[] = new ForeignKey($fieldName, $referenceTableName, $referenceFieldName, $name);
        }

        return $foreignKeys;
    }

    /**
     * Gets the indexes of a table
     * @param zibo\library\database\definition\Table $table Definition of the table
     * @return array Array with Index objects
     */
    protected function getTableIndexes(Table $table) {
        $tableName = $this->connection->quoteIdentifier($table->getName());

        $sql = 'SHOW INDEX FROM ' . $tableName;
        $result = $this->connection->execute($sql);

        $indexData = array();

        foreach ($result as $data) {
            if ($data['Key_name'] == 'PRIMARY') {
                continue;
            }

            if (!array_key_exists($data['Key_name'], $indexData)) {
                $indexData[$data['Key_name']] = array();
            }

            $indexData[$data['Key_name']][$data['Seq_in_index']] = $data['Column_name'];
        }

        $indexes = array();
        foreach ($indexData as $indexName => $indexFields) {
            $fields = array();
            foreach ($indexFields as $fieldName) {
                $fields[$fieldName] = $table->getField($fieldName);
            }

            $indexes[] = new Index($indexName, $fields);
        }

        return $indexes;
    }

    /**
     * Alters an existing table
     * @param zibo\library\database\definition\Table $table Table definition of the altered table
     * @return null
     */
    protected function alterTable(Table $table) {
        $tableName = $this->connection->quoteIdentifier($table->getName());

        $databaseTable = $this->getTable($table->getName());
        $databaseFields = $databaseTable->getFields();
        $foundDatabaseFields = array();

        $previousFieldName = null;

        $primaryKeys = array();
        $uniques = array();
        $indexesToAdd = array();
        $indexesToDrop = array();
        $foreignKeysToDrop = array();

        $sqls = array();

        $fields = $table->getFields();
        foreach ($fields as $field) {
            $fieldName = $this->connection->quoteIdentifier($field->getName());
            $fieldType = $this->getFieldType($field);

            $foundField = false;

            foreach ($databaseFields as $databaseFieldIndex => $databaseField) {
                if ($field->getName() != $databaseField->getName()) {
                    continue;
                }

                $previousFieldName = $fieldName;

                try {
                    $databaseFieldType = $this->getFieldType($databaseField);
                } catch (DatabaseException $e) {
                    $databaseFieldType = $databaseField->getType();
                }

                if ($fieldType != $databaseFieldType || $field->getDefaultValue() != $databaseField->getDefaultValue()) {
                    $sql = 'ALTER TABLE ' . $tableName . ' CHANGE ' . $fieldName . ' ' . $fieldName . ' ' . $fieldType;

                    if (!$field->isPrimaryKey()) {
                        $sql .= ' DEFAULT ' . $this->getDefaultValue($field);
                    }

                    $sqls [] = $sql;
                }

                if ($field->isPrimaryKey() != $databaseField->isPrimaryKey() && $field->isPrimaryKey()) {
                    $primaryKeys[] = $fieldName;
                }

                if ($field->isUnique() != $databaseField->isUnique()) {
                    if ($field->isUnique()) {
                        $uniques[] = $fieldName;
                    } else {
                        $indexesToDrop[] = $fieldName;
                    }
                }

                $foundField = true;
                $foundDatabaseFields[$databaseFieldIndex] = true;

                break;
            }

            if (!$foundField) {
                $sql = 'ALTER TABLE ' . $tableName . ' ADD ' . $fieldName . ' ' . $fieldType;

                if (!$field->isPrimaryKey()) {
                    $sql .= ' DEFAULT ' . $this->getDefaultValue($field);
                }

                if (!$previousFieldName) {
                    ' FIRST';
                } else {
                    ' AFTER ' . $previousFieldName;
                }

                $sqls[] = $sql;

                if ($field->isPrimaryKey()) {
                    $primaryKeys[] = $fieldName;
                }

                $previousFieldName = $field->getName();
            }
        }

        $indexes = $table->getIndexes();
        foreach ($indexes as $indexName => $index) {
            if (!$databaseTable->hasIndex($indexName)) {
                continue;
            }

            $databaseIndex = $databaseTable->getIndex($indexName);
            if ($index->equals($databaseIndex)) {
                unset($indexes[$indexName]);
            } else {
                $indexesToDrop[] = $this->connection->quoteIdentifier($indexName);
            }
        }

        foreach ($databaseFields as $databaseFieldIndex => $databaseField) {
            if (array_key_exists($databaseFieldIndex, $foundDatabaseFields)) {
                continue;
            }

            $databaseFieldName = $databaseField->getName();

            if ($databaseTable->hasForeignKey($databaseFieldName)) {
                $foreignKey = $databaseTable->getForeignKey($databaseFieldName);
                $foreignKeysToDrop[] = $this->connection->quoteIdentifier($foreignKey->getName());
            }

            $databaseFieldName = $this->connection->quoteIdentifier($databaseFieldName);

            if ($databaseField->isIndexed()) {
                $indexesToDrop[] = $databaseFieldName;
            }

            $sqls[] = 'ALTER TABLE ' . $tableName . ' DROP ' . $databaseFieldName;
        }

        foreach ($foreignKeysToDrop as $foreignKey) {
            $this->connection->execute('ALTER TABLE ' . $tableName . ' DROP FOREIGN KEY ' . $foreignKey);
        }

        foreach ($indexesToDrop as $index) {
            $this->connection->execute('ALTER TABLE ' . $tableName . ' DROP INDEX ' . $index);
        }

        if ($primaryKeys) {
            $this->connection->execute('ALTER TABLE ' . $tableName . ' DROP PRIMARY KEY');
        }

        foreach ($sqls as $sql) {
            $this->connection->execute($sql);
        }

        if ($primaryKeys) {
            $this->connection->execute('ALTER TABLE ' . $tableName . ' ADD PRIMARY KEY (' . implode(', ', $primaryKeys) . ')');
        }

        if ($uniques) {
            $this->connection->execute('ALTER TABLE ' . $tableName . ' ADD UNIQUE (' . implode(', ', $uniques) . ')');
        }

        foreach ($indexesToAdd as $index) {
            $this->addIndexFromFieldName($tableName, $fieldName);
        }

        foreach ($indexes as $index) {
            $this->addIndex($tableName, $index);
        }

        $tableName = $table->getName();
        if (array_key_exists($tableName, $this->tables)) {
            unset($this->tables[$tableName]);
        }
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
                if ($field->isAutoNumbering()) {
                    $sql .= ' AUTO_INCREMENT';
                }

                continue;
            }

            $sql .= ' DEFAULT ' . $this->getDefaultValue($field);
            if ($field->isUnique()) {
                $uniques[] = $fieldName;
            } elseif ($field->isIndexed()) {
                $indexes[] = $fieldName;
            }
        }

        if ($primaryKeys) {
            $sql .= ', PRIMARY KEY (' . implode(', ', $primaryKeys) . ')';
        }

        if ($uniques) {
            $sql .= ', UNIQUE (' . implode(', ', $uniques) . ')';
        }

        $sql = 'CREATE TABLE ' . $tableName . ' (' . $sql . ') ENGINE=INNODB CHARACTER SET utf8';
        $this->connection->execute($sql);

        foreach ($indexes as $fieldName) {
            $this->addIndexFromFieldName($tableName, $fieldName);
        }

        $indexes = $table->getIndexes();
        foreach ($indexes as $index) {
            $this->addIndex($tableName, $index);
        }
    }

    /**
     * Defines the foreign keys for the provided table
     * @param Table $table table definition
     * @return null
     */
    public function defineForeignKeys(Table $table) {
        $tableName = $table->getName();
        $databaseTable = $this->getTable($tableName);

        $foreignKeys = $table->getForeignKeys();
        $foreignKeysToDrop = array();

        foreach ($foreignKeys as $fieldName => $foreignKey) {
            if (!$databaseTable->hasForeignKey($fieldName)) {
                continue;
            }

            $databaseForeignKey = $databaseTable->getForeignKey($fieldName);
            if ($foreignKey->equals($databaseForeignKey)) {
                unset($foreignKeys[$fieldName]);
            } else {
                $foreignKeysToDrop[] = $this->connection->quoteIdentifier($databaseForeignKey->getName());
            }
        }

        $tableName = $this->connection->quoteIdentifier($tableName);

        foreach ($foreignKeysToDrop as $foreignKey) {
            $this->connection->execute('ALTER TABLE ' . $tableName . ' DROP FOREIGN KEY ' . $foreignKey);
        }

        foreach ($foreignKeys as $foreignKey) {
            $name = $this->connection->quoteIdentifier($foreignKey->getName());
            $fieldName = $this->connection->quoteIdentifier($foreignKey->getFieldName());
            $referenceTableName = $this->connection->quoteIdentifier($foreignKey->getReferenceTableName());
            $referenceFieldName = $this->connection->quoteIdentifier($foreignKey->getReferenceFieldName());

            $this->connection->execute('ALTER TABLE ' . $tableName .' ADD CONSTRAINT ' . $name . ' FOREIGN KEY (' . $fieldName . ') REFERENCES ' . $referenceTableName . ' (' . $referenceFieldName . ') ON DELETE SET NULL ON UPDATE NO ACTION');
        }
    }

    /**
     * Adds a index for a field to the provided table
     * @param string $tableName Quoted name of the table
     * @param string $fieldName Quoted name of the field to index
     * @return null
     */
    private function addIndexFromFieldName($tableName, $fieldName) {
        $sql = 'ALTER TABLE ' . $tableName . ' ADD INDEX (' . $fieldName . ')';

        $this->connection->execute($sql);
    }

    /**
     * Adds a index to the provided table
     * @param string $tableName Quoted name of the table
     * @param zibo\library\database\definition\Index $index Index to add
     * @return null
     */
    private function addIndex($tableName, Index $index) {
        $fields = $index->getFields();
        foreach ($fields as $fieldName => $field) {
            $fields[$fieldName] = $this->connection->quoteIdentifier($fieldName);
        }

        $sql = 'ALTER TABLE ' . $tableName . ' ADD INDEX ' . $this->connection->quoteIdentifier($index->getName()) . ' (';
        $sql .= implode(', ', $fields) . ')';

        $this->connection->execute($sql);
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

    /**
     * Converts all tables in this database to UTF8
     * @return null
     */
    public function convertDatabaseToUTF8() {
        $tables = $this->getTableList();

        $transactionStarted = $this->connection->startTransaction();
        try {
            foreach ($tables as $table) {
                $this->convertTableToUTF8($table);
            }

            if ($transactionStarted) {
                $this->connection->commitTransaction();
            }
        } catch (Exception $exception) {
            if ($transactionStarted) {
                $this->connection->rollbackTransaction();
            }

            throw $exception;
        }
    }

    /**
     * Converts the provided table to UTF8
     * @param string $name Name of the table
     * @return null
     */
    public function convertTableToUTF8($name) {
        $tableName = $this->connection->quoteIdentifier($name);

        $sql = 'SHOW FIELDS FROM ' . $tableName;
        $result = $this->connection->execute($sql);

        if (!$result) {
            throw new MysqlException('Could not find any fields for table ' . $name . '. Does it exist?');
        }

        // generate the SQL scripts

        $sqlToBinary = array();
        $sqlFromBinary = array();

        foreach ($result as $data) {
            $isVarchar = false;
            if (strpos($data['Type'], 'varchar') !== false) {
                $isVarchar = true;
            } elseif (strpos($data['Type'], 'text') === false) {
                continue;
            }

            $sql = 'ALTER TABLE ' . $tableName . ' MODIFY ' . $this->connection->quoteIdentifier($data['Field']) . ' ';
            $sqlNull = $data['Null'] == 'YES' ? ' NULL' : ' NOT NULL';

            if ($isVarchar) {
                $sqlToBinary[] = $sql . str_replace('varchar', 'varbinary', $data['Type']) . $sqlNull;
            } else {
                $sqlToBinary[] = $sql . 'longblob' . $sqlNull;
            }

            $sqlFromBinary[] = $sql . $data['Type'] . ' CHARACTER SET utf8' . $sqlNull;
        }

        if (!$sqlFromBinary) {
            return;
        }

        // execute the SQL scripts in a transaction
        $transactionStarted = $this->connection->startTransaction();
        try {
            foreach ($sqlToBinary as $sql) {
                $this->connection->execute($sql);
            }

            $this->connection->execute('ALTER TABLE ' . $tableName . ' CHARSET utf8');

            foreach ($sqlFromBinary as $sql) {
                $this->connection->execute($sql);
            }

            if ($transactionStarted) {
                $this->connection->commitTransaction();
            }
        } catch (Exception $exception) {
            if ($transactionStarted) {
                $this->connection->rollbackTransaction();
            }

            throw $exception;
        }
    }

}