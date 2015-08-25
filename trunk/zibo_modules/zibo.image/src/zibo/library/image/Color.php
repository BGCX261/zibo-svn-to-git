<?php

namespace zibo\library\image;

/**
 * Data container for a color
 */
class Color {

    /**
     * Value for red
     * @var integer
     */
    private $red;

    /**
     * Value for green
     * @var integer
     */
    private $green;

    /**
     * Value for blue
     * @var integer
     */
    private $blue;

    /**
     * Constructs a new color
     * @param integer|string $red
     * @param integer|string $green
     * @param integer|string $blue
     * @return null
     */
    public function __construct($red, $green, $blue) {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    /**
     * Gets a string representation of this color
     * @return string
     */
    public function __toString() {
        return 'RGB(' . $this->red . ', ' . $this->green . ', ' . $this->blue . ')';
    }

    /**
     * Gets the red value
     * @return integer
     */
    public function getRed() {
        return $this->red;
    }

    /**
     * Gets the green value
     * @return integer
     */
    public function getGreen() {
        return $this->green;
    }

    /**
     * Gets the blue value
     * @return integer
     */
    public function getBlue() {
        return $this->blue;
    }

    /**
     * Gets this color in HTML format
     * @return string
     */
    public function getHtmlColor() {
        $red = str_pad(dechex($this->red), 2, '0', STR_PAD_LEFT);
        $green = str_pad(dechex($this->green), 2, '0', STR_PAD_LEFT);
        $blue = str_pad(dechex($this->blue), 2, '0', STR_PAD_LEFT);

        return '#' . $red . $green . $blue;
    }

    /**
     * Gets whether the provided color equals this color
     * @param Color $color Color to compare with
     * @return boolean True if the color is the same, false otherwise
     */
    public function equals(Color $color) {
        return $this->red == $color->red && $this->green == $color->green && $this->blue == $color->blue;
    }

    /**
     * Creates a color object from a HTML color code
     * @param string $htmlColor HTML color code. Accepts the following formats: #AABBCC, AABBCC, #ABC and ABC
     * @return Color
     */
    public static function createFromHtmlColor($htmlColor) {
        if ($htmlColor[0] == '#') {
            $htmlColor = substr($htmlColor, 1);
        }

        if (strlen($htmlColor) == 3) {
            list($red, $green, $blue) = str_split($htmlColor, 1);

            $red .= $red;
            $green .= $green;
            $blue .= $blue;
        } else {
            list($red, $green, $blue) = str_split($htmlColor, 2);
        }

        $red = hexdec($red);
        $green = hexdec($green);
        $blue = hexdec($blue);

        return new self($red, $green, $blue);
    }

}