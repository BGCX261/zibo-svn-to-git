<?php

namespace zibo\install\model\profile;

use zibo\core\Zibo;

use zibo\library\ObjectFactory;

/**
 * Loader for installation profiles
 */
class ProfileLoader {

    /**
     * Configuration key for the profiles
     * @var string
     */
    const CONFIG_PROFILES = 'install.profile';

    /**
     * Class name of the profile interface
     * @var string
     */
    const INTERFACE_PROFILE = 'zibo\\install\\model\\profile\\Profile';

    /**
     * Loads the profiles from the Zibo configuration.
     * @return array Array with Profile instances
     */
    public function loadProfiles() {
        $profiles = array();

        $configProfiles = Zibo::getInstance()->getConfigValue(self::CONFIG_PROFILES);
        if (!$configProfiles) {
            return $profiles;
        }

        if (!is_array($configProfiles)) {
            $configProfiles = array($configProfiles);
        }

        $objectFactory = new ObjectFactory();
        foreach ($configProfiles as $name => $profileClassName) {
            $profile = $objectFactory->create($profileClassName, self::INTERFACE_PROFILE);
            $profiles[$name] = $profile;
        }

        return $profiles;
    }

}