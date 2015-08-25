<?php

namespace zibo\library\uuid\randomizer;

use zibo\library\uuid\exception\UUIDException;

use \COM;
use \Exception;

/**
 * Randomizer for Windows using a COM object
 */
class WindowsRandomizer implements Randomizer {

    /**
     * Application ID for the randomizer COM object
     * @var string
     */
    const APPLICATION_ID = 'CAPICOM.Utilities.1';

    /**
     * The COM object for the randomizer
     * @var COM
     */
    private $com;

    /**
     * Constructs a new COM randomizer
     * @return null
     * @throws zibo\ZiboException when the COM object is not available
     */
    public function __construct() {
        if (!class_exists('COM')) {
            throw new UUIDException('Could not construct the Windows randomizer: COM class is not available');
        }

        try {
            $this->com = new COM(self::APPLICATION_ID);
        } catch (Exception $exception) {
            throw new UUIDException('Could not construct the Windows randomizer: ' . $exception->getMessage());
        }
    }

    /**
     * Gets a number of random bytes
     * @param integer $number The number of random bytes
     * @return string The random bytes
     */
    public function getRandomBytes($number) {
        // straight binary mysteriously doesn't work, hence the base64
        return base64_decode($this->com->GetRandom($number, 0));
    }

}