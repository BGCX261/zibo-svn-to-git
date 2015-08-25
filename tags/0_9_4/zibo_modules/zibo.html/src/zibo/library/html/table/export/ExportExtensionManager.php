<?php

namespace zibo\library\html\table\export;

use zibo\core\Zibo;

use zibo\library\ObjectFactory;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Manager of the export extensions
 */
class ExportExtensionManager {

    /**
     * Configuration key for export extensions
     * @var string
     */
    const CONFIG_EXPORT_EXTENSIONS = 'html.table.export';

    /**
     * Class name of the export view interface
     * @var string
     */
    const INTERFACE_EXPORT_VIEW = 'zibo\\library\\html\\table\\export\\ExportView';

    /**
     * Instance of the extension manager
     * @var ExportExtensionManager
     */
    private static $instance;

    /**
     * Array with the extension as key and the ExportView class name as value
     * @var array
     */
    private $extensions;

    /**
     * Constructs a new extension manager
     * @return null
     */
    private function __construct() {
        $this->extensions = array();
        $this->loadExtensionsFromConfig();
    }

    /**
     * Get the instance of the extension manager
     * @return ExportExtensionManager
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets the export view for the provided extension
     * @param string $extension
     * @return ExportView
     */
    public function getExportView($extension) {
        if (!array_key_exists($extension, $this->extensions)) {
            throw new ZiboException($extension . ' is not a registered export extension');
        }

        $objectFactory = new ObjectFactory();

        return $objectFactory->create($this->extensions[$extension], self::INTERFACE_EXPORT_VIEW);
    }

    /**
     * Get the available export extensions
     * @return array Array with the extension as value
     */
    public function getExportExtensions() {
        return array_keys($this->extensions);
    }

    /**
     * Registers an export extension
     * @param string extension Extension of the export
     * @param string className Class name of the ExportView implementation for the extension
     * @return null
     * @throws zibo\ZiboException when the extension of the class name is empty
     */
    public function registerExportExtension($extension, $className) {
        if (String::isEmpty($extension)) {
            throw new ZiboException('Provided extension is empty');
        }
        if (String::isEmpty($className)) {
            throw new ZiboException('Provided class name is empty');
        }

        $this->extensions[$extension] = $className;
    }

    /**
     * Loads the export extensions from the Zibo configuration
     * @return null
     */
    private function loadExtensionsFromConfig() {
        $extensions = Zibo::getInstance()->getConfigValue(self::CONFIG_EXPORT_EXTENSIONS);
        if (!$extensions || !is_array($extensions)) {
            return;
        }

        foreach ($extensions as $extension => $className) {
            $this->registerExportExtension($extension, $className);
        }
    }

}