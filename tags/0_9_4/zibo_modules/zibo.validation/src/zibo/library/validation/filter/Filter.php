<?php

namespace zibo\library\validation\filter;

/**
 * Interface for a filter. A filter can pre-process a value and fix input errors automatically.
 */
interface Filter {

    /**
     * Construct a new filter instance
     * @param array $options options for this instance
     * @return null
     */
    public function __construct(array $options = null);

    /**
     * Filters a value
     * @param mixed $value value to filter
     * @return mixed filtered value
     */
    public function filter($value);

}