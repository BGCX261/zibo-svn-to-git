<?php

namespace joppa\model;

use zibo\library\widget\model\WidgetProperties;
use zibo\library\String;

/**
 * NodeSettings extended with the WidgetSettings interface to work with the Zibo widget library
 */
class WidgetSettings extends NodeSettings implements WidgetProperties {

    /**
     * id of the widget for who this container acts
     * @var int
     */
	private $widgetId;

	/**
	 * Prefix of the key for the WidgetSetting methods (widget.[widgetId].)
	 * @var string
	 */
	private $widgetSettingPrefix;

	/**
     * Construct this setting container
     * @param int $widgetId id of the widget for who this container acts
     * @param NodeSettings $nodeSettings underlying setting container
     * @return null
	 */
	public function __construct($widgetId, NodeSettings $nodeSettings) {
		$this->node = $nodeSettings->node;
		$this->settings = $nodeSettings->settings;
		$this->inheritedSettings = $nodeSettings->inheritedSettings;
		$this->defaultInherit = NodeTypeFacade::getInstance()->getDefaultInherit($this->node->type);

	    $this->widgetId = $widgetId;
	    $this->widgetSettingPrefix = NodeSettingModel::SETTING_WIDGET . '.' . $this->widgetId . '.';
	}

	/**
	 * Get the id of the widget where the widget settings are generated on
	 * @return int
	 */
	public function getWidgetId() {
	    return $this->widgetId;
	}

	/**
     * Set a settings for the widget
     * @param string $key key of the setting relative to widget.[widgetId].
     * @param mixed $value value for the setting
     * @return null
	 */
	public function setWidgetProperty($key, $value = null) {
	    $key = $this->widgetSettingPrefix . $key;
	    $this->set($key, $value);
	}

	/**
     * Get a setting value for the widget
     * @param string $key key of the setting
     * @param mixed $default default value for when the setting is not set
     * @return mixed setting value of $default if the setting was not set
	 */
    public function getWidgetProperty($key, $default = null) {
        $key = $this->widgetSettingPrefix . $key;
        return $this->get($key, $default);
    }

    /**
     * Clear the settings of this widget
     * @return null
     */
    public function clearWidgetProperties() {
        foreach ($this->settings as $key => $setting) {
            if (String::startsWith($key, $this->widgetSettingPrefix)) {
                unset($this->settings[$key]);
            }
        }
    }

}