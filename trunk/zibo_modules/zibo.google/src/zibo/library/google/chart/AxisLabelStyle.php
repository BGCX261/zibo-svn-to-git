<?php

namespace zibo\library\google\chart;

/**
 * Data container for a axis label style
 * @see http://code.google.com/apis/chart/image/docs/chart_params.html#axis_label_styles
 */
class AxisLabelStyle {

    /**
     * Value for the center alignment
     * @var integer
     */
    const ALIGNMENT_CENTER = 0;

    /**
     * Value for left alignment on the X-axis
     * @var integer
     */
    const ALIGNMENT_X_LEFT = -1;

    /**
     * Value for right alignment on the X-axis
     * @var integer
     */
    const ALIGNMENT_X_RIGHT = 1;

    /**
     * Value for LEFT alignment on the Y-axis
     * @var integer
     */
    const ALIGNMENT_Y_LEFT = 1;

    /**
     * Value for right alignment on the Y-axis
     * @var integer
     */
    const ALIGNMENT_Y_RIGHT = -1;

    /**
     * Value for the axisOrTick to draw only the axis
     * @var string
     */
    const DRAW_AXIS = 'l';

    /**
     * Value for the axisOrTick to draw only ticks
     * @var string
     */
    const DRAW_TICK = 't';

    /**
     * Value for the axisOrTick to draw both axis and ticks
     * @var string
     */
    const DRAW_AXIS_AND_TICK = 'lt';

    /**
     * Value for the axisOrTick to draw nothing
     * @var string
     */
    const DRAW_NONE = '_';

    /**
     * String to define the format of the label
     * @var string
     */
    private $formatString;

    /**
     * The color to apply to the axis text
     * @var string
     */
    private $labelColor;

    /**
     * Specifies the font size in pixels
     * @var integer
     */
    private $fontSize;

    /**
     * Label alignment
     * @var integer
     */
    private $alignment;

    /**
     * Flag to show tick marks and/or axis lines for this axis
     * @var integer
     */
    private $axisOrTick;

    /**
     * The color of the tick marks
     * @var string
     */
    private $tickColor;

    /**
     * The color of the axis line
     * @var string
     */
    private $axisColor;

    /**
     * Constructs a new axis label style container
     * @return null
     */
    public function __construct() {
        $this->formatString = null;
        $this->labelColor = null;
        $this->fontSize = null;
        $this->alignment = null;
        $this->axisOrTick = null;
        $this->tickColor = null;
        $this->axisColor = null;
    }

    /**
     * Gets a string representation of this axis style
     * @return string
     */
    public function __toString() {
        return $this->formatString . ',' . $this->labelColor . ',' . $this->fontSize . ',' . $this->alignment . ',' . $this->axisOrTick . ',' . $this->tickColor . ',' . $this->axisColor;
    }

    /**
     * Sets the format string for the label values
     * @param string|null $formatString The format string or null to clear the format string
     * @return null
     */
    public function setFormatString($formatString) {
        $this->formatString = $formatString;
    }

    /**
     * Gets the format string
     * @return string|null The format string or null if not set
     */
    public function getFormatString() {
        return $this->formatString;
    }

    /**
     * Sets the color for the label
     * @param string|null $color HTML color or null to clear value
     * @return null
     */
    public function setLabelColor($color = null) {
        $this->labelColor = $color;
    }

    /**
     * Gets the color for the label
     * @return string|null HTML color or null if not set
     */
    public function getLabelColor() {
        return $this->labelColor;
    }

    /**
     * Sets the font size for the label
     * @param integer|null $size The font size in pixels or null to clear the font size
     * @return null
     */
    public function setFontSize($size = null) {
        $this->fontSize = $size;
    }

    /**
     * Gets the font size for the label
     * @return integer|null The font size in pixels or null if not set
     */
    public function getFontSize() {
        return $this->fontSize;
    }

    /**
     * Sets the alignment for this label
     * @param integer|null $alignment One of the alignment constants or null to clear the alignment
     * @return null
     */
    public function setAlignment($alignment = null) {
        $this->alignment = $alignment;
    }

    /**
     * Gets the alignment for this label
     * @return integer|null One of the alignment constants or null if not set
     */
    public function getAlignment() {
        return $this->alignment;
    }

    /**
     * Sets the draw style of the axis
     * @param integer|null $axisOrTick One of the draw constants or null to clear the value
     * @return null
     */
    public function setAxisOrTick($axisOrTick = null) {
        $this->axisOrTick = $axisOrTick;
    }

    /**
     * Gets the draw style of the axis
     * @return integer One of the draw constants or null to clear the value
     */
    public function getAxisOrTick() {
        return $this->axisOrTick;
    }

    /**
     * Sets the color for the ticks
     * @param string|null $color HTML color or null to clear value
     * @return null
     */
    public function setTickColor($color = null) {
        $this->tickColor = $color;
    }

    /**
     * Gets the color for the ticks
     * @return string|null HTML color or null if not set
     */
    public function getTickColor() {
        return $this->tickColor;
    }

    /**
     * Sets the color for the axis
     * @param string|null $color HTML color or null to clear value
     * @return null
     */
    public function setAxisColor($color = null) {
        $this->axisColor = $color;
    }

    /**
     * Gets the color for the axis
     * @return string|null HTML color or null if not set
     */
    public function getAxisColor() {
        return $this->axisColor;
    }

}