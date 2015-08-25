<?php

namespace zibo\library\html;

/**
 * Breadcrumbs HTML element
 */
class Breadcrumbs extends AbstractElement {

    /**
     * Style class for the first breadcrumb
     * @var string
     */
    const STYLE_CLASS_FIRST = 'first';

    /**
     * Style class for the last breadcrumb
     * @var string
     */
    const STYLE_CLASS_LAST = 'last';

    /**
     * Style class for the navigation label
     * @var string
     */
    const STYLE_CLASS_LABEL = 'label';

    /**
     * Default separator between the breadcrumbs
     * @var string
     */
    const DEFAULT_SEPARATOR = ' &gt; ';

    /**
     * Array with the label of a breadcrumb as key and the URL as value
     * @var array
     */
    private $breadcrumbs;

    /**
     * Label before breadcrumbs
     * @var string
     */
    private $label;

    /**
     * Separator between the breadcrumbs
     * @var string
     */
    private $separator;

    /**
     * Construct a new breadcrumbs element
     * @param string $label label before the breadcrumbs
     * @param string $separator separator between the breadcrumbs
     * @return null
     */
    public function __construct($label = null, $separator = null) {
        if (!$separator) {
            $separator = self::DEFAULT_SEPARATOR;
        }

        $this->setLabel($label);
        $this->setSeparator($separator);
        $this->breadcrumbs = array();
    }

    /**
     * Adds a breadcrumb
     * @param string $url URL for the breadcrumb
     * @param string $label label for the breadcrumb
     * @return null
     */
    public function addBreadcrumb($url, $label) {
        $this->breadcrumbs[$label] = $url;
    }

    /**
     * Removes a breadcrumb if it exists
     * @param string $label label of the breadcrumb to remove
     * @return null
     */
    public function removeBreadcrumb($label) {
        if (array_key_exists($label, $this->breadcrumbs)) {
            unset($this->breadcrumbs[$label]);
        }
    }

    /**
     * Checks whether this element has breadcrumbs
     * @return boolean true if there are breadcrumbs, false otherwise
     */
    public function hasBreadcrumbs() {
        return !empty($this->breadcrumbs);
    }

    /**
     * Gets the breadcrumbs of this element
     * @return array Array with the label of the breadcrumb as key and the url of the breadcrumb as value
     */
    public function getBreadcrumbs() {
        return $this->breadcrumbs;
    }

    /**
     * Sets the label before the breadcrumbs
     * @param string $label
     * @return null
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Sets the separator between the breadcrumbs
     * @param string $separator
     * @return null
     */
    public function setSeparator($separator) {
        $this->separator = $separator;
    }

    /**
     * Gets the HTML of this breadcrumbs element
     * @return string
     */
    public function getHtml() {
        if (!$this->hasBreadcrumbs()) {
            return;
        }

        $html = '<div' . $this->getIdHtml() . $this->getClassHtml() . $this->getAttributesHtml() . '>';
        if ($this->label) {
            $html .= '<span class="' . self::STYLE_CLASS_LABEL . '">' . $this->label . '</span>';
        }
        $html .= $this->getBreadcrumbsHtml();
        $html .= '</div>';

        return $html;
    }

    /**
     * Get the HTML of the breadcrumbs itself
     * @return string
     */
    private function getBreadcrumbsHtml() {
        $html = '';
        $index = 1;
        $numBreadcrumbs = count($this->breadcrumbs);

        foreach ($this->breadcrumbs as $label => $url) {
            $anchor = new Anchor($label, $url);

            if ($index == 1) {
                $anchor->appendToClass(self::STYLE_CLASS_FIRST);
            }
            if ($index == $numBreadcrumbs) {
                $anchor->appendToClass(self::STYLE_CLASS_LAST);
            }

            $html .= ($html ? $this->separator : '') . $anchor->getHtml();

            $index++;
        }

        return $html;
    }

}