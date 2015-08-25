<?php

namespace zibo\library\google\chart;

use zibo\library\Number;

use zibo\ZiboException;

/**
 * Class to render image charts
 * @see http://code.google.com/apis/chart/image
 */
class Chart {

    /**
     * The base URL for a Google chart request
     * @var string
     */
    const BASE_URL = 'https://chart.googleapis.com/chart?';

    /**
     * Name of the type parameter
     * @var string
     */
    const PARAM_TYPE = 'cht';

    /**
     * Name of the size parameter
     * @var string
     */
    const PARAM_SIZE = 'chs';

    /**
     * Name of the color parameter
     * @var string
     */
    const PARAM_COLORS = 'chco';

    /**
     * Name of the data parameter
     * @var string
     */
    const PARAM_DATA = 'chd';

    /**
     * Name of the legend parameter
     * @var string
     */
    const PARAM_LEGEND = 'chdl';

    /**
     * Name of the legend position parameter
     * @var string
     */
    const PARAM_LEGEND_POSITION = 'chdlp';

    /**
     * Name of the legend style parameter
     * @var string
     */
    const PARAM_LEGEND_STYLE = 'chdls';

    /**
     * Name of the label parameter
     * @var string
     */
    const PARAM_LABEL = 'chl';

    /**
     * Name of the scaling parameter
     * @var string
     */
    const PARAM_SCALING = 'chds';

    /**
     * Name of the axis parameter
     * @var string
     */
    const PARAM_AXIS = 'chxt';

    /**
     * Name of the axis label parameter
     * @var string
     */
    const PARAM_AXIS_LABELS = 'chxl';

    /**
     * Name of the axis styles parameter
     * @var string
     */
    const PARAM_AXIS_STYLES = 'chxs';

    /**
     * Name of the bar width and spacing parameter
     * @var string
     */
    const PARAM_BAR_WIDTH_AND_SPACING = 'chbh';

    /**
     * Name of the horizontal stacked bar type
     * @return string
     */
    const TYPE_BAR_HORIZONTAL_STACKED = 'bhs';

    /**
     * Name of the vertical stacked bar type
     * @return string
     */
    const TYPE_BAR_VERTICAL_STACKED = 'bvs';

    /**
     * Name of the pie type
     * @var string
     */
    const TYPE_PIE = 'p';

    /**
     * Name of the 3D pie type
     * @var string
     */
    const TYPE_PIE_3D = 'p3';

    /**
     * Name of the concentric pie type
     * @var string
     */
    const TYPE_PIE_CONCENTRIC = 'pc';

    /**
     * Suffix for the chart type to disable the axis
     * @var string
     */
    const SUFFIX_NO_AXIS = ':nda';

    /**
     * Legend positioning value for bottom horizontal
     * @var string
     */
    const LEGEND_BOTTOM_HORIZONTAL = 'b';

    /**
     * Legend positioning value for bottom vertical
     * @var string
     */
    const LEGEND_BOTTOM_VERTICAL = 'bv';

    /**
     * Legend positioning value for top horizontal
     * @var string
     */
    const LEGEND_TOP_HORIZONTAL = 't';

    /**
     * Legend positioning value for top vertical
     * @var string
     */
    const LEGEND_TOP_VERTICAL = 'tv';

    /**
     * Legend positioning value for right vertical
     * @var string
     */
    const LEGEND_RIGHT_VERTICAL = 'r';

    /**
     * Legend positioning value for left vertical
     * @var string
     */
    const LEGEND_LEFT_VERTICAL = 'l';

    /**
     * The type of the chart
     * @var string
     */
    private $type;

    /**
     * The width of the generated image
     * @var integer
     */
    private $width;

    /**
     * The height of the generated image
     * @var integer
     */
    private $height;

    /**
     * The colors for the data representation
     * @var array
     */
    private $colors;

    /**
     * The data of the chart
     * @var array
     */
    private $data;

    /**
     * The scale for the data
     * @var array
     */
    private $scale;

    /**
     * Flag to see if auto scaling if set
     * @var boolean
     */
    private $isAutoScaling;

    /**
     * The label(s) for the data
     * @var string|array
     */
    private $label;

    /**
     * The legend for the data
     * @var array
     */
    private $legend;

    /**
     * The legend position value
     * @var string
     */
    private $legendPosition;

    /**
     * The color for the legend font
     * @var string
     */
    private $legendColor;

    /**
     * The font size for the legend
     * @var integer
     */
    private $legendFontSize;

    /**
     * Flag to see if the axis should be drawn
     * @var boolean
     */
    private $willShowAxis;

    /**
     * The definition for the axis
     * @var array
     */
    private $axis;

    /**
     * The labels for the axis
     * @var array
     */
    private $axisLabels;

    /**
     * The styles for the axis
     * @var array
     */
    private $axisStyles;

    /**
     * Tha definition for the bar width and spacing
     * @var BarWidthAndSpacing
     */
    private $barWidthAndSpacing;

    /**
     * Constructs a new chart object
     * @return null
     */
    public function __construct() {
        $this->type = self::TYPE_BAR_HORIZONTAL_STACKED;
        $this->width = 250;
        $this->height = 100;
        $this->colors = null;
        $this->data = null;
        $this->label = null;
        $this->legend = null;
        $this->legendPosition = null;
        $this->legendColor = '666666';
        $this->legendFontSize = 11;
        $this->scale = null;
        $this->isAutoScaling = false;
        $this->willShowAxis = true;
        $this->axis = null;
        $this->axisLabels = null;
        $this->axisStyles = array();
        $this->barWidthAndSpacing = null;
    }

    /**
     * Sets the type of the graph
     * @param string $type A type constant
     * @return null
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Gets the type of the graph
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the dimension of the chart
     * @param integer $width The width of the chart
     * @param integer $height The height of the chart
     * @return null
     */
    public function setDimension($width, $height) {
        $this->checkDimension($width, $height);
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Sets the width of the chart
     * @param integer $width The new width of the chart
     * @return null
     */
    public function setWidth($width) {
        $this->checkDimension($width, $this->height);
        $this->width = $width;
    }

    /**
     * Gets the width of the chart
     * @return integer
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * Sets the height of the chart
     * @param integer $height The new height of the chart
     * @return null
     */
    public function setHeight($height) {
        $this->checkDimension($this->width, $height);
        $this->height = $height;
    }

    /**
     * Gets the height of the chart
     * @return integer
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * Checks the dimensions
     * @param mixed $width The new width
     * @param mixed $height The new height
     * @return null
     * @throws zibo\ZiboException when the provided dimension is not valid
     */
    private function checkDimension($width, $height) {
        if (Number::isNegative($width)) {
            throw new ZiboException('Could not set the dimensions: provided width is negative');
        }
        if ($width > 1000) {
            throw new ZiboException('Could not set the dimensions: provided width exceeds 1000px');
        }
        if (Number::isNegative($height)) {
            throw new ZiboException('Could not set the dimensions: provided height is negative');
        }
        if ($height > 1000) {
            throw new ZiboException('Could not set the dimensions: provided height exceeds 1000px');
        }
        if ($width * $height > 300000) {
            throw new ZiboException('Could not set the dimensions: width multiplied with the height cannot exceed 300.000');
        }
    }

    /**
     * Sets the colors for the chart
     * @param string|array $colors Array containing RRGGBB values or a nested array containing RRGGBB values
     * @return null
     */
    public function setColors($colors) {
        $this->colors = $colors;
    }

    /**
     * Gets the colors of the chart
     * @return array
     */
    public function getColors() {
        return $this->colors;
    }

    /**
     * Sets the data for the chart
     * @param array $data A collection of datasets with an array of values inside
     * @return null
     */
    public function setData(array $data) {
        $this->data = $data;
    }

    /**
     * Gets the data for the chart
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Sets the label(s) for the data
     * @param string|array $label The label(s) for the data
     * @return null
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Gets the label(s) for the data
     * @return string|array
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Sets the legend for the data
     * @param array $label The legend for the data
     * @return null
     */
    public function setLegend(array $legend) {
        $this->legend = $legend;
    }

    /**
     * Gets the legend for the data
     * @return array
     */
    public function getLegend() {
        return $this->legend;
    }

    /**
     * Sets the legend position
     * @param string|null $position A legend position constant or null to clear the value
     * @return null
     */
    public function setLegendPosition($position = null) {
        $this->legendPosition = $position;
    }

    /**
     * Gets the legend position
     * @return string|null A legend position constant or null if not set
     */
    public function getLegendPosition() {
        return $this->legendPosition;
    }

    /**
     * Sets the color of the legend
     * @param string $color HTML color
     * @return null
     */
    public function setLegendColor($color) {
        $this->legendColor = $color;
    }

    /**
     * Gets the color of the legend
     * @return string HTML color
     */
    public function getLegendColor() {
        return $this->legendColor;
    }

    /**
     * Sets the font size for the legend
     * @param integer $size The font size for the legend
     * @return null
     */
    public function setLegendFontSize($size) {
        $this->legendFontSize = $size;
    }

    /**
     * Gets the font size for the legend
     * @return integer
     */
    public function getLegendFontSize() {
        return $this->legendFontSize;
    }

    /**
     * Sets the scale for the data
     * @param array $scale
     * @return null
     */
    public function setScale(array $scale) {
        $this->scale = $scale;
        $this->isAutoScaling = false;
    }

    /**
     * Gets the scale of the data
     * @return array
     */
    public function getScale() {
        return $this->scale;
    }

    /**
     * Sets the auto scaling flag
     * @param boolean $flag
     * @return null
     */
    public function setIsAutoScaling($flag) {
        $this->isAutoScaling = $flag;
    }

    /**
     * Checks whether autoscaling is enabled
     * @return boolean
     */
    public function isAutoScaling() {
        return $this->isAutoScaling;
    }

    /**
     * Sets whether the axis will be shown
     * @param boolean $flag True to show the axis, false otherwise
     * @return null
     */
    public function setWillShowAxis($flag) {
        $this->willShowAxis = $flag;
    }

    /**
     * Checks whether the axis will be shown
     * @return boolean True to show the axis, false otherwise
     */
    public function willShowAxis($flag) {
        return $this->willShowAxis;
    }

    /**
     * Sets the axis for the chart
     * @param array $axis Array with the definition for the axis (x, y, r, t)
     * @return null
     */
    public function setAxis(array $axis) {
        $this->axis = $axis;
    }

    /**
     * Gets the axis of the chart
     * @return array Array with the definition for the axis
     */
    public function getAxis() {
        return $this->axis;
    }

    /**
     * Sets the labels for the axis
     * @param array $labels Array with the index of the axis as key and an array with axis values as value
     * @return null
     */
    public function setAxisLabels(array $labels) {
        $this->axisLabels = $labels;
    }

    /**
     * Gets the labels for the axis
     * @return array Array with the index of the axis as key and an array with axis values as value
     */
    public function getAxisLabels() {
        return $this->axisLabels;
    }

    /**
     * Sets the style for the provided axis
     * @param integer $axisIndex The index of the axis
     * @param AxisLabelStyle $style The style for the axis
     * @return null
     */
    public function setAxisStyle($axisIndex, AxisLabelStyle $style) {
        $this->axisStyles[$axisIndex] = $style;
    }

    /**
     * Gets the axis styles
     * @return array Array with the index of the axis as key and the axis style as value
     */
    public function getAxisStyles() {
        return $this->axisStyles;
    }

    /**
     * Sets the bar width and the spacing
     * @param BarWidthAndSpacing $barWidthAndSpacing
     * @return null
     */
    public function setBarWidthAndSpacing(BarWidthAndSpacing $barWidthAndSpacing) {
        $this->barWidthAndSpacing = $barWidthAndSpacing;
    }

    /**
     * Gets the bar width and the spacing
     * @return BarWidthAndSpacing|null The bar width and spacing if set, null otherwise
     */
    public function getBarWidthAndSpacing() {
        return $this->barWidthAndSpacing;
    }

    /**
     * Gets the URL to the image of this chart
     * @return string
     */
    public function getUrl() {
        $type = $this->type;
        if (!$this->willShowAxis) {
            $type .= self::SUFFIX_NO_AXIS;
        }

        $url = self::BASE_URL . self::PARAM_TYPE . '=' . $type;

        $url .= $this->getSizeUrlPart();
        $url .= $this->getColorUrlPart();
        $url .= $this->getDataUrlPart();
        $url .= $this->getLabelUrlPart();
        $url .= $this->getLegendUrlPart();
        $url .= $this->getScaleUrlPart();

        if ($this->willShowAxis) {
            $url .= $this->getAxisUrlPart();
            $url .= $this->getAxisLabelUrlPart();
            $url .= $this->getAxisStylesUrlPart();
        }

        $url .= $this->getBarWidthAndSpacingUrlPart();

        return $url;
    }

    /**
     * Gets the URL part for the image size
     * @return string
     */
    private function getSizeUrlPart() {
        return '&' . self::PARAM_SIZE . '=' . $this->width . 'x' . $this->height;
    }

    /**
     * Gets the URL part for the color definition
     * @return string
     */
    private function getColorUrlPart() {
        if (!$this->colors) {
            return '';
        }

        $colorString = '';

        if (is_array($this->colors)) {
            foreach ($this->colors as $color) {
                if (is_array($color)) {
                    $concatString = '';

                    foreach ($color as $innerColor) {
                        $concatString .= ($concatString != ''  ? '|' : '') . $innerColor;
                    }
                } else {
                    $concatString = $color;
                }

                $colorString .= ($colorString != ''  ? ',' : '') . $concatString;
            }
        } else {
            $colorString = $this->colors;
        }

        return '&' . self::PARAM_COLORS . '=' . $colorString;
    }

    /**
     * Gets the URL part for the data
     * @return string
     */
    private function getDataUrlPart() {
        $dataString = '';

        foreach ($this->data as $data) {
            if (is_array($data)) {
                $concatString = '';

                foreach ($data as $innerData) {
                    $concatString .= ($concatString != '' ? ',' : '') . $innerData;
                }
            } else {
                $concatString = $data;
            }

            $dataString .= ($dataString != '' ? '|' : '') . $concatString;
        }

        $dataString = 't:' . $dataString;

        return '&' . self::PARAM_DATA . '=' . $dataString;
    }

    /**
     * Gets the URL part for the data legend
     * @return string
     */
    private function getLegendUrlPart() {
        if (!$this->legend) {
            return '';
        }

        $legendString = '';

        foreach ($this->legend as $legend) {
            $legendString .= ($legendString != '' ? '|' : '') . urlencode(strip_tags($legend));
        }

        $part = '&' . self::PARAM_LEGEND . '=' . $legendString;

        if ($this->legendPosition) {
            $part .= '&' . self::PARAM_LEGEND_POSITION . '=' . $this->legendPosition;
        }

        if ($this->legendColor) {
            $part .= '&' . self::PARAM_LEGEND_STYLE . '=' . $this->legendColor;

            if ($this->legendFontSize) {
                $part .= ',' . $this->legendFontSize;
            }
        }

        return $part;
    }

    /**
     * Gets the URL part for the data label(s)
     * @return string
     */
    private function getLabelUrlPart() {
        if (!$this->label) {
            return '';
        }

        $labelString = '';

        if (is_array($this->label)) {
            foreach ($this->label as $label) {
                $labelString .= ($labelString != '' ? '|' : '') . $this->encodeString($label);
            }
        } else {
            $labelString = $this->encodeString($this->label);
        }

        return '&' . self::PARAM_LABEL . '=' . $labelString;
    }

    /**
     * Gets the URL part for the data scale
     * @return string
     */
    private function getScaleUrlPart() {
        if (!$this->scale && !$this->isAutoScaling) {
            return '';
        }

        $scalingString = '';

        if ($this->isAutoScaling) {
            foreach ($this->data as $data) {
                $min = 0;
                $max = 0;

                if (is_array($data)) {
                    foreach ($data as $innerData) {
                        $min = min($min, $innerData);
                        $max = max($max, $innerData);
                    }
                } else {
                    $min = $data;
                    $max = $data;
                }

                $scalingString .= ($scalingString != '' ? ',' : '') . $min . ',' . $max;
            }
        } else {
            foreach ($this->scale as $limit) {
                $scalingString .= ($scalingString != '' ? ',' : '') . $limit;
            }
        }

        return '&' . self::PARAM_SCALING . '=' . $scalingString;
    }

    /**
     * Gets the URL part for the axis definition
     * @return string
     */
    private function getAxisUrlPart() {
        if (!$this->axis) {
            return '';
        }

        return '&' . self::PARAM_AXIS . '=' . implode(',', $this->axis);
    }

    /**
     * Gets the URL part for the axis labels
     * @return string
     */
    private function getAxisLabelUrlPart() {
        if (!$this->axisLabels) {
            return '';
        }

        $labelString = '';

        foreach ($this->axisLabels as $axis => $labels) {
            $labelsString = '';

            foreach ($labels as $label) {
                $labelsString .= ($labelsString != '' ? '|' : '') . urlencode(strip_tags($label));
            }

            $labelString .= ($labelString != '' ? '|' : '') . $axis . ':|' . $labelsString;
        }

        return '&' . self::PARAM_AXIS_LABELS . '=' . $labelString;
    }

    /**
     * Gets the URL part for the axis label styles
     * @return string
     */
    private function getAxisStylesUrlPart() {
        if (!$this->axisStyles) {
            return '';
        }

        $stylesString = '';
        foreach ($this->axisStyles as $axis => $style) {
            $stylesString .= ($stylesString != '' ? '|' : '') . $axis . $style;
        }

        return '&' . self::PARAM_AXIS_STYLES . '=' . $stylesString;
    }

    /**
     * Gets the URL part for the bar width and bar spacing
     * @return string
     */
    private function getBarWidthAndSpacingUrlPart() {
        if (!$this->barWidthAndSpacing) {
            return '';
        }

        return '&' . self::PARAM_BAR_WIDTH_AND_SPACING . '=' . $this->barWidthAndSpacing;
    }

    /**
     * Encode a string for a URL part
     * @param string $string The string which needs to be encoded
     * @return string
     */
    private function encodeString($string) {
        return urlencode(strip_tags($string));
    }

    /**
     * Gets the HTML for the image of the chart
     * @return string
     */
    public function getHtml() {
        return '<img src="' . str_replace('&', '&amp;', $this->getUrl()) . '" />';
    }

}