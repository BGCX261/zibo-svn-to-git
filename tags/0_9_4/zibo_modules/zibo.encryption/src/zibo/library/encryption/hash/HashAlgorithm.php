<?php

namespace zibo\library\encryption\hash;

/**
 * Interface for cryptographic hash algorithms. These are methods which take an arbitrary block of data and return a fixed-size bit string
 */
interface HashAlgorithm {

    /**
     * Hashes the provided string
     * @param string $string String to hash
     * @return string Hash value
     */
    public function hashString($string);

}