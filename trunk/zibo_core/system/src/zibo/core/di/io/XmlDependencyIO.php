<?php

namespace zibo\core\di\io;

use zibo\core\di\Dependency;
use zibo\core\di\DependencyCall;
use zibo\core\di\DependencyCallArgument;
use zibo\core\di\DependencyContainer;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\xml\dom\Document;

use \DOMElement;

/**
 * Implementation to get a dependency container based on XML files
 */
class XmlDependencyIO implements DependencyIO {

    /**
     * The file name
     * @var string
     */
    const PATH_FILE = 'config/di.xml';

    /**
     * Name of the dependency tag
     * @var string
     */
    const TAG_DEPENDENCY = 'dependency';

    /**
     * Name of the call tag
     * @var string
     */
    const TAG_CALL = 'call';

    /**
     * Name of the argument tag
     * @var string
     */
    const TAG_ARGUMENT = 'argument';

    /**
     * Name of the interface attribute
     * @var string
     */
    const ATTRIBUTE_INTERFACE = 'interface';

    /**
     * Name of the class attribute
     * @var string
     */
    const ATTRIBUTE_CLASS = 'class';

    /**
     * Name of the id attribute
     * @var string
     */
    const ATTRIBUTE_ID = 'id';

    /**
     * Name of the method attribute
     * @var string
     */
    const ATTRIBUTE_METHOD = 'method';

    /**
     * Name of the type attribute
     * @var string
     */
    const ATTRIBUTE_TYPE = 'type';

    /**
     * Name of the value attribute
     * @var string
     */
    const ATTRIBUTE_VALUE = 'value';

    /**
     * Name of the default value attribute
     * @var string
     */
    const ATTRIBUTE_DEFAULT_VALUE = 'default';

    /**
     * Gets the dependency container
     * @param zibo\core\Zibo $zibo Instance of zibo
     * @return zibo\core\di\DependencyContainer
     */
    public function getContainer(Zibo $zibo) {
        $container = new DependencyContainer();

        $files = array_reverse($zibo->getFiles(self::PATH_FILE));
        foreach ($files as $file) {
            $this->readDependencies($container, $file);
        }

        return $container;
    }

    /**
     * Reads the dependencies from the provided file and adds them to the
     * provided container
     * @param zibo\core\di\DependencyContainer $container
     * @param zibo\library\filesystem\File $file
     * @return null
     */
    private function readDependencies(DependencyContainer $container, File $file) {
        $dom = new Document();
        $dom->load($file);

        $dependencyElements = $dom->getElementsByTagName(self::TAG_DEPENDENCY);
        foreach ($dependencyElements as $dependencyElement) {
            $interface = $dependencyElement->getAttribute(self::ATTRIBUTE_INTERFACE);
            $className = $dependencyElement->getAttribute(self::ATTRIBUTE_CLASS);
            $id = $dependencyElement->getAttribute(self::ATTRIBUTE_ID);
            if (!$id) {
                $id = null;
            }

            $dependency = new Dependency($className, $id);

            $this->readCalls($dependency, $dependencyElement);

            $container->addDependency($interface, $dependency);
        }
    }

    /**
     * Reads the calls from the provided dependency element and adds them to
     * the dependency instance
     * @param zibo\core\di\Dependency $dependency
     * @param DOMElement $dependencyElement
     * @return null
     */
    private function readCalls(Dependency $dependency, DOMElement $dependencyElement) {
        $calls = array();

        $callElements = $dependencyElement->getElementsByTagName(self::TAG_CALL);
        foreach ($callElements as $callElement) {
            $methodName = $callElement->getAttribute(self::ATTRIBUTE_METHOD);

            $call = new DependencyCall($methodName);

            $argumentElements = $callElement->getElementsByTagName(self::TAG_ARGUMENT);
            foreach ($argumentElements as $argumentElement) {
                $type = $argumentElement->getAttribute(self::ATTRIBUTE_TYPE);
                $value = $argumentElement->getAttribute(self::ATTRIBUTE_VALUE);
                $extra = null;

                switch ($type) {
                    case DependencyCallArgument::TYPE_DEPENDENCY:
                        $id = $argumentElement->getAttribute(self::ATTRIBUTE_ID);
                        if ($id) {
                            $extra = $id;
                        }
                        break;
                    case DependencyCallArgument::TYPE_CONFIG:
                        if ($argumentElement->hasAttribute(self::ATTRIBUTE_DEFAULT_VALUE)) {
                            $extra = $argumentElement->getAttribute(self::ATTRIBUTE_DEFAULT_VALUE);
                        }
                        break;
                }

                $call->addArgument(new DependencyCallArgument($type, $value, $extra));
            }

            $dependency->addCall($call);
        }
    }

}