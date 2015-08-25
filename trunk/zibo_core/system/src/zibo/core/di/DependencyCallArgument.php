<?php

namespace zibo\core\di;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Definition of a argument for a depenency callback
 */
class DependencyCallArgument {

    /**
     * Type for a null value
     * @var string
     */
    const TYPE_CONFIG = 'config';

    /**
     * Type for a dependency value
     * @var string
     */
    const TYPE_DEPENDENCY = 'dependency';

    /**
     * Type for a null value
     * @var string
     */
    const TYPE_NULL = 'null';

    /**
     * Type for a primitive value
     * @var string
     */
    const TYPE_VALUE = 'value';

    /**
     * The type of this argument
     * @var string
     */
    protected $type;

    /**
     * The value of this type
     * @var mixed
     */
    protected $value;

    /**
     * The id of the value of the type is dependency
     * @var string
     */
    protected $dependencyId;

    /**
     * The default value for if the type is config
     * @var string
     */
    protected $defaultValue;

    /**
     * Constructs a new dependency callback argument
     * @param string $type The type of this argument
     * @param string $value The value for this argument
     * @param string $extra The id of the dependency, only if the type is
     * dependency. If the type is config, the default configuration value.
     * @return null
     */
    public function __construct($type = self::TYPE_NULL, $value = null, $extra = null) {
        $this->setValue($type, $value, $extra);
    }

    /**
     * Sets the value for this argument
     * @param string $type The type of the value
     * @param string $value The value
     * @param string $extra The id of the dependency, only if the type is
     * dependency. If the type is config, the default configuration value.
     * @return null
     * @throws zibo\ZiboException when invalid arguments provided
     */
    public function setValue($type, $value = null, $extra = null) {
        if ($type != self::TYPE_NULL && $type != self::TYPE_VALUE && $type != self::TYPE_DEPENDENCY && $type != self::TYPE_CONFIG) {
            throw new ZiboException('Invalid argument type provided, try null, value, dependency or config');
        }

        $this->type = $type;

        switch ($this->type) {
            case self::TYPE_NULL:
                $this->value = null;
                $this->dependencyId = null;
                $this->defaultValue = null;
                break;
            case self::TYPE_VALUE:
                $this->value = $value;
                $this->dependencyId = null;
                $this->defaultValue = null;
                break;
            case self::TYPE_CONFIG:
                if (!String::isString($value)) {
                    throw new ZiboException('Provided value is invalid');
                }

                $this->value = $value;
                $this->dependencyId = null;
                $this->defaultValue = $extra;
                break;
            case self::TYPE_DEPENDENCY:
                if (!String::isString($value)) {
                    throw new ZiboException('Provided value is invalid');
                }
                if ($extra !== null && !String::isString($extra, String::NOT_EMPTY)) {
                    throw new ZiboException('Provided id of the dependency value is empty or invalid');
                }

                $this->value = $value;
                $this->dependencyId = $extra;
                $this->defaultValue = null;
                break;
        }
    }

    /**
     * Gets the type of this argument
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Gets the value of this argument
     * @return string A value
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Gets the id of the dependency value
     * @return string A identifier
     */
    public function getDependencyId() {
        return $this->dependencyId;
    }

    /**
     * Gets the default value for the config value
     * @return mixed Default value
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }

}