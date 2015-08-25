<?php

namespace zibo\install\model\requirement;

use zibo\core\Zibo;

use zibo\library\ObjectFactory;

/**
 * Loader for installation requirements
 */
class RequirementLoader {

    /**
     * Class name of the requirement interface
     * @var string
     */
    const INTERFACE_REQUIREMENT = 'zibo\\install\\model\\requirement\\Requirement';

    /**
     * Loads a set of requirements from the Zibo configuration. The checks of the requirements are also invoked
     * @param string $configKey The configuration key for the requirement set
     * @return array Array with Requirement instances
     */
    public function loadRequirements($configKey) {
        $requirements = array();

        $configRequirements = Zibo::getInstance()->getConfigValue($configKey);
        if (!$configRequirements) {
            return $requirements;
        }

        if (!is_array($configRequirements)) {
            $configRequirements = array($configRequirements);
        }

        $objectFactory = new ObjectFactory();
        foreach ($configRequirements as $requirementClassName) {
            $requirement = $objectFactory->create($requirementClassName, self::INTERFACE_REQUIREMENT);
            $requirement->performCheck();

            $requirements[] = $requirement;
        }

        return $requirements;
    }

}