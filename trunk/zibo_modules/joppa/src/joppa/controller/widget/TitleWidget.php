<?php

namespace joppa\controller\widget;

use joppa\controller\JoppaWidget;

use joppa\form\widget\TitleWidgetPropertiesForm;

use joppa\view\widget\TitleWidgetPropertiesView;
use joppa\view\widget\TitleWidgetView;

use zibo\library\widget\controller\AbstractWidget;

/**
 * Widget to display the title of the node it's placed on
 */
class TitleWidget extends JoppaWidget {

	/**
	 * Name of the level setting
	 * @var string
	 */
	const PROPERTY_LEVEL = 'level';

	/**
	 * Name of the style id setting
	 * @var string
	 */
	const PROPERTY_STYLE_ID = 'style.id';

	/**
	 * Name of the style class setting
	 * @var string
	 */
	const PROPERTY_STYLE_CLASS = 'style.class';

	/**
	 * Default value for the heading level
	 * @var integer
	 */
	const DEFAULT_LEVEL = 2;

    /**
     * Relative path to the icon of this widget
     * @var string
     */
	const ICON = 'web/images/joppa/widget/title.png';

	/**
	 * Translation key for the name of this widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.widget.title.name';

	/**
	 * Translation key for the heading level
	 * @var string
	 */
    const TRANSLATION_LEVEL = 'joppa.widget.title.label.level';

    /**
	 * Translation key for the style id
	 * @var string
	 */
    const TRANSLATION_STYLE_ID = 'joppa.widget.title.label.style.id';

    /**
	 * Translation key for the style class
	 * @var string
	 */
    const TRANSLATION_STYLE_CLASS = 'joppa.widget.title.label.style.class';

    /**
     * Construct this widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
        $this->setIsCacheable(true);
    }

    /**
     * Sets a title view to the response
     * @return null
     */
    public function indexAction() {
        $level = $this->getLevel();
        $styleClass = $this->getStyleClass();
        $styleId = $this->getStyleId();

    	$node = $this->properties->getNode();
    	$title = $node->name;

    	$view = new TitleWidgetView($title, $level, $styleClass, $styleId);
    	$this->response->setView($view);
    }

    /**
     * Get a preview of the properties of this widget
     * @return string
     */
    public function getPropertiesPreview() {
        $translator = $this->getTranslator();

        $level = $this->getLevel();
        $styleClass = $this->getStyleClass();
        $styleId = $this->getStyleId();

        $preview = $translator->translate(self::TRANSLATION_LEVEL) . ': ' . $level. '<br />';
        if ($styleClass) {
            $preview .= $translator->translate(self::TRANSLATION_STYLE_CLASS) . ': ' . $styleClass. '<br />';
        }
        if ($styleId) {
            $preview .= $translator->translate(self::TRANSLATION_STYLE_ID) . ': ' . $styleId;
        }

        return $preview;
    }

    /**
     * Action to handle and show the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $level = $this->getLevel();
        $styleClass = $this->getStyleClass();
        $styleId = $this->getStyleId();

        $form = new TitleWidgetPropertiesForm($this->request->getBasePath(), $level, $styleClass, $styleId);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $level = $form->getLevel();
                $styleClass = $form->getStyleClass();
                $styleId = $form->getStyleId();

                $this->properties->setWidgetProperty(self::PROPERTY_LEVEL, $level);
                $this->properties->setWidgetProperty(self::PROPERTY_STYLE_CLASS, $styleClass);
                $this->properties->setWidgetProperty(self::PROPERTY_STYLE_ID, $styleId);
                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new TitleWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Gets the level of the heading
     * @return integer
     */
    private function getLevel() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_LEVEL, self::DEFAULT_LEVEL);
    }

    /**
     * Gets the style class
     * @return string
     */
    private function getStyleClass() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_STYLE_CLASS);
    }

    /**
     * Gets the style id
     * @return string
     */
    private function getStyleId() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_STYLE_ID);
    }

}