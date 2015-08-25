<?php

namespace zibo\core\router\io;

use zibo\core\router\Alias;
use zibo\core\router\Route;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\xml\dom\Document;

use zibo\ZiboException;

use \DOMElement;

/**
 * XML implementation of the RouterIO
 */
class XmlRouterIO extends AbstractRouterIO {

    /**
     * Configuration key for the routes schema file
     * @var string
     */
    const CONFIG_RNG = 'schema.routes';

    /**
     * Path to the xml file for the routes
     * @var string
     */
    const PATH_FILE = 'config/routes.xml';

    /**
    * Name of the alias tag
     * @var string
    */
    const TAG_ALIAS = 'alias';

    /**
    * Name of the route tag
    * @var string
    */
    const TAG_ROUTE = 'route';

    /**
     * Name of the path attribute
     * @var string
     */
    const ATTRIBUTE_PATH = 'path';

    /**
     * Name of the destination attribute
     * @var string
     */
    const ATTRIBUTE_DESTINATION = 'destination';

    /**
     * Name of the controller attribute
     * @var string
     */
    const ATTRIBUTE_CONTROLLER = 'controller';

    /**
     * Name of the action attribute
     * @var string
     */
    const ATTRIBUTE_ACTION = 'action';

    /**
     * Instance of Zibo
     * @var zibo\core\Zibo
     */
    protected $zibo;

    /**
     * Constructs a new XML router IO implementation
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return null
     */
    public function __construct(Zibo $zibo) {
        $this->zibo = $zibo;
    }

    /**
    * Reads the aliases from the data source
    * @return array Array with the path as key and the destination path as value
    */
    protected function readAliases() {
    	$aliases = array();

    	$files = array_reverse($this->zibo->getFiles(self::PATH_FILE));
    	foreach ($files as $file) {
    		$fileAliases = $this->readAliasesFromFile($file);
    		$aliases = $fileAliases + $aliases;
    	}

    	return $aliases;
    }

    /**
     * Reads the aliases from the provided file
     * @param zibo\library\filesystem\File $file
     * @return array Array with the path as key and the destination path as value
     */
    private function readAliasesFromFile(File $file) {
    	$dom = new Document();

        $relaxNg = $this->zibo->getConfigValue(self::CONFIG_RNG);
        if ($relaxNg) {
        	$dom->setRelaxNGFile($relaxNg);
        }

    	$dom->load($file);

    	return $this->getAliasesFromElement($file, $dom->documentElement);
    }

    /**
     * Gets the routes object from an XML routes element
     * @param zibo\library\filesystem\File $file the file which is being read
     * @param DomElement $aliasesElement the element which contains alias elements
     * @return array Array with Alias objects as value and their path as key
     */
    private function getAliasesFromElement(File $file, DOMElement $aliasesElement) {
    	$aliases = array();

    	$elements = $aliasesElement->getElementsByTagName(self::TAG_ALIAS);
    	foreach ($elements as $element) {
    		$path = $this->getAttribute($file, $element, self::ATTRIBUTE_PATH);
    		$destination = $this->getAttribute($file, $element, self::ATTRIBUTE_DESTINATION);

    		$aliases[$path] = new Alias($path, $destination);
    	}

    	return $aliases;
    }

    /**
     * Reads the routes from the data source
     * @return array Array with Route instances
     */
    protected function readRoutes() {
        $routes = array();

        $files = array_reverse($this->zibo->getFiles(self::PATH_FILE));
        foreach ($files as $file) {
            $fileRoutes = $this->readRoutesFromFile($file);
            $routes = $fileRoutes + $routes;
        }

        return $routes;
    }

    /**
     * Reads the routes from the provided file
     * @param zibo\library\filesystem\File $file
     * @return array Array with Route objects as value and their path as key
     */
    private function readRoutesFromFile(File $file) {
        $dom = new Document();

        $relaxNg = $this->zibo->getConfigValue(self::CONFIG_RNG);
        if ($relaxNg) {
            $dom->setRelaxNGFile($relaxNg);
        }

        $dom->load($file);

        return $this->getRoutesFromElement($file, $dom->documentElement);
    }

    /**
     * Gets the routes object from an XML routes element
     * @param zibo\library\filesystem\File $file the file which is being read
     * @param DomElement $routesElement the element which contains route elements
     * @return array Array with Route objects as value and their path as key
     */
    private function getRoutesFromElement(File $file, DOMElement $routesElement) {
        $routes = array();

        $elements = $routesElement->getElementsByTagName(self::TAG_ROUTE);
        foreach ($elements as $element) {
        	$path = $this->getAttribute($file, $element, self::ATTRIBUTE_PATH);
        	$controller = $this->getAttribute($file, $element, self::ATTRIBUTE_CONTROLLER);
        	$action = $this->getAttribute($file, $element, self::ATTRIBUTE_ACTION, false);
        	if (!$action) {
        	    $action = null;
        	}

        	$routes[$path] = new Route($path, $controller, $action);
        }

        return $routes;
    }

    /**
     * Gets the value of an attribute from the provided XML element
     * @param zibo\library\filesystem\File $file the file which is being read
     * @param DomElement $element the element from which the attribute needs to be retrieved
     * @param string $name name of the attribute
     * @param boolean $required flag to see if the value is required or not
     * @return string
     * @throws zibo\ZiboException when the attribute is required but not set or empty
     */
    private function getAttribute(File $file, DOMElement $element, $name, $required = true) {
        $value = $element->getAttribute($name);

        if ($required && empty($value)) {
            throw new ZiboException('Attribute ' . $name . ' not set in ' . $file->getPath());
        }

        return $value;
    }

}