<?php

namespace zibo\library\encryption\hash;

/**
 * Sha1 implementation of the hash algorithm
 */
class Sha1Hash implements Hash {

    /**
     * Hashes the provided string using SHA1
     * @param string $string String to hash
     * @return string SHA1 value of the provided string
     */
    public function hash($string) {
        return sha1($string);
    }

}