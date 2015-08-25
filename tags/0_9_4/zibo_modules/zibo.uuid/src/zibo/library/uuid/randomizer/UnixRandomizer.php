<?php

namespace zibo\library\uuid\randomizer;

use zibo\library\uuid\exception\UUIDException;

/**
 * Randomizer to read values from a file
 */
class UnixRandomizer implements Randomizer {

    /**
     * The randomizer file node
     * @var string
     */
    const SOURCE = '/dev/urandom';

    /**
     * The handle of the random file
     * @var resource
     */
    private $handle;

    /**
     * Constructs a new UNIX randomizer
     * @return null
     */
    public function __construct() {
        if (!is_readable(self::SOURCE)) {
            throw new UUIDException('Could not create the Unix randomizer: cannot read from ' . self::SOURCE);
        }

        $this->handle = fopen(self::SOURCE, 'rb');
    }

    /**
     * Gets a number of random bytes
     * @param integer $number The number of random bytes
     * @return string The random bytes
     */
    public function getRandomBytes($number) {
        return fread($this->handle, $number);
    }

}