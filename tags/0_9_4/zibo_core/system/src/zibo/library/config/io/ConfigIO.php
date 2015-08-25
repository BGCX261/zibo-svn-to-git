<?php

namespace zibo\library\config\io;

use zibo\core\environment\Environment;

use zibo\library\filesystem\browser\Browser;

/**
 * Configuration reader and writer
 */
interface ConfigIO {

    /**
     * Read the complete configuration
     * @return array Hierarchic array with each configuration token as a key
     */
    public function readAll();

    /**
     * Read a section from the configuration
     * @param string $section
     * @return array Hierarchic array with each configuration token as a key
     */
    public function read($section);

    /**
     * Write a configuration value
     * @param string $key key of the configuration value
     * @param mixed $value
     * @return null
     */
    public function write($key, $value);

    /**
     * Get the names of all the sections in the configuration
     * @return array Array with the names of all sections in the configuration
     */
    public function getAllSections();

}