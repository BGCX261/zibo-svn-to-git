<?php

namespace joppa\model;

use zibo\library\i18n\I18n;
use zibo\library\security\SecurityManager;
use zibo\library\DateTime;
use zibo\library\String;
use zibo\library\Structure;

use zibo\ZiboException;

/**
 * Container of NodeSetting objects of a Node
 */
class NodeSettings {

	/**
	 * Prefix for a setting key in the INI format when it should inherit to lower levels
	 * @var string
	 */
    const INHERIT_PREFIX = '_';

    /**
     * The node who owns the settings contained in this object
     * @var Node
     */
	protected $node;

	/**
	 * Array containing NodeSetting objects as value and the setting key as key
	 * @var array
	 */
	protected $settings;

	/**
	 * NodeSetting container of the inherited settings
	 * @var NodeSettings
	 */
	protected $inheritedSettings;

	/**
	 * Default value for the the inherit field
	 * @var boolean
	 */
	protected $defaultInherit;

    /**
     * Variable to cache the isPublished value
     * @var boolean
     */
    private $isPublished;

	/**
     * Construct the NodeSetting container
     * @param Node $node the node who owns the settings in this object
     * @param NodeSettings $inheritedSettings NodeSetting container to inherit from (optional)
     * @return null
	 */
	public function __construct(Node $node, NodeSettings $inheritedSettings = null) {
        $this->node = $node;
        $this->settings = array();
        $this->inheritedSettings = $inheritedSettings;
        $this->defaultInherit = NodeTypeFacade::getInstance()->getDefaultInherit($node->type);
	}

	/**
	 * Get the node who owns the settings in this object
	 * @return Node
	 */
	public function getNode() {
		$node = clone($this->node);
		$node->settings = $this;

		return $node;
	}

	/**
     * Set a node setting to this container
     * @param NodeSetting $setting setting to set
     * @return null
     * @throws zibo\ZiboException when an invalid key is provided
	 */
	public function setNodeSetting(NodeSetting $setting) {
	    $this->checkKey($setting->key);

		$setting->node = $this->node->id;
		$this->settings[$setting->key] = $setting;
	}

	/**
     * Get a node setting from this container
     * @param string $key key of the node setting
     * @return NodeSetting
     * @throws zibo\ZiboException when an invalid key is provided
	 */
	public function getNodeSetting($key) {
	    $this->checkKey($key);

		if (array_key_exists($key, $this->settings)) {
			return $this->settings[$key];
		}

		return null;
	}

	/**
     * Set a value to this setting container
     * @param string $key key for the setting
     * @param string $value value of the setting, null to unset the setting
     * @param boolean $inherit true to inherit this setting to lower levels, false to not inherit and null to use the previous inherit state
     * @return null
     * @throws zibo\ZiboException when an invalid key is provided
	 */
	public function set($key, $value = null, $inherit = null) {
	    $this->checkKey($key);

	    if ($value === null) {
	        if (array_key_exists($key, $this->settings)) {
    	        unset($this->settings[$key]);
	        }
	        return;
	    }

	    $inheritPrefixLength = strlen(self::INHERIT_PREFIX);
        if (strlen($key) > $inheritPrefixLength && strncmp($key, self::INHERIT_PREFIX, $inheritPrefixLength) == 0) {
            $key = substr($key, $inheritPrefixLength);
            $inherit = true;
        } elseif ($inherit === null) {
            if (array_key_exists($key, $this->settings)) {
                $inherit = $this->settings[$key]->inherit;
            } else {
                $inherit = $this->defaultInherit;
            }
        }

        if (array_key_exists($key, $this->settings)) {
        	$setting = $this->settings[$key];
        } else {
	        $setting = new NodeSetting();
    	    $setting->key = $key;
        }

        $setting->value = $value;
        $setting->inherit = $inherit;

        $this->setNodeSetting($setting);
	}

	/**
     * Get a value for a setting key
     * @param string $key key of the setting
     * @param mixed $default default value for when the setting is not set
     * @param boolean $inherited true to look in inherited settings, false to only look in this container
     * @param boolean $inheritedSettingRequired true to only return the value if the setting will inherit, needed internally for recursive lookup
     * @return mixed the value of the settings key if found, the provided default value otherwise
     * @throws zibo\ZiboException when an invalid key is provided
	 */
	public function get($key, $default = null, $inherited = true, $inheritedSettingRequired = false) {
	    $this->checkKey($key);

		if (array_key_exists($key, $this->settings) && (!$inheritedSettingRequired || ($inheritedSettingRequired && $this->settings[$key]->inherit))) {
			return $this->settings[$key]->value;
		}

		if ($inherited && $this->inheritedSettings) {
    		return $this->inheritedSettings->get($key, $default, true, true);
		}

		return $default;
	}

    /**
     * Check whether this node of these is published
     * @return boolean true if this node is published, false if not
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    public function isPublished() {
        if ($this->isPublished !== null) {
            return $this->isPublished;
        }

        $this->isPublished = $this->get(NodeSettingModel::SETTING_PUBLISH, false);
        if (!$this->isPublished) {
            return $this->isPublished;
        }

        $this->isPublished = false;

        $locale = I18n::getInstance()->getLocale();
        $today = DateTime::roundTimeToDay();
        $publishStart = $this->get(NodeSettingModel::SETTING_PUBLISH_START);
        $publishStop = $this->get(NodeSettingModel::SETTING_PUBLISH_STOP);

        if ($publishStart && $publishStop) {
            $publishStart = $locale->parseDate($publishStart, NodeSettingModel::DATE_FORMAT);
            $publishStop = $locale->parseDate($publishStop, NodeSettingModel::DATE_FORMAT);
            if ($publishStart <= $today && $today < $publishStop) {
                $this->isPublished = true;
            }
        } elseif ($publishStart) {
            $publishStart = $locale->parseDate($publishStart, NodeSettingModel::DATE_FORMAT);
            if ($publishStart <= $today) {
                $this->isPublished = true;
            }
        } elseif ($publishStop) {
            $publishStop = $locale->parseDate($publishStop, NodeSettingModel::DATE_FORMAT);
            if ($today < $publishStop) {
                $this->isPublished = true;
            }
        } else {
            $this->isPublished = true;
        }

        return $this->isPublished;
    }

    /**
     * Check whether the node of these settings is secured in any way
     * @return boolean true if this node is secured, false if not
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    public function isSecured() {
        $permissions = $this->get(NodeSettingModel::SETTING_PERMISSIONS);
        if (!$permissions || $permissions == NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the node of these settings is allowed for the current user
     * @return boolean true if this node is allowed for the current user, false if not
     * @throws zibo\ZiboException when the NodeSettings are not set to this node
     */
    public function isAllowed() {
        $securityManager = SecurityManager::getInstance();
        $user = $securityManager->getUser();
        $permissions = $this->get(NodeSettingModel::SETTING_PERMISSIONS);

        if (!$permissions || $permissions === NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY) {
            return true;
        }

        if ($permissions === NodeSettingModel::AUTHENTICATION_STATUS_ANONYMOUS) {
            if ($user === null) {
                return true;
            }
            return false;
        }
        if ($permissions === NodeSettingModel::AUTHENTICATION_STATUS_AUTHENTICATED) {
            if ($user === null) {
                return false;
            }
            return true;
        }

        $permissions = explode(',', $permissions);
        $isAllowed = true;
        foreach ($permissions as $permission) {
            if (!$securityManager->isPermissionAllowed($permission)) {
                $isAllowed = false;
                break;
            }
        }

        return $isAllowed;
    }

    /**
     * Gets whether this node is available in the provided locale
     * @param string $locale Code of the locale
     * @return boolean True if the node is available in the provided locale, false otherwise
     */
    public function isAvailableInLocale($locale) {
    	$availableLocales = $this->get(NodeSettingModel::SETTING_LOCALES);

    	if ($availableLocales === NodeSettingModel::LOCALES_ALL) {
    		return true;
    	}

    	$availableLocales = explode(NodeSettingModel::LOCALES_SEPARATOR, $availableLocales);

    	foreach ($availableLocales as $availableLocale) {
    		$availableLocale = trim($availableLocale);
    		if ($availableLocale === $locale) {
    			return true;
    		}
    	}

    	return false;
    }

	/**
	 * Get the inherited settings
	 * @return NodeSettings
	 */
    public function getInheritedNodeSettings() {
        return $this->inheritedSettings;
    }

    /**
     * Get an array with the settings of this object
     * @param boolean $includeInheritedSettings true to include the inherited settings in the array
     * @param boolean $onlyInheritSettings true to include only settings which inherit to lower levels
     * @return array Array with the setting key as key and a NodeSetting instance as value
     */
    public function getArray($includeInheritedSettings = false, $inheritedSettingRequired = false) {
        $array = $this->settings;
        if ($inheritedSettingRequired) {
            foreach ($array as $key => $nodeSetting) {
                if (!$nodeSetting->inherit) {
                    unset($array[$key]);
                }
            }
        }

        $inheritedSettings = $this->getInheritedNodeSettings();
        if (!$includeInheritedSettings || $inheritedSettings == null) {
            return $array;
        }

        $inheritedArray = $inheritedSettings->getArray($includeInheritedSettings, $inheritedSettingRequired);

        return Structure::merge($inheritedArray, $array);
    }

    /**
     * Get a INI string for the settings of this object
     * @param boolean $includeInheritedSettings true to include the inherited settings in the array
     * @param boolean $inheritedSettingRequired true to include only settings which inherit to lower levels
     * @return string
     */
	public function getIniString($includeInheritedSettings = false, $inheritedSettingRequired = false) {
        $settings = $this->getArray($includeInheritedSettings, $inheritedSettingRequired);
        ksort($settings);

        $ini = '';
        foreach ($settings as $key => $nodeSetting) {
            $ini .= $nodeSetting->getIniString() . "\n";
        }

        return $ini;
	}

	/**
	 * Checks whether a key is a non empty string
	 * @param mixed $key key for a setting
	 * @return null
	 * @throws zibo\ZiboException when the key is invalid
	 */
	private function checkKey($key) {
	    try {
	        if (String::isEmpty($key)) {
	            throw new ZiboException('Key is empty');
	        }
	    } catch (ZiboException $e) {
	        throw new ZiboException('Provided key is invalid: ' . $e->getMessage());
	    }
	}

}