<?php

namespace joppa\model;

use zibo\library\i18n\I18n;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

/**
 * NodeSettings validator for Joppa node configuration settings
 */
class NodeSettingsValidator {

    /**
     * Validate the Joppa node configuration values
     * @param NodeSettings $nodeSettings
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when a Joppa configuration setting in not validated
     */
    public function validateNodeSettings(NodeSettings $nodeSettings) {
        $validationException = new ValidationException();

        $publishStart = $nodeSettings->get(NodeSettingModel::SETTING_PUBLISH_START, null, false);
        $publishStop = $nodeSettings->get(NodeSettingModel::SETTING_PUBLISH_STOP, null, false);
        $isPublishStartEmpty = empty($publishStart);
        $isPublishStopEmpty = empty($publishStop);
        if (!$isPublishStartEmpty) {
            if ($date = $this->validateDate($publishStart, $validationException, NodeSettingModel::SETTING_PUBLISH_START)) {
                $nodeSettings->set(NodeSettingModel::SETTING_PUBLISH_START, $date);
            }
        }
        if (!$isPublishStopEmpty) {
            if ($date = $this->validateDate($publishStop, $validationException, NodeSettingModel::SETTING_PUBLISH_STOP)) {
                $nodeSettings->set(NodeSettingModel::SETTING_PUBLISH_STOP, $date);
            }
        }
        if (!$isPublishStartEmpty && !$isPublishStopEmpty && $publishStart >= $publishStop) {
            $error = new ValidationError('joppa.error.date.publish.negative', 'Publish stop date cannot be smaller or equal to publish start date');
            $validationException->addErrors(NodeSettingModel::SETTING_PUBLISH_STOP, array($error));
        }

        $permissions = $nodeSettings->get(NodeSettingModel::SETTING_PERMISSIONS, null, false);
        if ($permissions) {
            if ($permissions = $this->validatePermissions($permissions, $validationException)) {
                $nodeSettings->set(NodeSettingModel::SETTING_PERMISSIONS, $permissions);
            }
        }

        if ($validationException->hasErrors()) {
            throw $validationException;
        }
    }

    /**
     * Validate a date configuration value
     * @param string $date date configuration value
     * @param zibo\library\validation\exception\ValidationException $validationException when a ValidationError occures, it will be added to this exception and null will be returned
     * @param string $fieldName name of the field to register possible errors to the ValidationException
     * @return string valid date configuration value
     */
    private function validateDate($date, ValidationException $validationException, $fieldName) {
        try {
            $locale = I18n::getInstance()->getLocale();
            $timestamp = $locale->parseDate($date, NodeSettingModel::DATE_FORMAT);
            return $locale->formatDate($timestamp, NodeSettingModel::DATE_FORMAT);
        } catch (Exception $exception) {
            $error = new ValidationError('joppa.error.date', $exception->getMessage(), array('date' => $date));
            $validationException->addErrors($fieldName, array($error));
        }

        return null;
    }

    /**
     * Validate the permissions configuration value
     * @param string $permissions permissions configuration setting
     * @param zibo\library\validation\exception\ValidationException $validationException when a ValidationError occures, it will be added to this exception and null will be returned
     * @return string valid permissions configuration value
     */
    private function validatePermissions($permissions, ValidationException $validationException) {
        if ($permissions == NodeSettingModel::AUTHENTICATION_STATUS_ANONYMOUS ||
            $permissions == NodeSettingModel::AUTHENTICATION_STATUS_AUTHENTICATED ||
            $permissions == NodeSettingModel::AUTHENTICATION_STATUS_EVERYBODY) {
            return $permissions;
        }

        $configurationString = '';
        $securityManager = SecurityManager::getInstance();
        $permissions = explode(',', $permissions);
        foreach ($permissions as $permission) {
            $permission = trim($permission);
            if ($securityManager->hasPermission($permission)) {
                $configurationString .= ($configurationString ? ',' : '') . $permission;
                continue;
            }
            $error = new ValidationError('joppa.error.permission', 'permission \'%permission%\' does not exist', array('permission' => $permission));
            $validationException->addErrors(NodeSettingModel::SETTING_PERMISSIONS, array($error));
            return null;
        }

        return $configurationString;
    }

}