<?php

namespace zibo\library\orm\definition;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Definition of a data format. A data format is used to parse a data object of a model into a
 * human readable string. Can be used to get a title, teaser, image or date of a data object.
 */
class DataFormat {

    /**
     * The name of the format
     * @var string
     */
    protected $name;

    /**
     * The format string
     * @var string
     */
    protected $format;

    /**
     * Constructs a new data format
     * @param string $name Name of the format
     * @param string $format Format string
     * @return null
     * @throws zibo\ZiboException when the provided name or format is empty or not a string
     */
    public function __construct($name, $format) {
        $this->setName($name);
        $this->setFormat($format);
    }

    /**
     * Sets the name of this format
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or not a string
     */
    protected function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        $this->name = $name;
    }

    /**
     * Gets the name of this format
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the format string
     * @param string $format
     * @return null
     * @throws zibo\ZiboException when the provided format is empty or not a string
     */
    protected function setFormat($format) {
        if (String::isEmpty($format)) {
            throw new ZiboException('Provided format is empty');
        }

        $this->format = $format;
    }

    /**
     * Gets the format string
     * @return string
     */
    public function getFormat() {
        return $this->format;
    }

}