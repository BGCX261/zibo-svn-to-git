<?php

namespace zibo\library;

/**
 * Represents a HTTP header, as a name - value pair.
 */
class Header {

    /**
     * Name of the header
     * @var string
     */
    private $name;

    /**
     * Value of the header
     * @var string
     */
    private $value;

    /**
     * Construct this header
     * @param string $name
     * @param string $value
     * @return null
     */
    public function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Returns the header formatted as a string
     *
     * The returned string is ready to be used by {@link header() PHP's header function}
     *
     * @return string
     */
    public function __toString() {
        return $this->name . ': ' . $this->value;
    }

    /**
     * Get the name of this header
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the value of this header
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

}