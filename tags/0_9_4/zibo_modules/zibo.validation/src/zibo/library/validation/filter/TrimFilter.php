<?php

namespace zibo\library\validation\filter;

/**
 * Filter to trim scalar values
 */
class TrimFilter extends AbstractFilter {

    /**
     * Trims a scalar value
     * @param mixed $value value to trim
     * @return mixed processed value
     */
    public function filter($value) {
        if (is_scalar($value) && !is_bool($value)) {
            $value = trim($value);
        }

        return $value;
    }

}