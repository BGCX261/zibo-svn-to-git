<?php

namespace joppa\form\backend;

use joppa\model\NodeSettings;
use joppa\model\NodeSettingModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\I18n;
use zibo\library\security\SecurityManager;

use zibo\ZiboException;

/**
 * Form to manage the visibility properties of a node
 */
class NodeVisibilityForm extends Form {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formNodeVisibility';

	/**
	 * Name of the published field
	 * @var string
	 */
	const FIELD_PUBLISHED = 'published';

	/**
	 * Name of the publish start field
	 * @var string
	 */
	const FIELD_PUBLISH_START = 'publishStart';

	/**
	 * Name of the publish stop field
	 * @var string
	 */
	const FIELD_PUBLISH_STOP = 'publishStop';

	/**
	 * Name of the authentication status field
	 * @var string
	 */
	const FIELD_AUTHENTICATION_STATUS = 'authenticationStatus';

	/**
	 * Name of the permissions foemd
	 * @var string
	 */
	const FIELD_PERMISSIONS = 'permissions';

	/**
	 * Name of the save button
	 * @var string
	 */
	const FIELD_SAVE = 'save';

	/**
	 * Name of the cancel button
	 * @var string
	 */
	const FIELD_CANCEL = 'cancel';

	/**
	 * Value for the inherit option
	 * @var string
	 */
	const INHERIT = 'inherit';

	/**
	 * Value for the published option
	 * @var string
	 */
	const PUBLISH_YES = 1;

	/**
	 * Value for the not published option
	 * @var string
	 */
	const PUBLISH_NO = 2;

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Translation key for the cancel button
	 * @var string
	 */
	const TRANSLATION_CANCEL= 'button.cancel';

	/**
	 * Translation key for inherit
	 * @var string
	 */
	const TRANSLATION_INHERIT = 'joppa.label.inherit';

	/**
	 * Translation key for yes
	 * @var string
	 */
	const TRANSLATION_YES = 'label.yes';

	/**
	 * Translation key for no
	 * @var string
	 */
	const TRANSLATION_NO = 'label.no';

	/**
	 * Translation key for the everybody authentication status
	 * @var string
	 */
	const TRANSLATION_EVERYBODY = 'joppa.label.everybody';

	/**
	 * Translation key for the anonymous authentication status
	 * @var string
	 */
	const TRANSLATION_ANONYMOUS = 'joppa.label.anonymous';

	/**
	 * Translation key for the authenticated authentication status
	 * @var string
	 */
	const TRANSLATION_AUTHENTICATED = 'joppa.label.authenticated';

	/**
	 * The initial node settings
	 * @var joppa\model\NodeSettings
	 */
	private $nodeSettings;

	/**
     * Construct this form
     * @param string $action url where this form will point to
     * @param joppa\model\NodeSettings $settings
     * @return null
	 */
	public function __construct($action, NodeSettings $settings) {
		parent::__construct($action, self::NAME);

		$this->nodeSettings = $settings;

		$factory = FieldFactory::getInstance();
		$translator = I18n::getInstance()->getTranslator();

		$publishedField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_PUBLISHED);
		$publishedField->setOptions($this->getPublishedOptions($translator));

		$authenticationStatusField = $factory->createField(FieldFactory::TYPE_OPTION, self::FIELD_AUTHENTICATION_STATUS);
		$authenticationStatusField->setOptions($this->getAuthenticationStatusOptions($translator));

		$permissionsField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_PERMISSIONS);
		$permissionsField->setOptions($this->getPermissionOptions());
		$permissionsField->setIsMultiple(true);

		$this->addField($publishedField);
		$this->addField($factory->createField(FieldFactory::TYPE_DATE, self::FIELD_PUBLISH_START));
		$this->addField($factory->createField(FieldFactory::TYPE_DATE, self::FIELD_PUBLISH_STOP));
        $this->addField($authenticationStatusField);
        $this->addField($permissionsField);
        $this->addField($factory->createSubmitField(self::FIELD_SAVE, 'button.save'));
        $this->addField($factory->createSubmitField(self::FIELD_CANCEL, 'button.cancel'));

        $this->setValues($this->nodeSettings);
	}

    /**
     * Get the node settings from this form
     * @param boolean $updateValues true to get the submitted node settings, false to get the initial node settings
     * @return joppa\model\NodeSettings
     * @throws zibo\ZiboException when the form is not submitted and $updateValues is set to true
     */
	public function getNodeSettings($updateValues = true) {
		if (!$updateValues) {
    		return $this->nodeSettings;
		}

        if (!$this->isSubmitted()) {
            throw new ZiboException('Form is not submitted');
        }

		$nodeSettings = clone($this->nodeSettings);
		$this->getValues($nodeSettings);

		return $nodeSettings;
	}

    /**
     * Set the values from the NodeSettings to this form
     * @param joppa\model\NodeSettings $nodeSettings
     * @return null
     */
	private function setValues(NodeSettings $nodeSettings) {
	    $published = $nodeSettings->get(NodeSettingModel::SETTING_PUBLISH, null, false);
	    $publishStart = $nodeSettings->get(NodeSettingModel::SETTING_PUBLISH_START, null, false);
	    $publishStop = $nodeSettings->get(NodeSettingModel::SETTING_PUBLISH_STOP, null, false);
        $permissions = $nodeSettings->get(NodeSettingModel::SETTING_PERMISSIONS, null, false);

        $published = $this->getFormPublished($published, $nodeSettings);
        $publishStart = $this->getFormDate($publishStart);
        $publishStop = $this->getFormDate($publishStop);
        $authenticationStatus = $this->getFormAuthenticationStatus($permissions, $nodeSettings);
        $permissions = $this->getFormPermissions($permissions);

		$this->setValue(self::FIELD_PUBLISHED, $published);
		$this->setValue(self::FIELD_PUBLISH_START, $publishStart);
		$this->setValue(self::FIELD_PUBLISH_STOP, $publishStop);
		$this->setValue(self::FIELD_AUTHENTICATION_STATUS, $authenticationStatus);
		$this->setValue(self::FIELD_PERMISSIONS, $permissions);
	}

	/**
	 * Set the values from this form to the NodeSettings
	 * @param joppa\model\NodeSettings $nodeSettings container to set the values from this form to
	 * @return null
	 */
	private function getValues(NodeSettings $nodeSettings) {
	    $published = $this->getValue(self::FIELD_PUBLISHED);
	    if ($published == self::INHERIT) {
	        $published = null;
	    } elseif ($published == self::PUBLISH_YES) {
	        $published = 1;
	    } else {
	        $published = 0;
	    }

	    $publishStart = $this->getValue(self::FIELD_PUBLISH_START);
	    $publishStop = $this->getValue(self::FIELD_PUBLISH_STOP);
	    $publishStart = $this->getConfigurationDate($publishStart);
	    $publishStop = $this->getConfigurationDate($publishStop);

	    $authenticationStatus = $this->getValue(self::FIELD_AUTHENTICATION_STATUS);
	    $permissions = $this->getValue(self::FIELD_PERMISSIONS);
	    $permissions = $this->getConfigurationPermissions($authenticationStatus, $permissions);

		$nodeSettings->set(NodeSettingModel::SETTING_PUBLISH, $published);
		$nodeSettings->set(NodeSettingModel::SETTING_PUBLISH_START, $publishStart);
		$nodeSettings->set(NodeSettingModel::SETTING_PUBLISH_STOP, $publishStop);
		$nodeSettings->set(NodeSettingModel::SETTING_PERMISSIONS, $permissions);
	}

	/**
     * Get the value for the published field
     * @param boolean $published published configuration value
     * @param joppa\model\NodeSettings $nodeSettings the NodeSettings where the published configuration value came from
     * @return string value for the published field
	 */
	private function getFormPublished($published, NodeSettings $nodeSettings) {
        if ($published === null) {
            if ($nodeSettings->getInheritedNodeSettings()) {
                $published = self::INHERIT;
            } else {
                $published = self::PUBLISH_NO;
            }
        } elseif (!$published) {
            $published = self::PUBLISH_NO;
        } else {
            $published = self::PUBLISH_YES;
        }

        return $published;
	}

	/**
     * Parse a date into a timestamp
     * @param string date date in the format as used by the Joppa configuration
     * @param int timestamp of the date
	 */
	private function getFormDate($date) {
        try {
            $locale = I18n::getInstance()->getLocale();
            return $locale->parseDate($date, NodeSettingModel::DATE_FORMAT);
        } catch (ZiboException $e) {
            return null;
        }
	}

	/**
     * Get the value for the authentication status field from the permission configuration value
     * @param string $permissions permission configuration value
     * @param joppa\model\NodeSettings $nodeSettings the NodeSettings where the permission configuration value came from
     * @return string value for the authentication status field
	 */
	private function getFormAuthenticationStatus($permissions, NodeSettings $nodeSettings) {
		if ($permissions == null) {
		    if (!$nodeSettings->getInheritedNodeSettings()) {
        		return NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY;
		    } else {
                return self::INHERIT;
            }
		}

		if ($permissions == NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY ||
			$permissions == NodeSettingModel::AUTHENTICATION_STATUS_ANONYMOUS) {
			return $permissions;
		}

		return NodeSettingModel::AUTHENTICATION_STATUS_AUTHENTICATED;
	}

	/**
     * Get the value for the permissions field from the permission configuration value
     * @param string $permissions permission configuration value
     * @return array array with security permissions if there are permissions set to the configuration value
	 */
	private function getFormPermissions($permissions) {
		if ($permissions === null ||
			$permissions == NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY ||
			$permissions == NodeSettingModel::AUTHENTICATION_STATUS_ANONYMOUS ||
			$permissions == NodeSettingModel::AUTHENTICATION_STATUS_AUTHENTICATED) {
			return null;
		}

		$formPermissions = array();
		$permissions = explode(',', $permissions);
		foreach ($permissions as $permission) {
			$formPermissions[$permission] = $permission;
		}

		return $formPermissions;
	}

	/**
     * Parse a timestamp into the date format as used by the Joppa configuration
     * @param int $date timestamp of a date
     * @return string the timestamp in the date format as used by the Joppa configuration
	 */
	private function getConfigurationDate($date) {
        if (empty($date)) {
            return null;
        }
        return I18n::getInstance()->getLocale()->formatDate($date, NodeSettingModel::DATE_FORMAT);
	}

	/**
     * Get the configuration value for the permissions setting
     * @param string $formAuthenticationStatus authentication status taken from this form
     * @param array $formPermissions array of permission code taken from this form
     * @return string configuration value for the permissions setting
	 */
	private function getConfigurationPermissions($formAuthenticationStatus, $formPermissions) {
		if ($formAuthenticationStatus == self::INHERIT) {
			return null;
		}
		if ($formAuthenticationStatus != NodeSettingModel::AUTHENTICATION_STATUS_AUTHENTICATED) {
			return $formAuthenticationStatus;
		}
		if (empty($formPermissions)) {
			return $formAuthenticationStatus;
		}
		return implode(',', $formPermissions);
	}

    /**
     * Get the publish options
     * @param zibo\library\i18n\translation\Translator $translator
     * @return array Array with the publish code as key and the translation as value
     */
	private function getPublishedOptions(Translator $translator) {
	    $options = array();

	    $inheritedNodeSettings = $this->nodeSettings->getInheritedNodeSettings();
        if ($inheritedNodeSettings) {
            $options[self::INHERIT] = $translator->translate(self::TRANSLATION_INHERIT) . $this->getPublishedInheritSuffix($inheritedNodeSettings, $translator);
	    }

        $options[self::PUBLISH_YES] = $translator->translate(self::TRANSLATION_YES);
        $options[self::PUBLISH_NO] = $translator->translate(self::TRANSLATION_NO);

        return $options;
	}

    /**
     * Get a suffix for the publish inherit label based on the inherited settings
     * @param joppa\model\NodeSettings $nodeSettings
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string if a publish setting is found the suffix will be " (Yes)" or " (No)"
     */
    private function getPublishedInheritSuffix(NodeSettings $nodeSettings, Translator $translator) {
        $published = $nodeSettings->get(NodeSettingModel::SETTING_PUBLISH, NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY, true, true);
        if ($published == null) {
            return '';
        }

        $suffix = ' (';
        if ($published) {
            $suffix .= $translator->translate(self::TRANSLATION_YES);
        } else {
            $suffix .= $translator->translate(self::TRANSLATION_NO);
        }
        $suffix .= ')';

        return $suffix;
    }

	/**
	 * Get the authentication options
     * @param zibo\library\i18n\translation\Translator $translator
	 * @return array Array with the authentication status as key and the translation as value
	 */
	private function getAuthenticationStatusOptions(Translator $translator) {
        $options = array();

        $inheritedNodeSettings = $this->nodeSettings->getInheritedNodeSettings();
        if ($inheritedNodeSettings) {
            $options[self::INHERIT] = $translator->translate(self::TRANSLATION_INHERIT) . $this->getPermissionInheritSuffix($inheritedNodeSettings, $translator);
        }

        $options[NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY] = $translator->translate(self::TRANSLATION_EVERYBODY);
        $options[NodeSettingModel::AUTHENTICATION_STATUS_ANONYMOUS] = $translator->translate(self::TRANSLATION_ANONYMOUS);
        $options[NodeSettingModel::AUTHENTICATION_STATUS_AUTHENTICATED] = $translator->translate(self::TRANSLATION_AUTHENTICATED);

        return $options;
	}

	/**
	 * Get the permissions from the SecurityManager for the permissions field
	 * @return array Array with the permission codes as key and value
	 */
	private function getPermissionOptions() {
		$options = array();

		$permissions = SecurityManager::getInstance()->getPermissions();
		foreach ($permissions as $permission) {
			$code = $permission->getPermissionCode();
			$options[$code] = $code;
		}

		return $options;
	}


    /**
     * Get a suffix for the permission inherit label based on the inherited settings
     * @param joppa\model\NodeSettings $nodeSettings
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string if a permission setting is found the suffix will be " ($securityLevel)"
     */
    private function getPermissionInheritSuffix(NodeSettings $nodeSettings, Translator $translator) {
        $permission = $nodeSettings->get(NodeSettingModel::SETTING_PERMISSIONS, NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY, true, true);
        if ($permission == null) {
            return '';
        }

        $suffix = ' (';
        if ($permission == NodeSettingModel::AUTHENTICATION_STATUS_ANONYMOUS) {
            $suffix .= $translator->translate(self::TRANSLATION_ANONYMOUS);
        } elseif ($permission == NodeSettingModel::AUTHENTICATION_STATUS_AUTHENTICATED) {
            $suffix .= $translator->translate(self::TRANSLATION_AUTHENTICATED);
        } elseif ($permission == NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY) {
            $suffix .= $translator->translate(self::TRANSLATION_EVERYBODY);
        } else {
            $suffix .= $permission;
        }
        $suffix .= ')';

        return $suffix;
    }

}