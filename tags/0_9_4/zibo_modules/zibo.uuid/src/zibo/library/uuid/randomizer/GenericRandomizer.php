<?php

namespace zibo\library\uuid\randomizer;

/**
 * Generic randomizer
 */
class GenericRandomizer implements Randomizer {

    /**
     * Gets a number of random bytes
     * @param integer $number The number of random bytes
     * @return string The random bytes
     */
    public function getRandomBytes($number) {
        $result = '';

        for ($i = 0; $i < $number; $i++) {
            $result .= chr(mt_rand(0, 255));
        }

        return $result;
    }

}