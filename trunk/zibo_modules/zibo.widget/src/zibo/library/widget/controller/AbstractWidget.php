<?php

namespace zibo\library\widget\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\Response;
use zibo\core\View;

use zibo\library\widget\model\WidgetProperties;

use \ReflectionException;
use \ReflectionMethod;

/**
 * Abstract implementation of a widget
 */
class AbstractWidget extends AbstractController implements Widget {

    /**
     * The human friendly name of this widget
     * @var string
     */
    private $name;

    /**
     * Path to the icon of this widget
     * @var string
     */
    private $icon;

    /**
     * Flag to see if this widget has implemented properties
     * @var boolean
     */
    private $hasProperties;

    /**
     * Unique identifier of this widget
     * @var string
     */
    protected $identifier;

    /**
     * Code of the locale for the widget request
     * @var string
     */
    protected $locale;

    /**
     * Properties of this widget
     * @var zibo\library\widget\model\WidgetProperties
     */
    protected $properties;

    /**
     * Constructs a new abstract widget
     * @param string $nameTranslation Translation key for the name of the widget
     * @param string $icon Path to the icon of this widget
     * @return null
     */
    public function __construct($nameTranslation, $icon = null) {
        $translator = $this->getTranslator();

        $this->name = $translator->translate($nameTranslation);
        $this->icon = $icon;
    }

    /**
     * Gets the names of the possible request parameters of this widget
     * @return array
     */
    public function getRequestParameters() {
        return array();
    }

    /**
     * Empty implementation of the frontend method
     * @return null
     */
    public function indexAction() {

    }

    /**
     * Gets the human friendly name of the widget
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the path of the icon of the widget
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Sets the unique identifier of the widget
     * @param string $identifier Unique identifier
     * @return null
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Sets the code of the locale for the widget request
     * @param string $locale Code of the locale
     * @return null
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Gets whether the widget has implemented properties
     * @return boolean True if the widget has properties implemented, false otherwise
     */
    public function hasProperties() {
        if ($this->hasProperties !== null) {
            return $this->hasProperties;
        }

        try {
            new ReflectionMethod($this, self::METHOD_PROPERTIES);
            $this->hasProperties = true;
        } catch (ReflectionException $exception) {
            $this->hasProperties = false;
        }

        return $this->hasProperties;
    }

    /**
     * Sets the properties of the widget
     * @param zibo\library\widget\model\WidgetProperties $properties Widget properties
     * @return null
     */
    public function setProperties(WidgetProperties $properties) {
        $this->properties = $properties;
    }

    /**
     * Gets a preview of the set properties
     * @return string
     */
    public function getPropertiesPreview() {
        return '';
    }

    /**
     * Set an Error404View to the response
     * @param zibo\core\View $view View for the 404 response
     * @return null
     */
    protected function setError404(View $view = null) {
        if ($view) {
            $this->response->setView($view);
        }

        $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
    }

}