<?php

namespace zibo\library\orm\query;

use zibo\core\Zibo;

use zibo\library\database\manipulation\statement\SelectStatement;
use zibo\library\filesystem\File;
use zibo\library\orm\cache\QueryCacheObject;
use zibo\library\orm\cache\ResultCacheObject;
use zibo\library\orm\query\parser\QueryParser;
use zibo\library\orm\ModelManager;

class CachedModelQuery extends ModelQuery {

    /**
     * Cache instance
     * @var zibo\library\cache\Cache
     */
    private $cache;

    /**
     * Array with all the variables of this query
     * @var array
     */
    private $variables;

    /**
     * Flag to see if the result should be cached
     * @var boolean
     */
    private $willCacheResult;

    /**
     * Constructs a new cached model query
     * @param string|zibo\library\orm\model\Model $model Name or instance of the model for this query
     * @param string $operator Logical operator for the conditions
     * @return null
     */
    public function __construct($model, $operator = null) {
        parent::__construct($model, $operator);

        $this->variables = array();
        $this->willCacheResult = true;

        $this->cache = ModelManager::getInstance()->getModelCache();
    }

    /**
     * Gets a string representation of this query
     * @return string
     */
    public function __toString() {
        return $this->getQueryId();
    }

    /**
     * Sets whether this query should cache it's result
     * @param boolean $flag True to cache, false otherwise
     * @return null
     */
    public function setWillCacheResult($flag) {
        $this->willCacheResult = $flag;
    }

    /**
     * Gets the SQL of the count query
     * @return string The SQL of the count query
     */
    public function getCountSql() {
        $sql = parent::getCountSql();
        $connection = $this->model->getMeta()->getConnection();

        return $this->parseVariablesIntoSql($sql, $connection);
    }

    /**
     * Gets the SQL of the result query
     * @return string The SQL of the result query
     */
    public function getQuerySql() {
        $sql = parent::getQuerySql();
        $connection = $this->model->getMeta()->getConnection();

        return $this->parseVariablesIntoSql($sql, $connection);
    }

    /**
     * Counts the results for this query
     * @return integer number of rows in this query
     */
    public function count() {
        $queryId = $this->getQueryId('##count##');

        if ($this->willCacheResult) {
            $resultId = $this->getResultId($queryId);

            $cachedResult = $this->cache->getResult($resultId);
            if ($cachedResult) {
                return $cachedResult->getResult();
            }
        }

        $connection = $this->model->getMeta()->getConnection();

        $cachedQuery = $this->cache->getQuery($queryId);
        if (!$cachedQuery) {
            $statement = self::$queryParser->parseQueryForCount($this);

            $statementParser = $connection->getStatementParser();

            $sql = $statementParser->parseStatement($statement);
            $usedModels = $this->getUsedModels($statement);

            $cachedQuery = new QueryCacheObject($sql, $usedModels);

            $this->cache->setQuery($queryId, $cachedQuery);
        } else {
            $sql = $cachedQuery->getSql();
            $usedModels = $cachedQuery->getUsedModels();
        }

        $sql = $this->parseVariablesIntoSql($sql, $connection);

        $result = $connection->execute($sql);

        if ($result->getRowCount()) {
            $row = $result->getFirst();
            $result = $row[QueryParser::ALIAS_COUNT];
        } else {
            $result = 0;
        }

        if ($this->willCacheResult) {
            $cachedResult = new ResultCacheObject($result);

            $this->cache->setResult($resultId, $cachedResult, $usedModels);
        }

        return $result;
    }

    /**
     * Executes this query and returns the result
     * @param boolean $indexField Name of the field to use as key in the resulting array, default is id
     * @return array Array with data objects
     */
    public function query($indexField = null) {
        $queryId = $this->getQueryId($indexField);

        $result = null;

        if ($this->willCacheResult) {
            $resultId = $this->getResultId($queryId);

            $cachedResult = $this->cache->getResult($resultId);
            if ($cachedResult) {
                $result = $cachedResult->getResult();
                $belongsTo = $cachedResult->getBelongsToFields();
                $has = $cachedResult->getHasFields();
            }
        }

        if (!$result) {
            $connection = $this->model->getMeta()->getConnection();

            $cachedQuery = $this->cache->getQuery($queryId);
            if (!$cachedQuery) {
                $statement = self::$queryParser->parseQuery($this);

                $statementParser = $connection->getStatementParser();

                $sql = $statementParser->parseStatement($statement);
                $belongsTo = self::$queryParser->getRecursiveBelongsToFields();
                $has = self::$queryParser->getRecursiveHasFields();
                $usedModels = $this->getUsedModels($statement);

                $cachedQuery = new QueryCacheObject($sql, $usedModels, $belongsTo, $has);

                $this->cache->setQuery($queryId, $cachedQuery);
            } else {
                $sql = $cachedQuery->getSql();
                $belongsTo = $cachedQuery->getBelongsToFields();
                $has = $cachedQuery->getHasFields();
                $usedModels = $cachedQuery->getUsedModels();
            }

            $sql = $this->parseVariablesIntoSql($sql, $connection);

            $result = $connection->execute($sql);

            if ($this->willCacheResult) {
                $cachedResult = new ResultCacheObject($result, $belongsTo, $has);

                $this->cache->setResult($resultId, $cachedResult, $usedModels);
            }
        }

        $result = $this->parseResult($result, $belongsTo, $has, $indexField);

        return $result;
    }

    /**
     * Gets a unique identifier of this query
     * @return string
     */
    public function getQueryId($extra = null) {
        $modelName = $this->model->getName();

        $queryId = $modelName . '-' . $this->operator . '-' . $this->distinct . '-' . $this->recursiveDepth . '-' . $this->getLocale() . '-' . $this->willIncludeUnlocalizedData . '-' . $this->willAddIsLocalizedOrder . ';';

        if ($this->fields) {
            $queryId .= 'F:';
            foreach ($this->fields as $field) {
                $queryId .= $field->getExpression() . ';';
            }
        }

        $queryId .= 'J:';
        foreach ($this->joins as $join) {
            $table = $join->getTable();

            $queryId .= $join->getType() . ' ';
            $queryId .= $table->getModelName() . '-' . $table->getAlias() . ' ON ';
            $queryId .= $join->getCondition()->getExpression() . ';';
        }

        $queryId .= 'C:';
        foreach ($this->conditions as $condition) {
            $queryId .= $condition->getExpression() . ';';
        }

        $queryId .= 'G:';
        foreach ($this->groupBy as $groupBy) {
            $queryId .= $groupBy->getExpression() . ';';
        }

        $queryId .= 'H:';
        foreach ($this->having as $condition) {
            $queryId .= $condition->getExpression() . ';';
        }

        $queryId .= 'O:';
        foreach ($this->orderBy as $orderBy) {
            $queryId .= $orderBy->getExpression() . ';';
        }

        $queryId .= 'L:' . $this->limitCount . '-' . $this->limitOffset . ';' . $extra;

        $queryId = $modelName . '-' . md5($queryId);

        return $queryId;
    }

    /**
     * Gets a unique identifier of this query with these variables
     * @param string $queryId Unique identifier of the query
     * @return string
     */
    public function getResultId($queryId) {
        $variableString = '';

        foreach ($this->variables as $key => $value) {
            $variableString .= $key . ': ' . $value .  ';';
        }

        return $queryId . '-' . md5($variableString);
    }

    /**
     * Gets the names of the models used by this query
     * @param zibo\library\database\manipulation\statement\SelectStatement $statemenet
     * @return array Array with the names of the models used by this query
     */
    private function getUsedModels(SelectStatement $statement) {
        $usedModels = array();

        $tables = $statement->getTables();
        foreach ($tables as $table) {
            $usedModels[] = $table->getName();

            $joins = $table->getJoins();
            foreach ($joins as $join) {
                $modelName = $join->getTable()->getName();
                $usedModels[$modelName] = $modelName;
            }
        }

        return $usedModels;
    }

    /**
     * Add the provided fields to this query with named variables.
     * @param string $expression Field expression
     * @param array $variables Array with the variable name as key and the variable as value
     * @return null
     */
    public function addFieldsWithVariables($expression, array $variables) {
        $expression = $this->parseVariables($expression, $variables);

        parent::addFieldsWithVariables($expression, array());
    }

    /**
     * Adds a join to this query.
     * @param string $type Type of the join
     * @param string $modelName Name of the model to join with
     * @param string $alias Alias for the model to join with
     * @param string $condition Condition string for the join
     * @param array $variables Array with the variable name as key and the variable as value
     * @return null
     */
    public function addJoinWithVariables($type, $modelName, $alias, $condition, array $variables) {
        $condition = $this->parseVariables($condition, $variables);

        parent::addJoinWithVariables($type, $modelName, $alias, $condition, array());
    }

    /**
     * Adds a condition to this query with named variables.
     * @param string $condition Condition string
     * @param array $variables Array with the variable name as key and the variable as value
     * @return null
     */
    public function addConditionWithVariables($condition, array $variables) {
        $condition = $this->parseVariables($condition, $variables);

        parent::addConditionWithVariables($condition, array());
    }

    /**
     * Adds a group by to this query with named variables
     * @param string $expression Group by expression
     * @param array $variables Array with the variable name as key and the variable as value
     * @return null
     */
    public function addGroupByWithVariables($expression, array $variables) {
        $expression = $this->parseVariables($expression, $variables);

        parent::addGroupByWithVariables($expression, array());
    }

    /**
     * Adds a having condition to this query with named variables.
     * @param string $condition Condition string
     * @param array $variables Array with the variable name as key and the variable as value
     * @return null
     */
    public function addHavingWithVariables($condition, array $variables) {
        $condition = $this->parseVariables($condition, $variables);

        parent::addHavingWithVariables($condition, array());
    }


    /**
     * Adds a order by to this query with named variables
     * @param string $expression Order by expression
     * @param array $variables Array with the variable name as key and the variable as value
     * @return null
     */
    public function addOrderByWithVariables($expression, array $variables) {
        $expression = $this->parseVariables($expression, $variables);

        parent::addOrderByWithVariables($expression, array());
    }

    /**
     * Makes sure the variables used in the expression are unique over the complete query and stores the
     * variables in this query.
     * @param string $expression String of a expression
     * @param array $variables Array with the variables used in the condition
     * @return string String of the expression with unique variables
     */
    private function parseVariables($expression, $variables) {
        foreach ($variables as $variable => $value) {
            $newVariable = (count($this->variables) + 1) . '_' . $variable;

            $expression = str_replace('%' . $variable . '%', '%' . $newVariable . '%', $expression);

            $this->variables[$newVariable] = $value;
        }

        return $expression;
    }

    /**
     * Parsed the variables into the provided SQL
     * @param string $sql The SQL
     * @param zibo\library\database\driver\Driver $connection Database connection to quote the values
     * @return string The SQL with the parsed variables
     */
    private function parseVariablesIntoSql($sql, $connection) {
        $connection = $this->model->getMeta()->getConnection();
        $statementParser = $connection->getStatementParser();

        foreach ($this->variables as $variable => $value) {
            if ($value instanceof ModelQuery) {
                $statement = self::$queryParser->parseQuery($value);
                $value = $statementParser->parseStatement($statement);

                $sql = str_replace('%' . $variable . '%', '(' . $value . ')', $sql);
            } elseif (is_array($value)) {
                foreach ($value as $k => $v) {
                    $value[$k] = $connection->quoteValue($v);
                }

                $sql = str_replace('%' . $variable . '%', '(' . implode(', ', $value) . ')', $sql);
            } else {
                $sql = str_replace('%' . $variable . '%', $connection->quoteValue($value), $sql);
            }
        }

        return $sql;
    }

}