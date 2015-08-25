<?php

namespace zibo\library\google\chart;

/**
 * Defintion of the bar width and spacing
 * @see http://code.google.com/apis/chart/image/docs/gallery/bar_charts.html#chbh
 */
class BarWidthAndSpacing {

    /**
     * Value for spacing in absolute units
     * @var string
     */
    const SCALING_ABSOLUTE = 'a';

    /**
     * Value for spacing in relative units
     * @var string
     */
    const SCALING_RELATIVE = 'r';

    /**
     * Width of the bar when no scaling is set
     * @var integer
     */
    private $barWidth;

    /**
     * Scaling value
     * @var string
     */
    private $scaling;

    /**
     * Absolute or relative value for the spacing between the bars
     * @var integer|float
     */
    private $spacingBetweenBars;

    /**
     * Absolute or relative value for the spacing between the groups
     * @var integer|float
     */
    private $spacingBetweenGroups;

    /**
     * Constructs a new bar width and spacing object
     * @return null
     */
    public function __construct($barWidthOrScaling = null, $spacingBetweenBars = null, $spacingBetweenGroups = null) {
        $this->barWidth = null;
        $this->scaling = null;
        $this->spacingBetweenBars = null;
        $this->spacingBetweenGroups = null;

        if ($barWidthOrScaling == self::SCALING_ABSOLUTE || $barWidthOrScaling == self::SCALING_RELATIVE) {
            $this->scaling = $barWidthOrScaling;
        } else {
            $this->setBarWidth($barWidthOrScaling);
        }

        $this->setSpacingBetweenBars($spacingBetweenBars);
        $this->setSpacingBetweenGroups($spacingBetweenGroups);
    }

    /**
     * Gets a string representation of the bar width and spacing
     * @return string
     */
    public function __toString() {
        if ($this->scaling) {
            $result = $this->scaling;
        } else {
            $result = $this->barWidth;
        }

        if ($this->spacingBetweenBars) {
            $result .= ',' . $this->spacingBetweenBars;
        }

        if ($this->spacingBetweenGroups) {
            $result .= ',' . $this->spacingBetweenGroups;
        }

        return $result;
    }

    /**
     * Sets the width of the bars
     * @param integer $barWidth Width of the bars in pixels
     * @return null
     */
    public function setBarWidth($barWidth) {
        $this->barWidth = $barWidth;
        $this->scaling = null;
    }

    /**
     * Gets the width of th bar
     * @return integer
     */
    public function getBarWidth() {
        return $this->barWidth;
    }

    /**
     * Sets the scaling value
     * @param string $scaling A scaling constant
     * @return null
     */
    public function setScaling($scaling) {
        $this->scaling = $scaling;
        $this->barWidth = null;
    }

    /**
     * Gets the scaling value
     * @return string
     */
    public function getScaling() {
        return $this->scaling;
    }

    /**
     * Sets the spacing between the bars
     * @param integer|float $spacing A value in pixels when the scaling is absolute or not set, a relative float value when scaling is relative. (1.0 is width of the bars)
     * @return null
     */
    public function setSpacingBetweenBars($spacing) {
        $this->spacingBetweenBars = $spacing;
    }

    /**
     * Gets the spacing between the bars
     * @return integer|float|null A absolute value in pixels, a relative value in units, null when not set
     */
    public function getSpacingBetweenBars() {
        return $this->spacingBetweenBars;
    }
    /**
     * Sets the spacing between the bar groups
     * @param integer|float $spacing A value in pixels when the scaling is absolute or not set, a relative float value when scaling is relative. (1.0 is width of the bars)
     * @return null
     */
    public function setSpacingBetweenGroups($spacing) {
        $this->spacingBetweenGroups = $spacing;
    }

    /**
     * Gets the spacing between the groups
     * @return integer|float|null A absolute value in pixels, a relative value in units, null when not set
     */
    public function getSpacingBetweenGroups() {
        return $this->spacingBetweenGroups;
    }

}