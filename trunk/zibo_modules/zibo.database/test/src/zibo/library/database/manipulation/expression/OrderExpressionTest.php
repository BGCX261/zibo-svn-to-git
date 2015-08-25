<?php

namespace zibo\library\database\manipulation\expression;

use zibo\test\BaseTestCase;

class OrderExpressionTest extends BaseTestCase {

    public function testConstruct() {
        $field = new FieldExpression('test');

        $order = new OrderExpression($field);

        $this->assertEquals($field, $order->getExpression());
        $this->assertEquals(OrderExpression::DIRECTION_ASC, $order->getDirection());
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenEmptyDirectionPassed() {
        new OrderExpression(new FieldExpression('field'), '');
    }

    /**
     * @expectedException zibo\library\database\exception\DatabaseException
     */
    public function testConstructThrowsExceptionWhenInvalidDirectionPassed() {
        new OrderExpression(new FieldExpression('field'), 'test');
    }

}