<?php

namespace zibo\library\orm\query\parser;

use zibo\library\database\manipulation\condition\NestedCondition;
use zibo\library\database\manipulation\condition\SimpleCondition;
use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\expression\OrderExpression;
use zibo\library\orm\query\tokenizer\FieldTokenizer;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class QueryParserTest extends BaseTestCase {

    public function testConstruct() {
        $parser = new QueryParser();
        $this->assertNotNull(Reflection::getProperty($parser, 'fieldTokenizer'));
        $this->assertNotNull(Reflection::getProperty($parser, 'expressionParser'));
    }

//
//	/**
//	 * @var Parser
//	 */
//	private $parser;
//
//	protected function setUp() {
//		$this->parser = new QueryParser(new FieldTokenizer());
//	}
//
//	public function testParseOrderExpression() {
//		$tests = array (
//            array(
//                'value' => '{field} desc',
//                'expectedFieldExpression' => new FieldExpression('field'),
//                'expectedType' => OrderExpression::DIRECTION_DESC,
//            ),
//            array(
//                'value' => '{field}',
//                'expectedFieldExpression' => new FieldExpression('field'),
//                'expectedType' => OrderExpression::DIRECTION_ASC,
//            ),
//            array(
//                'value' => '{field.relationFieldExpression}',
//                'expectedFieldExpression' => new FieldExpression('relationFieldExpression', new TableExpression('field')),
//                'expectedType' => OrderExpression::DIRECTION_ASC,
//            ),
//        );
//
//		foreach ($tests as $test) {
//	        $order = $this->parser->parseOrderExpression($test['value']);
//	        $this->assertNotNull($order, 'order  is null');
//	        $this->assertTrue($order instanceof OrderExpression);
//	        $this->assertEquals($test['expectedFieldExpression'], $order->getFieldExpression(), $test['value'] . ' gave not the expected field');
//	        $this->assertEquals($test['expectedType'], $order->getDirection(), $test['value'] . ' gave not the expected type');
//		}
//	}
//
//    public function testParseCondition() {
//        $condition = $this->parser->parseCondition('{field} = %1%', 'test');
//        $expectedOperator = '=';
//        $expectedValues = array(new FieldExpression('field'), 'test');
//
//        $this->assertNotNull($condition, 'condition is null');
//        $this->assertTrue($condition instanceof SimpleCondition);
//        $this->assertEquals($expectedOperator, $condition->getOperator(), 'wrong operator returned, expected =');
//        $this->assertEquals($expectedValues, $condition->getValues(), 'wrong fields and values in the condition');
//    }
//
//    public function testParseConditionOperatorInFieldExpressionName() {
//        $nestedCondition = $this->parser->parseCondition('{fork} = %1% AND {fork} = %1% OR {fork} = %1%', 'test');
//
//        $condition = new SimpleCondition('=');
//        $condition->addValue(new FieldExpression('fork'));
//        $condition->addValue('test');
//
//        $expectedCondition = new NestedCondition('OR');
//
//        $expectedNestedCondition = new NestedCondition('AND');
//        $expectedNestedCondition->addCondition($condition);
//        $expectedNestedCondition->addCondition($condition);
//        $expectedCondition->addCondition($expectedNestedCondition);
//        $expectedCondition->addCondition($condition);
//
//        $this->assertEquals($expectedCondition, $nestedCondition, 'wrong fields and values in the condition');
//    }
//
//    public function testParseConditionWithDifferentConditionOperators() {
//        $nestedCondition = $this->parser->parseCondition('{field} = %1% AND {field} = %1% OR {field} = %1%', 'test');
//
//        $condition = new SimpleCondition('=');
//        $condition->addValue(new FieldExpression('field'));
//        $condition->addValue('test');
//
//        $expectedCondition = new NestedCondition('OR');
//
//        $expectedNestedCondition = new NestedCondition('AND');
//        $expectedNestedCondition->addCondition($condition);
//        $expectedNestedCondition->addCondition($condition);
//        $expectedCondition->addCondition($expectedNestedCondition);
//        $expectedCondition->addCondition($condition);
//
//        $this->assertEquals($expectedCondition, $nestedCondition, 'wrong fields and values in the condition');
//    }
//
//    public function testParseConditionWithMultipleConditionOperators() {
//        $nestedCondition = $this->parser->parseCondition('{field} = %1% OR {field} = %1% OR {field} = %1%', 'test');
//
//        $condition = new SimpleCondition('=');
//        $condition->addValue(new FieldExpression('field'));
//        $condition->addValue('test');
//
//        $expectedCondition = new NestedCondition('OR');
//        $expectedCondition->addCondition($condition);
//        $expectedCondition->addCondition($condition);
//        $expectedCondition->addCondition($condition);
//
//        $this->assertEquals($expectedCondition, $nestedCondition, 'wrong fields and values in the condition');
//    }
//
//    public function testParseConditionWithNestedCondition() {
//    	$firstCondition = new SimpleCondition('=');
//    	$firstCondition->addValue(new FieldExpression('field'));
//    	$firstCondition->addValue(5);
//
//    	$secondCondition = new SimpleCondition('<=');
//    	$secondCondition->addValue(new FieldExpression('field2'));
//    	$secondCondition->addValue('value1');
//
//    	$thirdCondition = new SimpleCondition('>=');
//    	$thirdCondition->addValue(new FieldExpression('field2'));
//    	$thirdCondition->addValue('value2');
//
//    	$secondAndThirdCondition = new NestedCondition('OR');
//    	$secondAndThirdCondition->addCondition($secondCondition);
//    	$secondAndThirdCondition->addCondition($thirdCondition);
//
//        $expectedCondition = new NestedCondition('AND');
//        $expectedCondition->addCondition($firstCondition);
//        $expectedCondition->addCondition($secondAndThirdCondition);
//
//        $condition = $this->parser->parseCondition('{field} = 5 AND (({field2} <= %1%) OR ({field2} >= %2%))', 'value1', 'value2');
//
//        $this->assertNotNull($condition, 'condition is null');
//        $this->assertTrue($condition instanceof NestedCondition);
//        $this->assertEquals($expectedCondition, $condition);
//    }

}