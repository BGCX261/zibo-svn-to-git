<?php

/*
   Based on DrUUID RFC4122 library for PHP5 by J. King (http://jkingweb.ca/)
   Licensed under MIT license

   See http://jkingweb.ca/code/php/lib.uuid/
*/

namespace zibo\library\uuid;

use zibo\library\uuid\exception\UUIDException;
use zibo\library\uuid\randomizer\GenericRandomizer;
use zibo\library\uuid\randomizer\UnixRandomizer;
use zibo\library\uuid\randomizer\WindowsRandomizer;
use zibo\library\System;

/**
 * Class to generate UUID values
 * @see http://www.ietf.org/rfc/rfc4122.txt
 */
class UUID {

    /**
     * Value to clears all bits of the version byte with AND
     * @var integer
     */
    const CLEAR_VERSION = 15;  // 00001111

    /**
     * Value to clear all relevant bits of the variant byte with AND
     * @var integer
     */
    const CLEAR_VARIANT = 63;  // 00111111

    /**
     * Variant value reserved for future use
     * @var integer
     */
    const VARIANT_RESERVED = 224; // 11100000

    /**
     * Variant value for Microsoft compatibility
     * @var integer
     */
    const VARIANT_MICROSOFT = 192; // 11000000

    /**
     * Variant value for RFC 4122
     * @var integer
     */
    const VARIANT_RFC = 128; // 10000000

    /**
     * Variant value for NCS compatibility
     * @var integer
     */
    const VARIANT_NCS = 0; // 000000

    /**
     * Version value for version 1
     * @var integer
     */
    const VERSION_1 = 16;  // 00010000

    /**
     * Version value for version 2
     * @var integer
     */
    const VERSION_2 = 32;  // 00100000

    /**
     * Version value for version 3
     * @var integer
     */
    const VERSION_3 = 48;  // 00110000

    /**
     * Version value for version 4
     * @var integer
     */
    const VERSION_4 = 64;  // 01000000

    /**
     * Version value for version 5
     * @var integer
     */
    const VERSION_5 = 80;  // 01010000

    /**
     * Time (in 100ns steps) between the start of the UTC and Unix epochs
     * @var integer
     */
    const INTERVAL = 0x01b21dd213814000;

    /**
     * Namespace for when the node string is a fully-qualified domain name
     * @var string
     */
    const NAMESPACE_DNS  = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Namespace for when the node string is a URL
     * @var string
     */
    const NAMESPACE_URL  = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Namespace for when the node string is an ISO OID
     * @var string
     */
    const NAMESPACE_OID  = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Namespace for when the node string is an X.500 DN
     * @var string
     */
    const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * The randomizer
     * @var zibo\library\uuid\randomizer\Randomizer
     */
    protected static $randomizer;

    /**
     * The bytes of this UUID
     * @var integer
     */
    protected $bytes;

    /**
     * The string representation of this UUID
     * @var string
     */
    protected $string;

    /**
     * Constructs a new UUID
     * @param string $uuid The bytes of the UUID
     * @return null
     */
    protected function __construct($uuid) {
        if (strlen($uuid) != 16) {
            throw new UUIDException("Could not construct a new UUID instance: input must be a 128-bit integer.");
        }

        $this->bytes  = $uuid;

        $this->string =
            bin2hex(substr($uuid, 0, 4)) . '-' .
            bin2hex(substr($uuid, 4, 2)) . '-' .
            bin2hex(substr($uuid, 6, 2)) . '-' .
            bin2hex(substr($uuid, 8, 2)) . '-' .
            bin2hex(substr($uuid, 10, 6));
    }

    /**
     * Gets a string representation of this UUID
     * @return string
     */
    public function __toString() {
        return $this->string;
    }

    /**
     * Gets the bytes of the UUID
     * @return string
     */
    public function getBytes() {
        return $this->bytes;
    }

    /**
     * Gets the version of this UUID
     * @return integer
     */
    public function getVersion() {
        return ord($this->bytes[6]) >> 4;
    }

    /**
     * Gets the variant of this UUID
     * @return integer|null 3 for reserved variant, 2 for Microsoft compatibility varuabt, 1 for RFC 4122 variant, null otherwise
     */
    public function getVariant() {
        $byte = ord($this->bytes[8]);

        if ($byte >= self::VARIANT_RESERVED) {
            return 3;
        }

        if ($byte >= self::VARIANT_MICROSOFT) {
            return 2;
        }

        if ($byte >= self::VARIANT_RFC) {
            return 1;
        }

        return null;
    }

    /**
     * Gets the time of this time-based UUID (only for version 1)
     * @return null|integer Null when this UUID is not a time-based UUID, Unix timestamp otherwise
     */
    public function getTime() {
        if ($this->getVersion() != 1) {
            return null;
        }

        // Restore contiguous big-endian byte order
        $time = bin2hex($this->bytes[6] . $this->bytes[7] . $this->bytes[4] . $this->bytes[5] . $this->bytes[0] . $this->bytes[1] . $this->bytes[2] . $this->bytes[3]);

        // Clear version flag
        $time[0] = "0";

        // Do some reverse arithmetic to get a Unix timestamp
        return floor((hexdec($time) - self::INTERVAL) / 10000000);
    }

    /**
     * Gets the node of this time-based UUID (only for version 1)
     * @return string The MAC address of this UUID
     */
    public function getNode() {
        if ($this->getVersion() != 1) {
            return null;
        }

        return bin2hex(substr($this->bytes, 10));
    }

    /**
     * Creates a UUID object from the provided UUID string
     * @param string $uuid The string representation of the UUID
     * @return UUID
     */
    public static function create($uuid) {
        $uuid = self::makeBinary($uuid, 16);
        return new self($uuid);
    }

    /**
     * Generates a new UUID
     * @param integer $version Version of the UUID
     * @param string $node The node for the UUID. For version 1, a MAC address. For version 3 and 5, a 48-bit name value
     * @param string $namespace The namespace for the UUID
     * @return UUID
     */
    public static function generate($version, $node = null, $namespace = null) {
        switch ((int) $version) {
            case 1:
                $uuid = self::generateTimeBased($node);
                break;
            case 2:
                throw new UUIDException('Could not generate the UUID: level 2 is not implemented');
                break;
            case 3:
            case 5:
                $uuid = self::generateNameBased($version, $node, $namespace);
                break;
            case 4:
                $uuid = self::generateRandom();
                break;
            default:
                throw new UUIDException('Could not generate the UUID: version ' . $version . ' is invalid');
                break;
        }

        return new self($uuid);
    }

    /**
     * Generates a new time-based UUID (version 1).
     *
     * These are derived from the time at which they were generated.
     * @param string $node The node for version 1 is a MAC address
     * @return string The bytes of the UUID
     */
    protected static function generateTimeBased($node = null) {
        // Get time since Gregorian calendar reform in 100ns intervals
        // This is exceedingly difficult because of PHP's (and pack()'s)
        // integer size limits.
        // Note that this will never be more accurate than to the microsecond.
        $time = microtime(true) * 10000000 + self::INTERVAL;

        // Convert to a string representation
        $time = sprintf('%F', $time);

        //strip decimal point
        preg_match('/^\d+/', $time, $time);

        // And now to a 64-bit binary representation
        $time = base_convert($time[0], 10, 16);
        $time = pack('H*', str_pad($time, 16, '0', STR_PAD_LEFT));

        // Reorder bytes to their proper locations in the UUID
        $uuid  = $time[4] . $time[5] . $time[6] . $time[7] . $time[2] . $time[3] . $time[0] . $time[1];

        // Generate a random clock sequence
        $uuid .= self::getRandomBytes(2);

        // set variant
        $uuid[8] = chr(ord($uuid[8]) & self::CLEAR_VARIANT | self::VARIANT_RFC);

        // set version
        $uuid[6] = chr(ord($uuid[6]) & self::CLEAR_VERSION | self::VERSION_1);

        // Set the final 'node' parameter, a MAC address
        if ($node) {
            $node = self::makeBinary($node, 6);
        }
        if (!$node) {
            // If no node was provided or if the node was invalid,
            //  generate a random MAC address and set the multicast bit
            $node = self::getRandomBytes(6);
            $node[0] = pack("C", ord($node[0]) | 1);
        }
        $uuid .= $node;

        return $uuid;
    }

    /**
     * Generates a new random UUID (version 4)
     *
     * These are derived soly from random numbers.
     * @return string The bytes of the UUID
     */
    protected static function generateRandom() {
        // generate random fields
        $uuid = self::getRandomBytes(16);

        // set variant
        $uuid[8] = chr(ord($uuid[8]) & self::CLEAR_VARIANT | self::VARIANT_RFC);

        // set the version
        $uuid[6] = chr(ord($uuid[6]) & self::CLEAR_VERSION | self::VERSION_4);

        return $uuid;
    }

    /**
     * Generates a name-based UUID (version 3 or 5)
     *
     * These are derived from a hash of a name and its namespace, in binary form.
     * @param integer $version UUID version
     * @param string $node
     * @param string $namespace
     */
    protected static function generateNameBased($version, $node, $namespace) {
        if (!$node) {
            throw new UUIDException('Could not generate a name-based UUID: a name-string is required');
        }

        // if the namespace UUID isn't binary, make it so
        $namespace = self::makeBinary($namespace, 16);
        if (!$namespace) {
            throw new UUIDException('Could not generate a name-based UUID: a binary namespace is required');
        }

        switch((int) $version) {
            case 3:
                $version = self::VERSION_3;
                $uuid = md5($namespace . $node, true);
                break;
            case 5:
                $version = self::VERSION_5;
                $uuid = substr(sha1($namespace . $node, true), 0, 16);
                break;
            default:
                throw new UUIDException('Could not generate a name-based UUID: invalid version provided');
                break;
        }

        // set variant
        $uuid[8] = chr(ord($uuid[8]) & self::CLEAR_VARIANT | self::VARIANT_RFC);

        // set version
        $uuid[6] = chr(ord($uuid[6]) & self::CLEAR_VERSION | $version);

        return $uuid;
    }

    /**
     * Gets a number of random bytes
     * @param integer $number Number of random bytes
     * @return string String with the random bytes
     */
    protected static function getRandomBytes($number) {
        if (!self::$randomizer) {
            try {
                if (System::isUnix()) {
                    self::$randomizer = new UnixRandomizer();
                } else {
                    self::$randomizer = new WindowsRandomizer();
                }
            } catch (UUIDException $exception) {
                self::$randomizer = new GenericRandomizer();
            }
        }

        return self::$randomizer->getRandomBytes($number);
    }

    /**
     * Insures that the provided string is either binary or hexadecimal
     * @param mixed $string
     * @param integer $length The required number of bytes
     * @return string|boolean A binary representation, false on failure
     */
    protected static function makeBinary($string, $length) {
        if ($string instanceof self) {
            return $string->bytes;
        }

        if (strlen($string) == $length) {
            return $string;
        }

        $string = preg_replace('/^urn:uuid:/is', '', $string); // strip URN scheme and namespace
        $string = preg_replace('/[^a-f0-9]/is', '', $string);  // strip non-hex characters

        if (strlen($string) == ($length * 2)) {
            return pack("H*", $string);
        }

        return false;
    }

}