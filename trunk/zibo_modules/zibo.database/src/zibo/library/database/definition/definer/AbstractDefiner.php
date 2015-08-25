<?php

namespace zibo\library\database\definition\definer;

use zibo\library\database\exception\DatabaseException;
use zibo\library\database\definition\Field;
use zibo\library\database\definition\Table;
use zibo\library\database\driver\Driver;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Abstract database/table definer
 */
abstract class AbstractDefiner implements Definer {

    /**
     * Connection of this definer
     * @var zibo\library\database\driver\Driver
     */
    protected $connection;

    /**
     * Constructs a new definer
     * @param zibo\library\database\driver\Driver $connection
     * @return null
     */
    public function __construct(Driver $connection) {
       $this->connection = $connection;
    }

    /**
     * Defines a table in the connection with the given table definition. If the table does not
     * exist, it will be created. If the table structure is different then the definition, it
     * will be altered
     * @param Table $table table definition
     * @return null
     */
    public function defineTable(Table $table) {
        if ($this->tableExists($table->getName())) {
            $this->alterTable($table);
        } else {
            $this->createTable($table);
        }
    }

    /**
     * Alter the table in the connection with the given table definition
     * @param Table $table table definition of the table to alter
     * @return null
     */
    abstract protected function alterTable(Table $table);

    /**
     * Create a new table in the connection
     * @param Table $table table definition of the table to create
     * @return null
     */
    abstract protected function createTable(Table $table);

    /**
     * Drop a table from the connection if it exists
     * @param string $name name of the table to drop
     * @return null
     * @throws zibo\library\database\Exception\DatabaseException when the name is empty or not a string
     */
    public function dropTable($name) {
        $this->validateName($name);

        $sql = 'DROP TABLE IF EXISTS ' . $this->connection->quoteIdentifier($name);

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

        $sql = 'SHOW TABLES LIKE ' . $this->connection->quoteValue($name);

        $result = $this->connection->execute($sql);

        return $result->getRowCount() == 0 ? false : true;
    }

    /**
     * Gets a list of the tables in the database connection
     * @return array Array with table names
     */
    public function getTableList() {
        $sql = 'SHOW TABLES';

        $result = $this->connection->execute($sql);

        $key = 'Tables_in_' . $this->connection->getDsn()->getDatabase();
        $tables = array();
        foreach ($result as $row) {
            $tables[] = $row[$key];
        }

        return $tables;
    }

    /**
     * Gets the predefined types for this definer
     * @return array Array with the name of the predefined type as key and the database type as value
     */
    public function getFieldTypes() {
        return array();
    }

    /**
     * Translates a database layer's field type to a mysql field type
     * @param string|zibo\library\database\definition\Field $field field can be a database layer's type or a Field object
     * @return string mysql field type
     * @throws zibo\library\database\exception\DatabaseException when no type found for the provided field or type
     */
    protected function getFieldType($field) {
        $fieldTypes = $this->getFieldTypes();

        if ($field instanceof Field) {
            $fieldType = $field->getType();
            if (!isset($fieldTypes[$fieldType])) {
                throw new DatabaseException('No database type found for type ' . $fieldType);
            }

            return $fieldTypes[$fieldType];
        }

        $type = array_search($field, $fieldTypes);
        if ($type === false) {
            throw new DatabaseException('No type found for database type ' . $field);
        }

        return $type;
    }

    /**
     * Gets the default value of a field
     * @param Field $field
     * @return string SQL of the default value
     */
    protected function getDefaultValue(Field $field) {
        $default = $field->getDefaultValue();

        if ($default == null || strtoupper($default) == 'NULL') {
            return 'NULL';
        }

        return $this->connection->quoteValue($default);
    }

    /**
     * Checks if a name is a string and not empty
     * @param string $name
     * @return null
     * @throws zibo\library\database\Exception\DatabaseException when the name is empty or not a string
     */
    protected function validateName($name) {
        if (!String::isString($name, String::NOT_NAME)) {
            throw new DatabaseException('Provided name is empty or invalid');
        }
    }

}