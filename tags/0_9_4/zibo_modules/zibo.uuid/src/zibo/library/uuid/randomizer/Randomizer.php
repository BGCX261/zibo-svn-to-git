<?php

namespace zibo\library\uuid\randomizer;

/**
 * Interface for a randomizer
 */
interface Randomizer {

    /**
     * Gets a number of random bytes
     * @param integer $number The number of random bytes
     * @return string The random bytes
     */
    public function getRandomBytes($number);

}