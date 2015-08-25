<?php

namespace zibo\library\widget\controller;

use zibo\core\Controller;
use zibo\core\Response;
use zibo\core\Request;

use zibo\library\widget\model\WidgetProperties;

/**
 * Interface for a widget: a small independant component
 */
interface Widget extends Controller {

    /**
     * Name of the widget frontend method
     * @var string
     */
    const METHOD_INDEX = 'indexAction';

    /**
     * Name of the widget backend (properties) method
     * @var string
     */
    const METHOD_PROPERTIES = 'propertiesAction';

    /**
     * Gets the name of the widget
     * @return string
     */
    public function getName();

    /**
     * Gets the path to the icon of the widget
     * @return string
     */
    public function getIcon();

    /**
     * Gets the names of the possible request parameters of this widget
     * @return array
     */
    public function getRequestParameters();

    /**
     * Sets the unique identifier of the widget
     * @param string $identifier Unique identifier
     * @return null
     */
    public function setIdentifier($identifier);

    /**
     * Sets the code of the locale for the widget request
     * @param string $locale Code of the locale
     * @return null
     */
    public function setLocale($locale);

    /**
     * Gets whether the widget has implemented a properties action
     * @return boolean True if the widget has a properties action implemented, false otherwise
     */
    public function hasProperties();

    /**
     * Sets the properties of the widget
     * @param zibo\library\widget\model\WidgetProperties $properties Widget properties
     * @return null
     */
    public function setProperties(WidgetProperties $properties);

    /**
     * Gets a preview of the set properties
     * @return string
     */
    public function getPropertiesPreview();

}