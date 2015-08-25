<?php

namespace joppa\text\controller;

use joppa\controller\JoppaWidget;

use joppa\text\form\TextPropertiesForm;
use joppa\text\model\data\TextData;
use joppa\text\model\TextModel;
use joppa\text\view\TextView;
use joppa\text\view\TextPropertiesView;

use zibo\library\validation\exception\ValidationException;
use zibo\library\String;

/**
 * Widget to show a text
 */
class TextWidget extends JoppaWidget {

    /**
     * Relative path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/text.png';

    /**
     * Setting key of the node where the search form will point to
     * @var string
     */
    const PROPERTY_TEXT = 'text';

    /**
     * Translation key of the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.text';

    /**
     * Hook with the orm module
     * @var string
     */
    public $useModels = TextModel::NAME;

    /**
     * Construct this widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
        $this->setIsCacheable(true);
    }

    /**
     * Action to set the text to the response
     * @return null
     */
    public function indexAction() {
        $text = $this->getText();

        $view = new TextView($text);
        $this->response->setView($view);
    }

    /**
     * Get a preview of the properties of this widget
     * @return string
     */
    public function getPropertiesPreview() {
        $text = $this->getText();

        $text = htmlentities($text->text);
        $text = String::truncate($text, 120);
        $text = nl2br($text);

        return $text;
    }

    /**
     * Action to show and handle the properties of this widget
     * @return null
     */
    public function propertiesAction($version = null) {
    	$basePath = $this->request->getBasePath();
        $text = $this->getText($version);

        $form = new TextPropertiesForm($basePath, $text);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
            	$text = $form->getText();

            	$this->setText($text);

            	$this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $exception) {
            	$form->setValidationException($exception);
            }
        }

        if ($text->id) {
            $history = $this->models[TextModel::NAME]->getTextHistory($text, $this->locale);
        } else {
        	$history = array();
        }

        $view = new TextPropertiesView($form, $history, $basePath . '/', $text->version);
        $this->response->setView($view);

        return false;
    }

    /**
     * Gets the text of this widget
     * @return joppa\text\model\TextData
     */
    private function getText($version = null) {
    	$text = null;

        $textId = $this->properties->getWidgetProperty(self::PROPERTY_TEXT);
        if ($textId) {
	        if ($version !== null) {
	           $text = $this->models[TextModel::NAME]->getTextVersion($textId, $version, $this->locale);
	        } else {
	           $text = $this->models[TextModel::NAME]->findById($textId, 0, $this->locale, true);
	        }
        }

        if (!$text) {
            $text = $this->models[TextModel::NAME]->createData();
        }

        return $text;
    }

    /**
     * Saves the text of this widget
     * @param joppa\text\model\TextData $text
     * @return null
     */
    private function setText(TextData $text) {
    	if ($text->id) {
    		$currentText = $this->getText();
    		$text->version = $currentText->version;
    	}

    	$text->dataLocale = $this->locale;

    	$this->models[TextModel::NAME]->save($text);

    	$this->properties->setWidgetProperty(self::PROPERTY_TEXT, $text->id);
    }

}