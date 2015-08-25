<?php

namespace zibo\core\config;

use zibo\core\config\exception\ConfigException;
use zibo\core\config\io\CachedConfigIO;
use zibo\core\config\io\ConfigIO;

use zibo\library\String;

/**
 * Configuration data container
 */
class Config {

    /**
     * Separator between the tokens of the configuration key
     * @var string
     */
    const TOKEN_SEPARATOR = '.';

    /**
     * Array with the configuration
     * @var array
     */
    private $data;

    /**
     * Configuration input/output implementation
     * @var zibo\core\config\io\ConfigIO
     */
    private $io;

    /**
     * Constructs a new configuration container
     * @param zibo\core\config\io\ConfigIO $io Configuration input/output
     * implementation
     * @return null
     */
    public function __construct(ConfigIO $io) {
        $this->data = array();
        $this->io = $io;
    }

    /**
     * Clears the cache
     * @return null
     */
    public function clearCache() {
        if ($this->io instanceof CachedConfigIO) {
            $this->io->clearCache();
        }
    }

    /**
     * Gets the complete configuration as a tree
     * @return array Tree like array with each configuration key token as a
     * array key
     */
    public function getAll() {
        return $this->data = $this->io->readAll();
    }

    /**
     * Gets a configuration value
     * @param string $key configuration key
     * @param mixed $default default value for when the configuration key is
     * not set
     * @return mixed the configuration value if set, the provided default
     * value otherwise
     * @throws zibo\core\config\exception\ConfigException when the key is empty
     * or not a string
     */
    public function get($key, $default = null) {
        $tokens = $this->getKeyTokens($key);

        $result = $this->data;
        foreach ($tokens as $token) {
            if (!isset($result[$token])) {
                return $default;
            }
            $result = $result[$token];
        }

        return $result;
    }

    /**
     * Sets a configuration value
     * @param string $key configuration key
     * @param mixed $value value for the configuration key
     * @return null
     * @throws zibo\core\config\exception\ConfigException when the key is empty
     * or not a string
     */
    public function set($key, $value) {
        $tokens = $this->getKeyTokens($key);

        $data =& $this->data;
        $numTokens = count($tokens);
        for ($index = 0; $index < $numTokens; $index++) {
            $token = $tokens[$index];
            if ($index == $numTokens - 1) {
                $dataKey = $token;
                break;
            }

            if (isset($data[$token]) && is_array($data[$token])) {
                $data = &$data[$token];
            } else {
                $data[$token] = array();
                $data = &$data[$token];
            }
        }
        $data[$dataKey] = $value;

        $this->io->write($key, $value);
    }

    /**
     * Gets the tokens of a configuration key. This method will read the
     * configuration for the section token (first token) if it has not been read before.
     * @return array Array with the tokens of the configuration key
     */
    private function getKeyTokens($key) {
        if (!String::isString($key, String::NOT_EMPTY)) {
            throw new ConfigException('Provided key is empty');
        }

        $tokens = explode(self::TOKEN_SEPARATOR, $key);

        $section = $tokens[0];
        if (!isset($this->data[$section])) {
            $this->data[$section] = $this->io->read($section);
        }

        return $tokens;
    }


    /**
     * Parses a hierarchic array into a flat array
     * @param array $config Hierarchic array with configuration values
     * @param string $prefix Prefix for the keys of the configuration array
     * (needed for recursive calls)
     * @return array Flat array of the provided configuration
     */
    public static function parseConfigTree(array $config, $prefix = null) {
        $result = array();

        if ($prefix) {
            $prefix .= self::TOKEN_SEPARATOR;
        }

        foreach ($config as $key => $value) {
            $prefixedKey = $prefix . $key;

            if (is_array($value)) {
                $result = self::parseConfigTree($value, $prefixedKey) + $result;
            } else {
                $result[$prefixedKey] = $value;
            }
        }

        return $result;
    }

}