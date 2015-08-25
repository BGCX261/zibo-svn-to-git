<?php

namespace zibo\library\database\manipulation\condition;

/**
 * Nested condition. A condition that can contain multiple other conditions
 */
class NestedCondition extends Condition {

    /**
     * Array with the condition parts
     * @var array
     */
    private $parts = array();

    /**
     * Adds a part to this condition
     * @param Condition $condition Part of the nested condition
     * @param string $operator Logical operator used before this condition when it's not the first part
     * @return null
     */
    public function addCondition(Condition $condition, $operator = null) {
        $part = new NestedConditionPart($condition, $operator);
        $this->parts[] = $part;
    }

    /**
     * Gets the parts of this condition
     * @return array Array with NestedConditionPart objects
     */
    public function getParts() {
        return $this->parts;
    }

}