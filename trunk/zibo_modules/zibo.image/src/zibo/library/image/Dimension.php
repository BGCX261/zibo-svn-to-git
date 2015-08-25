<?php

namespace zibo\library\image;

/**
 * Data container for a dimension
 */
class Dimension {

    /**
     * Width of the dimension
     * @var integer
     */
    private $width;

    /**
     * Height of the dimension
     * @var integer
     */
    private $height;

    /**
     * Constructs a new dimension
     * @param integer $width
     * @param integer $height
     * @return null
     */
    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Gets a string representation of this dimension
     * @return string;
     */
    public function __toString() {
        return $this->width . ' x ' . $this->height;
    }

    /**
     * Gets the width of the dimension
     * @return integer
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * Gets the height of the dimension
     * @return integer
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Gets whether the provided dimension equals this dimension
     * @param Dimension $dimension Dimension to compare with
     * @return boolean True if the dimension is the same, false otherwise
     */
    public function equals(Dimension $dimension) {
        return $this->width == $point->width && $this->height == $point->height;
    }

}