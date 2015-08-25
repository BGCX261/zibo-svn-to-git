<?php

namespace zibo\library\bbcode\tag;

use zibo\library\Number;

use zibo\ZiboException;

/**
 * Implementation of the size tag
 */
class SizeTag extends AbstractTag {

    /**
     * Minimum allowed size
     * @var integer
     */
    private $minimum = 5;

    /**
     * Maximum allowed size
     * @var integer
     */
    private $maximum = 40;

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'size';

    /**
     * Constructs the size tag
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

    /**
     * Parses the tag
     * @param string $content Content of the tag
     * @param array $parameters Parameters of the tag
     * @return string HTML replacement for the tag
     */
    public function parseTag($content, array $parameters) {
        if (count($parameters) != 1 && !array_key_exists(0, $parameters)) {
            return false;
        }

        $size = $parameters[0];

        if ($size < $this->minimum || $this->maximum < $size) {
            return false;
        }

        return '<span style="font-size: ' . $size . 'px;">' . $content . '</span>';
    }

    /**
     * Sets the allowed minimum size
     * @param integer $minimum
     * @return null
     * @throws zibo\ZiboException when the provided minimum is invalid
     */
    public function setMinimum($minimum) {
        if (Number::isNegative($minimum)) {
            throw new ZiboException('Provided minimum cannot be negative');
        }

        $this->minimum = $minimum;
    }

    /**
     * Sets the allowed maximum size
     * @param integer $maximum
     * @return null
     */
    public function setMaximum($maximum) {
        if (Number::isNegative($maximum)) {
            throw new ZiboException('Provided maximum cannot be negative');
        }
        $this->maximum = $maximum;
    }

}