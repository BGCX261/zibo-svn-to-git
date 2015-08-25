<?php

namespace zibo\install\model;

use zibo\core\Zibo;

use zibo\install\model\profile\ProfileLoader;
use zibo\install\model\requirement\RequirementLoader;

use zibo\library\i18n\translation\Translator;

use zibo\repository\ClientModule;

/**
 * Install logic for the Zibo system
 */
class Installer {

    /**
     * Configuration key for the requirements
     * @var string
     */
    const CONFIG_REQUIREMENTS = 'install.requirement';

    /**
     * Configuration key for the available languages
     * @var string
     */
    const CONFIG_LANGUAGES = 'install.language';

    /**
     * Instance of Zibo
     * @var zibo\core\Zibo
     */
    private $zibo;

    /**
     * The requirements for the Zibo installation
     * @var array
     */
    private $requirements;

    /**
     * Constructs a new installer object
     * @return null
     */
    public function __construct() {
        $this->zibo = Zibo::getInstance();

        $this->profiles = null;
        $this->requirements = null;
    }

    /**
     * Loads the profile instances from the Zibo configuration
     * @return array Array with the name of the profile as key and the Profile instance as value
     */
    public function getProfiles() {
        if ($this->profiles !== null) {
            return $this->profiles;
        }

        $profileLoader = new ProfileLoader();
        $this->profiles = $profileLoader->loadProfiles();

        return $this->profiles;
    }

    /**
     * Gets the profile with the provided name
     * @param string $name Name of the profile
     * @return zibo\install\model\profile\Profile
     * @throws zibo\ZiboException when the provided profile does not exist
     */
    public function getProfile($name) {
        $profiles = $this->getProfiles();

        if (!array_key_exists($name, $profiles)) {
            throw new ZiboException('Could not get the profile: profile ' . $name . ' does not exist');
        }

        return $this->profiles[$name];
    }

    /**
     * Loads the requirement instances from the Zibo configuration and performs the requirement checks
     * @return array Array with Requirements objects
     */
    public function getRequirements($profile = null) {
        if ($this->requirements === null) {
            $requirementLoader = new RequirementLoader();
            $this->requirements = $requirementLoader->loadRequirements(self::CONFIG_REQUIREMENTS);
        }

        $requirements = $this->requirements;

        if ($profile) {
            $profile = $this->getProfile($profile);
            $profileRequirements = $profile->getRequirements();
            foreach ($profileRequirements as $requirement) {
                $requirements[] = $requirement;
            }
        }

        return $requirements;
    }

    /**
     * Checks if the requirements are met
     * @param string $profile Name of the profile, if provided, the requirements of the profile are also checked
     * @return boolean True if the requirements are met, false otherwise
     */
    public function hasRequirementsMet($profile = null) {
        $requirements = $this->getRequirements($profile);

        foreach ($requirements as $requirement) {
            if (!$requirement->isMet()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets the available languages
     * @return array Array with the language code as key and the localized language name as value
     */
    public function getLanguages() {
        $languages = array(
            'en' => 'English'
        );

        $languages += Zibo::getInstance()->getConfigValue(self::CONFIG_LANGUAGES, array());

        return $languages;
    }

    /**
     * Installs the extra modules of the provided profile
     * @param string $profile The name of the profile
     * @return null
     */
    public function installProfile($profile, $languages) {
        if (!$this->hasRequirementsMet($profile)) {
            throw new ZiboException('Could not install profile ' . $profile . ': The requirements are not met.');
        }

        $client = ClientModule::getClient();
        if (!$client) {
            throw new ZiboException('Could not install profile ' . $profile . ': The repository client is not properly configured.');
        }

        // install the necessairy modules
        $profile = $this->getProfile($profile);
        $modules = $profile->getModules();

        foreach ($modules as $module) {
            $client->installModule($module->getNamespace(), $module->getName());
        }

        // install the extra languages
        foreach ($languages as $languageCode) {
            if ($languageCode == 'en') {
                continue;
            }

            $client->installModule('zibo', 'l10n.' . $languageCode);
        }

        $profile->install();
    }

}