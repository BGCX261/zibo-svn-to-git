<?php

namespace joppa\model;

use zibo\library\String;

/**
 * Region data container
 */
class Region {

    /**
     * Name of the region
     * @var string $name
     */
    private $name;

    /**
     * Construct this region
     * @param string $name name of the region
     * @return null
     * @throws zibo\ZiboException when the provided name is invalid
     */
    public function __construct($name) {
        $this->setName($name);
    }

    /**
     * Set the name of this theme
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the provided name is invalid
     */
    private function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Name of the region is empty');
        }

        $this->name = $name;
    }

    /**
     * Get the name of this region
     * @return string
     */
    public function getName() {
        return $this->name;
    }

}