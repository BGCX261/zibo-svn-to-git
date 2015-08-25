<?php

namespace zibo\library\excel\format;

use zibo\library\Number;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Container for cell formatting properties
 */
class Format {

    /**
     * Alignment type for center alignment
     * @var string
     */
    const ALIGN_CENTER = 'center';

    /**
     * Alignment type for left alignment
     * @var string
     */
    const ALIGN_LEFT = 'left';

    /**
     * Alignment type for right alignment
     * @var string
     */
    const ALIGN_RIGHT = 'right';

    /**
     * Alignment type for a merged alignment (over multiple cells)
     * @var string
     */
    const ALIGN_MERGE = 'merge';

    /**
     * Border type for a dashed border
     * @var int
     */
    const BORDER_DASHED = 3;

    /**
     * Border type for a dotted border
     * @var int
     */
    const BORDER_DOTTED = 4;

    /**
     * Border type for no border
     * @var int
     */
    const BORDER_NONE = 0;

    /**
     * Border type for a thick border
     * @var int
     */
    const BORDER_THICK = 2;

    /**
     * Border type for a Thin border
     * @var int
     */
    const BORDER_THIN = 1;

    /**
     * Border type for a double thin border
     * @var int
     */
    const BORDER_THIN_DOUBLE = 5;

    /**
     * Normal font weight
     * @var int
     */
    const WEIGHT_NORMAL = 0;

    /**
     * Bold font weight
     * @var unknown_type
     */
    const WEIGHT_BOLD = 1;

    /**
     * Alignment of this format
     * @var string
     */
    private $align = self::ALIGN_LEFT;

    /**
     * Color for the background of the cell (HTML color)
     * @var string
     */
    private $backgroundColor;

    /**
     * Color for the border (HTML color)
     * @var string
     */
    private $borderColor = '#000000';

    /**
     * Type of the border
     * @var int
     */
    private $borderType = self::BORDER_NONE;

    /**
     * Font family
     * @var string
     */
    private $font = 'Verdana';

    /**
     * Color for the text (HTML color)
     * @var string
     */
    private $textColor = '#000000';

    /**
     * Font size in pixels
     * @var int
     */
    private $textSize = 8;

    /**
     * Weight of the text
     * @var int
     */
    private $textWeight = self::WEIGHT_NORMAL;

    /**
     * Set the cell alignment
     * @param string align alignment of the cell (Check constants like ALIGN_LEFT, ALIGN_CENTER, ...)
     */
    public function setAlign($align) {
        if (
            $align != self::ALIGN_CENTER &&
            $align != self::ALIGN_LEFT &&
            $align != self::ALIGN_RIGHT &&
            $align != self::ALIGN_MERGE
            ) {
            throw new ZiboException('Align should be one of the following values: left, center, right, merge');
        }
        $this->align = $align;
    }

    /**
     * Get the cell alignment
     * @return string alignment of the cell
     */
    public function getAlign() {
        return $this->align;
    }

    /**
     * Set the background color of the cell
     * @param string $background Color HTML color eg. #336699
     * @return null
     * @throws zibo\ZiboException when the provided color is not a valid HTML color
     */
    public function setBackgroundColor($backgroundColor) {
        $this->checkColor($backgroundColor);
        $this->backgroundColor = $backgroundColor;
    }

    /**
     * Get the color of the border
     * @return string HTML color of the border
     */
    public function getBackgroundColor() {
        return $this->backgroundColor;
    }

    /**
     * Set the color of the border
     * @param string $borderColor HTML color eg. #336699
     * @return null
     * @throws zibo\ZiboException when the provided color is not a valid HTML color
     */
    public function setBorderColor($borderColor) {
        $this->checkColor($borderColor);
        $this->borderColor = $borderColor;
    }

    /**
     * Get the color of the border
     * @return string HTML color of the border
     */
    public function getBorderColor() {
        return $this->borderColor;
    }

    /**
     * Set the type of the border
     * @param int $borderType type of the border (check constants like BORDER_NONE, BORDER_THIN, ...)
     * @return null
     */
    public function setBorderType($borderType) {
        if (
            $borderType !== self::BORDER_NONE &&
            $borderType != self::BORDER_THIN &&
            $borderType != self::BORDER_THIN_DOUBLE &&
            $borderType != self::BORDER_THICK &&
            $borderType != self::BORDER_DASHED &&
            $borderType != self::BORDER_DOTTED
            ) {
            throw new ZiboException('Invalid border type provided, border can be Format::BORDER_NONE, Format::BORDER_THIN, ...');
        }
        $this->borderType = $borderType;
    }

    /**
     * Get the type of border
     * @return int border type
     */
    public function getBorderType() {
        return $this->borderType;
    }

    /**
     * Set the font family of the text
     * @param string $font font family eg. Verdana, Arial
     * @return null
     */
    public function setFont($font) {
    	if (String::isEmpty($font)) {
    		throw new ZiboException('Provided font is empty');
    	}
        $this->font = $font;
    }

    /**
     * Get the font family of the text
     * @return string font family of the text
     */
    public function getFont() {
        return $this->font;
    }

    /**
     * Set the color of the text
     * @param string $textColor HTML color eg. #336699
     * @return null
     * @throws zibo\ZiboException when the provided color is not a valid HTML color
     */
    public function setTextColor($textColor) {
        $this->checkColor($textColor);
        $this->textColor = $textColor;
    }

    /**
     * Get the color of the text
     * @return string HTML color of the text
     */
    public function getTextColor() {
        return $this->textColor;
    }

    /**
     * Set the size of the text
     * @param int $size text size
     * @return null
     */
    public function setTextSize($size) {
        if (Number::isNegative($size)) {
            throw new ZiboException('Invalid text size provided, text size should be a positive numeric value');
        }
        $this->textSize = $size;
    }

    /**
     * Get the size of the text
     * @return int text size
     */
    public function getTextSize() {
        return $this->textSize;
    }

    /**
     * Set the text weight.
     * @param int $weight weight can be one of the constants WEIGHT_NORMAL and WEIGHT_BOLD
     * @return null
     */
    public function setTextWeight($weight) {
        if ($weight !== self::WEIGHT_NORMAL && $weight != self::WEIGHT_BOLD) {
            throw new ZiboException('Invalid text weight provided, text weight can be Format::WEIGHT_NORMAL, Format::WEIGHT_BOLD');
        }
        $this->textWeight = $weight;
    }

    /**
     * Get the text weight
     * @return int text weight
     */
    public function getTextWeight() {
        return $this->textWeight;
    }

    /**
     * Checks whether the given color string is a valid HTML color
     * @param string $color HTML color eg. #336699
     * @return null
     * @throws zibo\ZiboException when the provided color is not a valid HTML color
     */
    private function checkColor($color) {
        if (!preg_match('/^#\w\w\w\w\w\w$/', $color)) {
            throw new ZiboException($color . ' is not a valid HTML color. eg #336699');
        }
    }

}