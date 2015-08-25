<?php

namespace zibo\library\encryption\hash;

/**
 * Plain text implementation of the hash algorithm
 */
class PlainHash implements Hash {

    /**
     * Hashes the provided string
     * @param string $string String to hash
     * @return string The provided string untouched
     */
    public function hash($string) {
        return $string;
    }

}