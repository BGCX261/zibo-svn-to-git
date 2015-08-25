<?php

namespace zibo\library\orm\model\data;

/**
 * Country data container
 */
class CountryData extends Data {

    /**
     * Code of the country
     * @var string
     */
    public $code;

    /**
     * Name of the country
     * @var string
     */
    public $name;

    /**
     * Continent of the country
     * @var Continent
     */
    public $continent;

    /**
     * Gets a string representation of this data
     * @return string
     */
    public function __toString() {
        return $this->name . ' (' . $this->code . ')';
    }

}