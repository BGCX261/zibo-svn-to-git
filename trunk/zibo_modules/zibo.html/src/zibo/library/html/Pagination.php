<?php

namespace zibo\library\html;

use zibo\library\i18n\I18n;

/**
 * Pagination HTML element
 */
class Pagination extends AbstractElement {

    /**
     * Translation key for the previous button
     * @var string
     */
    const TRANSLATION_TITLE_PREVIOUS = 'label.page.previous';

    /**
     * Translation key for the next button
     * @var string
     */
    const TRANSLATION_TITLE_NEXT = 'label.page.next';

    /**
     * Translation key for the page number
     * @var string
     */
    const TRANSLATION_TITLE_PAGE = 'label.page.number';

    /**
     * Label for the gaps
     * @var string
     */
    private $ellipsis = '<span class="ellipsis">...</span>';

    /**
     * Label for the next page anchor
     * @var string
     */
    private $nextLabel = '&raquo;';

    /**
     * Style class for the next page anchor
     * @var string
     */
    private $nextClass = 'next';

    /**
     * Label for the previous page anchor
     * @var string
     */
    private $previousLabel = '&laquo;';

    /**
     * Style class for the previous page anchor
     * @var string
     */
    private $previousClass = 'previous';

    /**
     * Style class for a page anchor
     * @var string
     */
    private $pageClass = 'page';

    /**
     * Style class for the label
     * @var string
     */
    private $labelClass = 'label';

    /**
     * Translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Label to display before the page numbers
     * @var string
     */
    private $label;

    /**
     * Href attribute for the page anchors
     * @var string
     */
    private $href;

    /**
     * Onclick attribute for the page anchors
     * @var string
     */
    private $onclick;

    /**
     * Total number of pages
     * @var int
     */
    private $pages;

    /**
     * Number of the current page
     * @var int
     */
    private $page;

    /**
     * Construct a new pagination HTML element
     * @param int $pages number of pages
     * @param int $page number of the current page
     * @return null
     */
    public function __construct($pages, $page) {
        $this->pages = $pages;
        $this->page = $page;
    }

    /**
     * Set the href attribute for each page anchor
     * @param string $onclick
     * @return null
     */
    public function setHref($href) {
        $this->href = $href;
    }

    /**
     * Set the label
     * @param string $label this string will be displayed before the page numbers
     * @return null
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Set the onclick attribute for each page anchor
     * @param string $onclick
     * @return null
     */
    public function setOnclick($onclick) {
        $this->onclick = $onclick;
    }

    /**
     * Get the HTML of this pagination element
     * @return string
     */
    public function getHtml() {
        $this->translator = I18n::getInstance()->getTranslator();

        $html = '<div class="pagination">' . "\n";

        if ($this->label) {
            $html .= '<span class="' . $this->labelClass . '">' . $this->label . '</span>';
        }

        if (!empty($this->page)) {
            if ($this->page != 1) {
                $anchor = $this->createAnchor($this->previousLabel, $this->previousClass, $this->page - 1);
                $anchor->setAttribute('title', $this->translator->translate(self::TRANSLATION_TITLE_PREVIOUS));
                $html .= "\t" . $anchor->getHtml() . "\n";
            } else {
                $html .= "\t" . '<span class="' . $this->previousClass . '">' . $this->previousLabel . '</span>' . "\n";
            }
        }

        $html .= $this->getPagesHtml();

        if (!empty($this->page)) {
            if ($this->page != $this->pages) {
                $anchor = $this->createAnchor($this->nextLabel, $this->nextClass, $this->page + 1);
                $anchor->setAttribute('title', $this->translator->translate(self::TRANSLATION_TITLE_NEXT));
                $html .= "\t" . $anchor->getHtml() . "\n";
            } else {
                $html .= "\t" . '<span class="' . $this->nextClass . '">' . $this->nextLabel . '</span>' . "\n";
            }
        }

        $html .= '</div>' . "\n";

        return $html;
    }

    /**
     * Get the HTML of the page numbers
     * @return string
     */
    private function getPagesHtml() {
        $gaps = $this->getGaps();
        $gap = null;
        $currentGap = null;
        $html = '';

        if ($gaps) {
            $gap = array_pop($gaps);
        }
        for ($i = 1; $i <= $this->pages; $i++) {
            if ($i == $this->page) {
                $html .= "\t" . '<span class="' . $this->pageClass . '">' . $i . '</span>' . "\n";
                continue;
            }

            $display = true;
            if ($currentGap != null && $currentGap['stop'] == $i) {
                $currentGap = null;
                if ($gaps) {
                    $gap = array_pop($gaps);
                }
                $html .= "\t" . $this->ellipsis . "\n";
            } elseif ($currentGap != null && ($currentGap['start'] < $i && $i < $currentGap['stop'])) {
                $display = false;
            } else if ($gap != null && $gap['start'] == $i) {
                $currentGap = $gap;
                $gap = null;
                $display = false;
            }
            if ($display) {
                $anchor = $this->createAnchor($i, $this->pageClass, $i);
                $anchor->setAttribute('title', $this->translator->translate(self::TRANSLATION_TITLE_PAGE, array('number' => $i)));
                $html .= "\t" . $anchor->getHtml() . "\n";
            }
        }

        return $html;
    }

    /**
     * Get a gap array for the pages in this element
     * @return array Array containing arrays with the start and stop of the gaps in the pagination
     */
    private function getGaps() {
        $gaps = array();
        if ($this->pages <= 10) {
            return $gaps;
        }

        $gap = array();
        if ($this->page < 6) {
            $gap['start'] = 8;
            $gap['stop'] = $this->pages - 1;
        } elseif ($this->page > ($this->pages - 6)) {
            $gap['start'] = 3;
            $gap['stop'] = $this->pages - 7;
        } else {
            $gap['start'] = $this->page + 3;
            $gap['stop'] = $this->pages - 1;
            $gaps[] = $gap;

            $gap = array();
            $gap['start'] = 3;
            $gap['stop'] = $this->page - 2;
        }
        $gaps[] = $gap;

        return $gaps;
    }

    /**
     * Create a new page anchor
     * @param string $label label for the anchor
     * @param string $class style class for the anchor
     * @param int $page page number to link to
     * @return zibo\library\html\Anchor
     */
    private function createAnchor($label, $class, $page) {
        $anchor = new Anchor($label);

        if ($this->onclick) {
            $anchor->setAttribute('onclick', str_replace('%page%', $page, $this->onclick));
        }
        if ($this->href) {
            $anchor->setAttribute('href', str_replace('%page%', $page, $this->href));
        }

        $anchor->setClass($class);

        return $anchor;
    }

}