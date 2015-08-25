<?php

namespace zibo\library\html;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Element for a HTML anchor
 */
class Anchor extends AbstractElement {

    /**
     * Name of the link attribute
     * @var string
     */
    const ATTRIBUTE_HREF = 'href';

    /**
     * The link of this anchor
     * @var string
     */
    private $href;

    /**
     * The label of this anchor
     * @var string
     */
    private $label;

    /**
     * Construct a new HTML anchor
     * @param string $label
     * @param string $href
     * @return null
     */
    public function __construct($label, $href = '#') {
        parent::__construct();
        $this->setLabel($label);
        $this->setHref($href);
    }

    /**
     * Sets the label of this anchor element
     * @param string $label
     * @return null
     * @throws zibo\ZiboException when the label is empty or not a string
     */
    public function setLabel($label) {
        if (String::isEmpty($label)) {
            throw new ZiboException('Provided label is empty');
        }

        $this->label = $label;
    }

    /**
     * Gets the label of this anchor element
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Sets the link of this anchor element
     * @param string $href
     * @return null
     */
    public function setHref($href) {
        parent::setAttribute(self::ATTRIBUTE_HREF, $href);
    }

    /**
     * Gets the link of this anchor element
     * @param string $default default link value for when no link is set
     * @return string
     */
    public function getHref($default = null) {
        return parent::getAttribute(self::ATTRIBUTE_HREF, $default);
    }

    /**
     * Gets the HTML of this anchor element
     * @return string
     */
    public function getHtml() {
        return '<a' .
            $this->getIdHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            '>' .
            $this->getLabel() .
            '</a>';
    }

}