<?php

namespace zibo\library\exchange\type;

use \InvalidArgumentException;

/**
 * Abstract implementation of the view for a FindItem operation
 */
abstract class BasePaging {

    /**
     * Describes the maximum number of results to return in the FindItem response.
     * @var integer
     */
    public $MaxEntriesReturned;

    /**
     * Constructs a abstract view element
     * @param integer $maxEntriesReturned Describes the maximum number of results to return in the FindItem response.
     * @return null
     * @throws InvalidArgumentException when the provided maxEntriesReturned is not null or a positive number
     */
    public function __construct($maxEntriesReturned = null) {
        if ($maxEntriesReturned !== null && (!is_numeric($maxEntriesReturned) || $maxEntriesReturned < 1)) {
            throw new InvalidArgumentException();
        }

        $this->MaxEntriesReturned = $maxEntriesReturned;
    }

}