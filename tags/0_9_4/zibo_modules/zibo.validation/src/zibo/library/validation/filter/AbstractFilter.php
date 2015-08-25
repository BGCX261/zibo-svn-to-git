<?php

namespace zibo\library\validation\filter;

/**
 * Abstract implementation of a filter
 */
abstract class AbstractFilter implements Filter {

    /**
     * Construct a new filter instance
     * @param array $options options for this instance
     * @return null
     */
    public function __construct(array $options = null) {

    }

}