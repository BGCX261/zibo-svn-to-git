<?php

namespace zibo\library\encryption\hash;

/**
 * MD5 implementation of the hash algorithm
 */
class Md5HashAlgorithm implements HashAlgorithm {

    /**
     * Hashes the provided string using MD5
     * @param string $string String to hash
     * @return string MD5 of the provided string
     */
    public function hashString($string) {
        return md5($string);
    }

}