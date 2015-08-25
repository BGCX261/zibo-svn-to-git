<?php

namespace zibo\library\database\manipulation;

use zibo\library\database\driver\Driver;
use zibo\library\database\exception\DatabaseException;
use zibo\library\database\manipulation\condition\Condition;
use zibo\library\database\manipulation\condition\NestedCondition;
use zibo\library\database\manipulation\condition\SimpleCondition;
use zibo\library\database\manipulation\condition\SqlCondition;
use zibo\library\database\manipulation\expression\AliasExpression;
use zibo\library\database\manipulation\expression\CaseExpression;
use zibo\library\database\manipulation\expression\Expression;
use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\FunctionExpression;
use zibo\library\database\manipulation\expression\LimitExpression;
use zibo\library\database\manipulation\expression\MathematicalExpression;
use zibo\library\database\manipulation\expression\OrderExpression;
use zibo\library\database\manipulation\expression\ScalarExpression;
use zibo\library\database\manipulation\expression\SubqueryExpression;
use zibo\library\database\manipulation\expression\SqlExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\statement\DeleteStatement;
use zibo\library\database\manipulation\statement\InsertStatement;
use zibo\library\database\manipulation\statement\SelectStatement;
use zibo\library\database\manipulation\statement\UpdateStatement;
use zibo\library\database\manipulation\statement\Statement;

/**
 * Generic implementation of StatementParser
 */
class GenericStatementParser implements StatementParser {

    /**
     * Instance of the driver for the connection which is using this parser
     * @var zibo\łibrary\database\driver\AbstractDriver
     */
    protected $connection;

    /**
     * Construct a new statement parser
     * @param zibo\library\database\driver\AbstractDriver $connection
     * @param ExpressionParser $expressionParser
     * @return null
     */
    public function __construct(Driver $connection) {
        $this->connection = $connection;
    }

    /**
     * Get the SQL of a statement
     * @param Statement $statement statement to translate into sql
     * @return string SQL of the statement
     * @throws zibo\library\database\exception\DatabaseException when the statement is not supported by this parser
     */
    public function parseStatement(Statement $statement) {
        if ($statement instanceof SelectStatement) {
            return $this->parseSelectStatement($statement);
        }
        if ($statement instanceof InsertStatement) {
            return $this->parseInsertStatement($statement);
        }
        if ($statement instanceof UpdateStatement) {
            return $this->parseUpdateStatement($statement);
        }
        if ($statement instanceof DeleteStatement) {
            return $this->parseDeleteStatement($statement);
        }

        throw new DatabaseException('Unsupported statement ' . get_class($statement));
    }

    /**
     * Get the SQL of a delete statement
     * @param DeleteStatement $statement
     * @return string SQL of the delete statement
     * @throws zibo\library\database\exception\DatabaseException when no table was added to the statement
     */
    protected function parseDeleteStatement(DeleteStatement $statement) {
        $tables = $statement->getTables();
        if (empty($tables)) {
            throw new DatabaseException('No tables added to the insert statement');
        }
        $table = array_shift($tables);

        $sql = 'DELETE FROM ' . $this->connection->quoteIdentifier($table->getName());

        $conditions = $statement->getConditions();
        if ($conditions) {
            $operator = $statement->getOperator();
            $sql .= ' WHERE ' . $this->parseConditions($conditions, $operator, false);
        }

        return $sql;
    }

    /**
     * Get the SQL of a insert statement
     * @param zibo\library\database\manipulation\statement\manipulation\InsertStatement $statement
     * @return string SQL of the insert statement
     * @throws zibo\library\database\exception\DatabaseException when no table was added to the statement
     * @throws zibo\library\database\exception\DatabaseException when no values where added to the statement
     */
    protected function parseInsertStatement(InsertStatement $statement) {
        $tables = $statement->getTables();
        if (empty($tables)) {
            throw new DatabaseException('No tables added to the insert statement');
        }
        $table = array_shift($tables);

        $values = $statement->getValues();
        if (empty($values)) {
            throw new DatabaseException('No values added to the insert statement');
        }

        $sqlFields = '';
        $sqlValues = '';
        foreach ($values as $valueExpression) {
            $field = $valueExpression->getField();
            $value = $valueExpression->getValue();

            $value = $this->parseExpression($value);

            $sqlFields .= ($sqlFields == '' ? '' : ', ') . $this->connection->quoteIdentifier($field->getName());
            $sqlValues .= ($sqlValues == '' ? '' : ', ') . $value;
        }

        $sql = 'INSERT INTO ' . $this->connection->quoteIdentifier($table->getName());
        $sql .= ' (' . $sqlFields . ') VALUES (' . $sqlValues . ')';

        return $sql;
    }

    /**
     * Get the SQL for a select statement
     * @param zibo\library\database\manipulation\statement\manipulation\SelectStatement $statement
     * @return string SQL of the select statement
     */
    protected function parseSelectStatement(SelectStatement $statement) {
        $operator = $statement->getOperator();

        $sql = 'SELECT ';
        if ($statement->isDistinct()) {
            $sql .= 'DISTINCT ';
        }

        $fields = $statement->getFields();
        if (empty($fields)) {
            $sql . '*';
        } else {
            $sql .= $this->parseExpressionsForSelect($fields);
        }

        $tables = $statement->getTables();
        if ($tables) {
            $sql .= ' FROM ' . $this->parseTableExpressionsForFrom($tables);
        }

        $conditions = $statement->getConditions();
        if ($conditions) {
            $sql .= ' WHERE ' . $this->parseConditions($conditions, $operator, false);
        }

        $group = $statement->getGroupBy();
        if ($group) {
            $sql .= ' GROUP BY ' . $this->parseExpressions($group, ', ', true);
        }

        $having = $statement->getHaving();
        if ($having) {
            $sql .= ' HAVING ' . $this->parseConditions($having, $operator, true);
        }

        $order = $statement->getOrderBy();
        if ($order) {
            $sql .= ' ORDER BY ' . $this->parseExpressions($order, ', ', false);
        }

        $limit = $statement->getLimit();
        if ($limit) {
            $sql .= $this->parseLimitExpression($limit);
        }

        return $sql;
    }

    /**
     * Get the SQL of a update statement
     * @param zibo\library\database\manipulation\statement\manipulation\UpdateStatement $statement
     * @return string SQL of the update statement
     * @throws zibo\library\database\exception\DatabaseException when no table was added to the statement
     * @throws zibo\library\database\exception\DatabaseException when no values where added to the statement
     */
    protected function parseUpdateStatement(UpdateStatement $statement) {
        $tables = $statement->getTables();
        if (empty($tables)) {
            throw new DatabaseException('No tables added to the update statement');
        }
        $table = array_shift($tables);

        $values = $statement->getValues();
        if (empty($values)) {
            throw new DatabaseException('No values added to the update statement');
        }

        $sql = '';

        foreach ($values as $valueExpression) {
            $field = $valueExpression->getField();
            $value = $valueExpression->getValue();

            $value = $this->parseExpression($value);

            $sql .= ($sql ? ', ' : '') . $this->connection->quoteIdentifier($field->getName()) . ' = ' . $value;
        }
        $sql = 'UPDATE ' . $this->connection->quoteIdentifier($table->getName()) . ' SET ' . $sql;

        $conditions = $statement->getConditions();
        if ($conditions) {
            $operator = $statement->getOperator();
            $sql .= ' WHERE ' . $this->parseConditions($conditions, $operator, false);
        }

        return $sql;
    }

    /**
     * Create the SQL of an array of conditions
     * @param array conditions array with Condition objects
     * @param string $operator logical operator between the conditions
     * @param boolean useAlias
     * @return string SQL of the array of conditions
     */
    protected function parseConditions(array $conditions, $operator, $useAlias = true) {
        $sql = '';

        foreach ($conditions as $condition) {
            if ($sql) {
                $sql . ' ' . $operator . ' ';
            }
            $sql .= $this->parseCondition($condition, $useAlias);
        }

        return $sql;
    }

    /**
     * Get the SQL of a condition
     * @param zibo\library\database\manipulation\expression\condition\Condition $condition condition to translate into SQL
     * @return string SQL of the condition
     * @throws zibo\library\database\exception\DatabaseException when the condition is not supported by this parser
     */
    protected function parseCondition(Condition $condition, $useAlias = true) {
        if ($condition instanceof SimpleCondition) {
            return $this->parseSimpleCondition($condition, $useAlias);
        }
        if ($condition instanceof NestedCondition) {
            return $this->parseNestedCondition($condition, $useAlias);
        }
        if ($condition instanceof SqlCondition) {
            return $condition->getSql();
        }

        throw new DatabaseException('Unsupported condition ' . get_class($expression));
    }

    /**
     * Create the SQL of a nested condition
     * @param zibo\library\database\manipulation\expression\condition\NestedCondition $condition
     * @param boolean useAlias
     * @return string sql of the nested condition
     */
    protected function parseNestedCondition(NestedCondition $condition, $useAlias = true) {
        $parts = $condition->getParts();

        $sql = '';
        foreach ($parts as $part) {
            if ($sql) {
                $sql .= ' ' . $part->getOperator() . ' ';
            }
            $sql .= $this->parseCondition($part->getCondition(), $useAlias);
        }

        return '(' . $sql . ')';
    }

    /**
     * Create the SQL of a simple condition
     * @param zibo\library\database\manipulation\expression\condition\SimpleCondition $condition
     * @param boolean useAlias
     * @return string sql part of the simple condition
     */
    protected function parseSimpleCondition(SimpleCondition $condition, $useAlias = true) {
        $expressionLeft = $condition->getLeftExpression();
        $expressionRight = $condition->getRightExpression();
        $operator = $condition->getOperator();

        return $this->parseExpression($expressionLeft, $useAlias) . ' ' . $operator . ' ' . $this->parseExpression($expressionRight, $useAlias);
    }

    /**
     * Get the SQL of an array of expressions
     * @param array $expressions
     * @param string $concat string to put between the expressions
     * @param boolean $useAlias
     * @return string SQL of the expressions
     */
    protected function parseExpressions(array $expressions, $concat = ', ', $useAlias = true) {
        $sql = '';

        foreach ($expressions as $expression) {
            if ($sql) {
                $sql .= $concat;
            }
            $sql .= $this->parseExpression($expression, $useAlias);
        }

        return $sql;
    }

    /**
     * Get the SQL of a expression
     * @param zibo\library\database\manipulation\expression\Expression $expression expression to translate into SQL
     * @param boolean $useAlias get the alias of the expression
     * @return string SQL of the expression
     * @throws zibo\library\database\exception\DatabaseException when the expression is not supported by this parser
     */
    protected function parseExpression(Expression $expression, $useAlias = false) {
        if ($expression instanceof FieldExpression) {
            return $this->parseFieldExpression($expression, $useAlias);
        }
        if ($expression instanceof TableExpression) {
            return $this->parseTableExpression($expression, $useAlias);
        }
        if ($expression instanceof ScalarExpression) {
            return $this->parseScalarExpression($expression, $useAlias);
        }
        if ($expression instanceof FunctionExpression) {
            return $this->parseFunctionExpression($expression, $useAlias);
        }
        if ($expression instanceof OrderExpression) {
            return $this->parseOrderExpression($expression, $useAlias);
        }
        if ($expression instanceof LimitExpression) {
            return $this->parseLimitExpression($expression, $useAlias);
        }
        if ($expression instanceof CaseExpression) {
            return $this->parseCaseExpression($expression, $useAlias);
        }
        if ($expression instanceof MathematicalExpression) {
            return $this->parseMathematicalExpression($expression, $useAlias);
        }
        if ($expression instanceof SubqueryExpression) {
            return $this->parseSelectStatement($expression->getStatement());
        }
        if ($expression instanceof SqlExpression) {
            return $expression->getSql();
        }

        throw new DatabaseException('Unsupported expression ' . get_class($expression));
    }

    /**
     * Get the SQL of expressions for a SELECT expression
     * @param array $expressions Array containing Expression objects
     * @return string SQL of the expressions for a SELECT expression
     */
    protected function parseExpressionsForSelect(array $expressions) {
        $sql = '';

        foreach ($expressions as $expression) {
            $sql .= ($sql ? ', ' : '') . $this->parseExpressionForSelect($expression);
        }

        return $sql;
    }

    /**
     * Get the SQL of a expression for a SELECT expression
     * @param zibo\library\database\manipulation\expression\Expression $expression
     * @return string SQL of the expression for a SELECT expression
     */
    protected function parseExpressionForSelect(Expression $expression) {
        $sql = $this->parseExpression($expression, false);

        if ($expression instanceof AliasExpression) {
            $alias = $expression->getAlias();
            if ($alias) {
                $sql .= ' AS ' . $this->connection->quoteIdentifier($alias);
            }
        }

        return $sql;
    }

    /**
     * Get the quoted name of the given field, ready to query
     * @param Field field field to get the quoted query name of
     * @param boolean useAlias true to use the field's alias if set, false otherwise (default: true)
     * @return string quoted name of the field
     */
    protected function parseFieldExpression(FieldExpression $field, $useAlias = true) {
        $alias = $field->getAlias();
        if ($alias && $useAlias) {
            return $this->connection->quoteIdentifier($alias);
        }

        $name = $this->connection->quoteIdentifier($field->getName());
        $table = $field->getTable();
        if ($table) {
            $name = $this->parseTableExpression($table, true) . '.' . $name;
        }

        return $name;
    }

    /**
     * Create the SQL of a function expression
     * @param zibo\library\database\manipulation\expression\FunctionExpression $function
     * @return string SQL of the function expression
     */
    protected function parseFunctionExpression(FunctionExpression $function) {
        $sql = $function->getName() . '(';

        if ($function->isDistinct()) {
            $sql .= 'DISTINCT ';
        }

        $arguments = $function->getArguments();
        if ($arguments) {
            $argumentSql = '';
            foreach ($arguments as $expression) {
                $argumentSql .= ($argumentSql ? ', ' : '') . $this->parseExpression($expression);
            }

            $sql .= $argumentSql;
        }

        return $sql . ')';
    }

    /**
     * Create the SQL of a mathematical expression
     * @param zibo\library\database\manipulation\expression\MathematicalExpression $expression
     * @param boolean $useAlias
     * @return string SQL of the mathematical expression
     */
    protected function parseMathematicalExpression(MathematicalExpression $expression, $useAlias = true) {
        $parts = $expression->getParts();

        $sql = '';
        foreach ($parts as $part) {
            if ($sql) {
                $sql .= ' ' . $part->getOperator() . ' ';
            }
            $sql .= $this->parseExpression($part->getExpression(), false);
        }

        return '(' . $sql . ')';
    }

    /**
     * Create the SQL of a case expression
     * @param zibo\library\database\manipulation\expression\CaseExpression $case
     * @return string SQL of the case expression
     */
    protected function parseCaseExpression(CaseExpression $case, $useAlias = true) {
        $sql = 'CASE ';

        $when = $case->getWhen();
        foreach ($when as $w) {
            $whenCondition = $this->parseCondition($w->getCondition(), $useAlias);
            $whenExpression = $this->parseExpression($w->getExpression(), $useAlias);

            $sql .= 'WHEN ' . $whenCondition . ' ';
            $sql .= 'THEN ' . $whenExpression . ' ';
        }

        $defaultExpression = $case->getDefaultExpression();
        if ($defaultExpression) {
            $sql .= 'ELSE ' . $this->parseExpression($defaultExpression, $useAlias) . ' ' ;
        }

        $sql = '(' . $sql . 'END)';

        $alias = $case->getAlias();
        if ($useAlias && $alias) {
            $sql .= ' AS ' . $this->connection->quoteIdentifier($alias);
        }

        return $sql;
    }

    /**
     * Get the SQL for the provided scalar expression
     * @param zibo\library\database\manipulation\expression\ScalarExpression $expression
     * @param boolean $useAlias true to use the table's alias if set, false otherwise (default: true)
     * @return string SQL of the scalar expression
     */
    protected function parseScalarExpression(ScalarExpression $expression, $useAlias = true) {
        $value = $expression->getValue();

        if ($value === null) {
            $sql = 'NULL';
        } else {
            $sql = $this->connection->quoteValue($value);
        }

        $alias = $expression->getAlias();
        if ($useAlias && $alias) {
            $sql .= ' AS ' . $this->connection->quoteIdentifier($alias);
        }

        return $sql;
    }

    /**
     * Get the quoted name of the given table, ready to query
     * @param zibo\library\database\manipulation\expression\TableExpression $table
     * @param boolean $useAlias true to use the table's alias if set, false otherwise (default: true)
     * @return string quoted name of the table
     */
    protected function parseTableExpression(TableExpression $table, $useAlias = true) {
        $alias = $table->getAlias();

        if (!$alias || !$useAlias) {
            $alias = $table->getName();
        }

        return $this->connection->quoteIdentifier($alias);
    }

    /**
     * Create the SQL for a FROM expression of a statement
     * @param array $tables Array with TableExpressions
     * @return string SQL to use as a FROM expression
     */
    protected function parseTableExpressionsForFrom(array $tables) {
        $sql = '';

        foreach ($tables as $table) {
            $sql .= ($sql ? ', ' : '');
            $sql .= $this->parseTableExpressionForFrom($table);
            $sql .= $this->parseTableExpressionJoins($table);
        }

        return $sql;
    }

    /**
     * Create the SQL of the given table for a FROM expression
     * @param zibo\library\database\manipulation\expression\TableExpression $table
     * @return string SQL representation of the table
     */
    protected function parseTableExpressionForFrom(TableExpression $table) {
        $tableName = $this->connection->quoteIdentifier($table->getName());
        $tableAlias = $table->getAlias();

        if ($tableAlias != null) {
            $tableName .= ' AS ' . $this->connection->quoteIdentifier($tableAlias);
        }

        return $tableName;
    }

    /**
     * Create the SQL of the joins of a table for a FROM expression
     * @param zibo\library\database\manipulation\expression\TableExpression $table
     * @return string SQL representation of the joins
     */
    protected function parseTableExpressionJoins(TableExpression $table) {
        $sql = '';

        $joins = $table->getJoins();
        if (!$joins) {
            return $sql;
        }

        foreach ($joins as $join) {
            $sql .= ' ' . $join->getType() . ' JOIN ' . $this->parseTableExpressionForFrom($join->getTable()) . ' ON ' . $this->parseCondition($join->getCondition(), false);
        }

        return $sql;
    }

    /**
     * Create the SQL of a order expression
     * @param zibo\library\database\manipulation\expression\OrderExpression $order
     * @return string SQL of the order expression
     */
    protected function parseOrderExpression(OrderExpression $order) {
        return $this->parseExpression($order->getExpression(), true) . ' ' . $order->getDirection();
    }

    /**
     * Create the SQL of a limit expression
     * @param zibo\library\database\manipulation\expression\LimitExpression $expression
     * @return string SQL of the limit expression
     */
    protected function parseLimitExpression(LimitExpression $expression) {
        $rowCount = $expression->getRowCount();
        $offset = $expression->getOffset();

        $sql = ' LIMIT ' . $rowCount;
        if ($offset != null) {
            $sql .= ' OFFSET ' . $offset;
        }

        return $sql;
    }

}