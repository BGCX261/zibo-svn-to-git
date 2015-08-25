<?php

namespace zibo\admin\model\module\io;

use zibo\admin\model\module\exception\ModuleDefinitionNotFoundException;
use zibo\admin\model\module\Module;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\xml\dom\Document;

use \DOMElement;

/**
 * Xml implementation of ModuleIO
 */
class XmlModuleIO implements ModuleIO {

    /**
     * Namespace of the module.xml
     * @var string
     */
    const XML_NAMESPACE = 'http://www.zibo.be/ns/zibo/admin/modules';

    /**
     * Configuration key for the schema file of the module.xml
     * @var string
     */
    const CONFIG_MODULES_SCHEMA = 'schema.modules';

    /**
     * Name of the name attribute
     * @var string
     */
    const ATTRIBUTE_NAME = 'name';

    /**
     * Name of the namespace attribute
     * @var string
     */
    const ATTRIBUTE_NAMESPACE = 'namespace';

    /**
     * Name of the path attribute
     * @var string
     */
    const ATTRIBUTE_PATH = 'path';

    /**
     * Name of the version attribute
     * @var string
     */
    const ATTRIBUTE_VERSION = 'version';

    /**
     * Name of the Zibo version attribute
     * @var string
     */
    const ATTRIBUTE_VERSION_ZIBO = 'ziboVersion';

    /**
     * Filename of the XML file with the module definition
     * @var string
     */
    const MODULE_FILE = 'module.xml';

    /**
     * Name of the dependency tag
     * @var string
     */
    const TAG_DEPENDENCY = 'dependency';

    /**
     * Name of the module tag
     * @var string
     */
    const TAG_MODULE = 'module';

    /**
     * Name of the modules tag
     * @var string
     */
    const TAG_MODULES = 'modules';

    /**
     * Read the modules from the provided path
     * @param zibo\library\filesystem\File $path Path to read the module definitions from
     * @return array Array with Module instances
     * @throws zibo\admin\model\exception\ModuleDefinitionNotFoundException when no module definition could be read from the provided path
     */
    public function readModules(File $path) {
        $xmlFile = new File($path, self::MODULE_FILE);
        if (!$xmlFile->exists()) {
            throw new ModuleDefinitionNotFoundException($path);
        }

        $dom = new Document('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->setSchemaFileFromConfig(self::CONFIG_MODULES_SCHEMA);
        $dom->load($xmlFile->getPath());

        return $this->readModulesFromElement($dom->documentElement, self::TAG_MODULE);
    }

    /**
     * Gets the modules from a modules DOM element
     * @param DOMElement $modulesElement
     * @param string $elementName Name of the module element
     * @return array Array of Module objects
     */
    private function readModulesFromElement(DOMElement $modulesElement, $elementName) {
        $modules = array();

        $elements = $modulesElement->getElementsByTagName($elementName);
        foreach($elements as $element ) {
            $modules[] = $this->readModuleFromElement($element);
        }

        return $modules;
    }

    /**
     * Gets the module from a module DOM element
     * @param DOMElement $moduleElement
     * @return Module
     */
    private function readModuleFromElement(DOMElement $moduleElement) {
        $namespace = $moduleElement->getAttribute(self::ATTRIBUTE_NAMESPACE);
        $name = $moduleElement->getAttribute(self::ATTRIBUTE_NAME);
        $version = $moduleElement->getAttribute(self::ATTRIBUTE_VERSION);
        $ziboVersion = $moduleElement->getAttribute(self::ATTRIBUTE_VERSION_ZIBO);
        $path = $moduleElement->getAttribute(self::ATTRIBUTE_PATH);

        $dependencies = $this->readModulesFromElement($moduleElement, self::TAG_DEPENDENCY);

        $module = new Module($namespace, $name, $version, $ziboVersion, $dependencies);
        if (!empty($path)) {
            $module->setPath(new File($path));
        }

        return $module;
    }

    /**
     * Write modules to the provided path
     * @param zibo\library\filesystem\File $path Path to write the modules definitions to
     * @param array $modules Array with Module instances
     * @return null
     */
    public function writeModules(File $path, array $modules) {
        $dom = new Document('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->setSchemaFileFromConfig(self::CONFIG_MODULES_SCHEMA);

        $modulesElement = $dom->createElementNS(self::XML_NAMESPACE, self::TAG_MODULES);
        $modulesElement->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:schemaLocation', self::XML_NAMESPACE . ' ' . self::XML_NAMESPACE . ' ');

        $dom->appendChild($modulesElement);
        foreach ($modules as $namespace => $names) {
            foreach ($names as $name => $module) {
                $moduleElement = $this->getModuleElementFromModule($dom, $module, self::TAG_MODULE);
                $modulesElement->appendChild($moduleElement);
            }
        }

        $path->create();
        $file = new File($path, self::MODULE_FILE);

        $dom->save($file->getPath());
    }

    /**
     * Gets a module DOM element from a module
     * @param zibo\library\xml\dom\Document $dom the document of the new element
     * @param zibo\admin\model\Module $module The module for the DOM element
     * @param string $elementName Name of the new module DOM element
     * @return DOMElement DOM element representing the provided module
     */
    private function getModuleElementFromModule(Document $dom, Module $module, $elementName) {
        $element = $dom->createElementNS(self::XML_NAMESPACE, $elementName);
        $element->setAttribute(self::ATTRIBUTE_NAMESPACE, $module->getNamespace());
        $element->setAttribute(self::ATTRIBUTE_NAME, $module->getName());
        $element->setAttribute(self::ATTRIBUTE_VERSION, $module->getVersion());

        $ziboVersion = $module->getZiboVersion();
        if (!empty($ziboVersion)) {
            $element->setAttribute(self::ATTRIBUTE_VERSION_ZIBO, $ziboVersion);
        }

        $path = $module->getPath();
        if (!empty($path)) {
            $element->setAttribute(self::ATTRIBUTE_PATH, $path->getPath());
        }

        $dependencies = $module->getDependencies();
        foreach ($dependencies as $dependency) {
            $dependencyElement = $this->getModuleElementFromModule($dom, $dependency, self::TAG_DEPENDENCY);
            $element->appendChild($dependencyElement);
        }

        return $element;
    }

}