<?php

namespace zibo\library\orm\query\parser;

use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\FunctionExpression;
use zibo\library\database\manipulation\expression\MathematicalExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\expression\ScalarExpression;
use zibo\library\database\manipulation\expression\SqlExpression;
use zibo\library\orm\query\tokenizer\FieldTokenizer;
use zibo\library\orm\query\tokenizer\MathematicTokenizer;

use zibo\test\BaseTestCase;

class ExpressionParserTest extends BaseTestCase {

    /**
     * @var Parser
     */
    private $parser;

    protected function setUp() {
        $this->parser = new ExpressionParser();
    }

    /**
     * @dataProvider providerParseExpression
     */
    public function testParseExpression($expected, $value) {
        $result = $this->parser->parseExpression($value);

        $this->assertEquals($expected, $result);
    }

    public function providerParseExpression() {
        $field = new FieldExpression('field');

        $minFieldFunction = new FunctionExpression(FunctionExpression::FUNCTION_MIN, 'mf');
        $minFieldFunction->addArgument($field);

        $simpleMathematic = new MathematicalExpression();
        $simpleMathematic->addExpression(new ScalarExpression(5));
        $simpleMathematic->addExpression(new ScalarExpression(10), '+');

        $mediumMathematic = new MathematicalExpression();
        $mediumMathematic->addExpression(new ScalarExpression(5));
        $mediumMathematic->addExpression($simpleMathematic, '/');

        return array(
            array($field, '{field}'),
            array(new FieldExpression('field', null, 'f'), '{field} AS f'),
            array(new FieldExpression('field', new TableExpression('table')), '{table.field}'),
            array(new FieldExpression('field', new TableExpression('table'), 'tf'), '{table.field} AS tf'),
            array($minFieldFunction, 'MIN({field}) AS mf'),
            array(new ScalarExpression('1'), '1'),
            array(new ScalarExpression('a string'), '"a string"'),
            array(new SqlExpression('%1%'), '%1%'),
            array($simpleMathematic, '5 + 10'),
            array($mediumMathematic, '5 / (5 + 10)'),
        );
    }

    /**
     * @dataProvider providerParseExpressionWithVariables
     */
    public function testParseExpressionWithVariables($expected, $value, $variables) {
        $this->parser->setVariables($variables);

        $result = $this->parser->parseExpression($value);

        $this->assertEquals($expected, $result);
    }

    public function providerParseExpressionWithVariables() {
        $variables = array(1 => 'test', 2 => 'test2', 3 => 5, 4 => 10);

        $field = new FieldExpression('field');
        $variable = new ScalarExpression('test');
        $variable2 = new ScalarExpression('test2');

        $maxFieldFunction = new FunctionExpression(FunctionExpression::FUNCTION_MAX);
        $maxFieldFunction->addArgument($variable2);

        $minFieldFunction = new FunctionExpression(FunctionExpression::FUNCTION_MIN, 'mf');
        $minFieldFunction->addArgument($field);
        $minFieldFunction->addArgument($variable);

        $minFieldFunction2 = new FunctionExpression(FunctionExpression::FUNCTION_MIN, 'mf');
        $minFieldFunction2->addArgument($field);
        $minFieldFunction2->addArgument($variable);
        $minFieldFunction2->addArgument($maxFieldFunction);

        $simpleMathematic = new MathematicalExpression();
        $simpleMathematic->addExpression(new ScalarExpression(5));
        $simpleMathematic->addExpression(new ScalarExpression(10), '+');

        $mediumMathematic = new MathematicalExpression();
        $mediumMathematic->addExpression(new ScalarExpression(5));
        $mediumMathematic->addExpression($simpleMathematic, '/');

        $minFunction = new FunctionExpression(FunctionExpression::FUNCTION_MIN);
        $minFunction->addArgument(new ScalarExpression(5));

        $maxFunction = new FunctionExpression(FunctionExpression::FUNCTION_MAX);
        $maxFunction->addArgument($simpleMathematic);

        $functionMathematic = new MathematicalExpression();
        $functionMathematic->addExpression($minFunction);
        $functionMathematic->addExpression($maxFunction);

        return array(
            array(new ScalarExpression('This is a test sentence'), '"This is a %1% sentence"', $variables),
            array(new ScalarExpression('test'), '%1%', $variables),
            array($maxFieldFunction, 'MAX(%2%)', $variables),
            array($minFieldFunction, 'MIN({field}, %1%) AS mf', $variables),
            array($minFieldFunction2, 'MIN({field}, %1%, MAX(%2%)) AS mf', $variables),
            array($simpleMathematic, '%3% + %4%', $variables),
            array($mediumMathematic, '%3% / (%3% + %4%)', $variables),
            array($functionMathematic, 'MIN(%3%) + MAX(%3% + %4%)', $variables),
        );
    }
}