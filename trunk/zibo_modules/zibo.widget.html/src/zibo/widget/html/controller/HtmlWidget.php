<?php

namespace zibo\widget\html\controller;

use zibo\library\widget\controller\AbstractWidget;
use zibo\library\String;

use zibo\widget\html\form\HtmlWidgetPropertiesForm;
use zibo\widget\html\view\HtmlWidgetPropertiesView;
use zibo\widget\html\view\HtmlWidgetView;

/**
 * Widget for a HTML block
 */
class HtmlWidget extends AbstractWidget {

    /**
     * Translation key for the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'widget.html.name';

    /**
     * Name of the HTML property
     * @var string
     */
    const PROPERTY_HTML = 'html';

    /**
     * Constructs a new HTML widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME);
    }

    /**
     * Action to show the set HTML
     * @return null
     */
    public function indexAction() {
        $view = new HtmlWidgetView($this->getHtml());
        $this->response->setView($view);
    }

    /**
     * Gets a preview of the set HTML
     * @return string
     */
    public function getPropertiesPreview() {
        return String::getPreviewString($this->getHtml());
    }

    /**
     * Action to edit the HTML of this widget
     * @return boolean True if the properties have been changed, false otherwise
     */
    public function propertiesAction() {
        $form = new HtmlWidgetPropertiesForm($this->request->getBasePath(), $this->locale, $this->getHtml());
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            $locale = $form->getLocale();
            $html = $form->getContent();

            $this->setHtml($locale, $html);

            $this->response->setRedirect($this->request->getBaseUrl());
            return true;
        }

        $view = new HtmlWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Gets the HTML from the widget properties
     * @return string HTML
     */
    private function getHtml() {
        $html = $this->properties->getWidgetProperty($this->locale . '.' . self::PROPERTY_HTML);

        if (!$html) {
            $html = $this->properties->getWidgetProperty(self::PROPERTY_HTML);
        }

        return $html;
    }

    /**
     * Sets the HTML to the widget properties
     * @param string $locale Locale code
     * @param string $html HTML to set
     * @return null
     */
    private function setHtml($locale, $html) {
        $this->properties->setWidgetProperty($locale . '.' . self::PROPERTY_HTML, $html);
    }

}