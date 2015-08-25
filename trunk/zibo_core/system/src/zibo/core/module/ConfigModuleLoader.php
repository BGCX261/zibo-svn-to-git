<?php

namespace zibo\core\module;

use zibo\core\config\Config;
use zibo\core\Zibo;

use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * Implementation of ModuleLoader to get the modules from the Zibo configuration
 */
class ConfigModuleLoader implements ModuleLoader {

    /**
     * Configuration value for the defined modules
     * @var string
     */
    const CONFIG_MODULE = 'module';

    /**
     * Full class name of the module interface
     * @var string
     */
    const INTERFACE_MODULE = 'zibo\\core\\module\\Module';

    /**
     * Loads the defined modules from the Zibo Configuration
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return array Array with the defined modules
     * @throws zibo\ZiboException when a module could not be created
     * @see Module
     * @see CONFIG_MODULE
     */
    public function loadModules(Zibo $zibo) {
        $configModules = $zibo->getConfigValue(self::CONFIG_MODULE);
        if (!$configModules) {
            return array();
        }

        $objectFactory = new ObjectFactory();
        $modules = array();

        $configModules = Config::parseConfigTree($configModules);
        foreach ($configModules as $configKey => $moduleClass) {
            try {
                $modules[] = $objectFactory->create($moduleClass, self::INTERFACE_MODULE);
            } catch (ZiboException $exception) {
                throw new ZiboException('Could not create ' . $moduleClass . ' from the ' . $configKey . ' configuration key', 0, $exception);
            }
        }

        return $modules;
    }

}