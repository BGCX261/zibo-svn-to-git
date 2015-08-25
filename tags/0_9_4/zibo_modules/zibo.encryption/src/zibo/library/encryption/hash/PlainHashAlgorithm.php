<?php

namespace zibo\library\encryption\hash;

/**
 * Plain text implementation of the hash algorithm
 */
class PlainHashAlgorithm implements HashAlgorithm {

    /**
     * Hashes the provided string
     * @param string $string String to hash
     * @return string The provided string untouched
     */
    public function hashString($string) {
        return $string;
    }

}