<?php

namespace zibo\library\orm\query\parser;

use zibo\library\database\DatabaseResult;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\model\data\Data;
use zibo\library\orm\model\Model;

use \Exception;

/**
 * Parser to parse database results into orm results
 */
class ResultParser {

    /**
     * Meta definition of the model of the result
     * @var zibo\library\orm\model\ModelMeta
     */
    private $meta;

    /**
     * Empty data object for the model
     * @var mixed
     */
    private $data;

    /**
     * Array with empty data objects for relation models
     * @var array
     */
    private $modelData;

    /**
     * Constructs a new result parser
     * @param zibo\library\orm\model\Model $model Model of this result
     * @return null
     */
    public function __construct(Model $model) {
        $this->meta = $model->getMeta();
        $this->data = $model->createData(false);
    }

    /**
     * Parses a database result into a orm result
     * @param zibo\library\database\DatabaseResult $databaseResult
     * @param string $indexFieldName Name of the field to index the result on
     * @return array Array with data objects
     */
    public function parseResult(DatabaseResult $databaseResult, $indexFieldName = null) {
        $this->modelData = array();

        $result = array();

        if ($indexFieldName === null) {
            $indexFieldName = ModelTable::PRIMARY_KEY;
        }

        foreach ($databaseResult as $row) {
            $data = $this->getDataObjectFromRow($row);

            if ($indexFieldName && isset($data->$indexFieldName)) {
                $result[$data->$indexFieldName] = $data;
            } else {
                $result[] = $data;
            }
        }

        return $result;
    }

    /**
     * Gets a data object from the provided database result row
     * @param array $row Database result row
     * @return array Array with data objects
     */
    private function getDataObjectFromRow($row) {
        $aliasses = array();

        $data = clone($this->data);

        foreach ($row as $column => $value) {
            $positionAliasSeparator = strpos($column, QueryParser::ALIAS_SEPARATOR);
            if ($positionAliasSeparator === false) {
                $data->$column = $value;
                continue;
            }

            $alias = substr($column, 0, $positionAliasSeparator);
            $fieldName = substr($column, $positionAliasSeparator + QueryParser::ALIAS_SEPARATOR_LENGTH);

            if ($alias == QueryParser::ALIAS_SELF) {
                $data->$fieldName = $value;
                continue;
            }

            if (!array_key_exists($alias, $aliasses)) {
                $aliasses[$alias] = $this->createData($alias);
            }

            $aliasses[$alias]->$fieldName = $value;
        }

        foreach ($aliasses as $fieldName => $value) {
            if (!(is_object($value) && !$value->id)) {
                $data->$fieldName = $value;
                continue;
            }

            $containsValues = false;
            foreach ($value as $k => $v) {
                if ($v) {
                    $containsValues = true;
                    break;
                }
            }

            if ($containsValues) {
                $data->$fieldName = $value;
            } else {
                $data->$fieldName = null;
            }
        }

        return $data;
    }

    /**
     * Creates a data object for relation data
     * @param string $fieldName Name of the relation field
     * @return mixed Empty data object
     */
    private function createData($fieldName) {
        try {
            if (!array_key_exists($fieldName, $this->modelData)) {
                $relationModel = $this->meta->getRelationModel($fieldName);
                $this->modelData[$fieldName] = $relationModel->createData(false);
            }

            $data = clone($this->modelData[$fieldName]);
        } catch (Exception $e) {
            $data = new Data();
        }

        return $data;
    }

}