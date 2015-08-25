<?php

namespace zibo\library\orm\model\data\format\modifier;

use zibo\core\Zibo;

use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * Facade for the data format modifiers
 */
class DataFormatModifierFacade {

    /**
     * Configuration key for the definition of the modifiers
     * @var string
     */
    const CONFIG_MODIFIERS = 'orm.data.format.modifier';

    /**
     * Class name of the data format modifier interface
     * @var string
     */
    const INTERFACE_MODIFIER = 'zibo\\library\\orm\\model\\data\\format\\modifier\\DataFormatModifier';

    /**
     * Instance of the facade
     * @var DataFormatModifierFacade
     */
    private static $instance;

    /**
     * Array with the name of the modifier as key and the class name or an instance of the modifier as value
     * @var array
     */
    private $modifiers;

    /**
     * Constructs the factory
     * @return null
     */
    private function __construct() {
        $this->loadModifiers();
    }

    /**
     * Gets the instance of this facade
     * @return DataFormatModifierFacade
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Applies a modifier on the provided value
     * @param string $value Value to modify
     * @param string $name Name of the modifier
     * @param array $arguments Arguments for the modifier
     * @return string Modified value
     */
    public function modifyValue($value, $name, array $arguments = null) {
        $modifier = $this->getModifier($name);

        if ($arguments === null) {
            $arguments = array();
        }

        return $modifier->modifyValue($value, $arguments);
    }

    /**
     * Gets a modifier
     * @param string $name Name of the modifier
     * @return DataFormatModifier
     */
    private function getModifier($name) {
        if (!array_key_exists($name, $this->modifiers)) {
            throw new ZiboException('No modifier found for name ' . $name);
        }

        return $this->modifiers[$name];
    }

    /**
     * Loads the modifiers from the Zibo configuration
     * @return null
     */
    private function loadModifiers() {
        $this->modifiers = array();

        $objectFactory = new ObjectFactory();

        $modifiers = Zibo::getInstance()->getConfigValue(self::CONFIG_MODIFIERS);
        foreach ($modifiers as $name => $className) {
            $this->modifiers[$name] = $objectFactory->create($className, self::INTERFACE_MODIFIER);
        }
    }

}