<?php

namespace zibo\core\router\io;

use zibo\core\router\Route;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\xml\dom\Document;
use zibo\library\Structure;

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
    const CONFIG_ROUTES_RNG = 'schema.routes';

    /**
     * Path to the xml file
     * @var string
     */
    const PATH_FILE = 'config/routes.xml';

    /**
     * Name of the path attribute
     * @var string
     */
    const ATTRIBUTE_PATH = 'path';

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
     * Name of the route tag
     * @var string
     */
    const TAG_ROUTE = 'route';

    /**
     * Reads the routes from the data source
     * @return array Array with Route instances
     */
    protected function readRoutes() {
        $routes = array();

        $files = array_reverse(Zibo::getInstance()->getFiles(self::PATH_FILE));
        foreach ($files as $file) {
            $fileRoutes = $this->readRoutesFromFile($file);
            $routes = Structure::merge($routes, $fileRoutes);
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
        $dom->setRelaxNGFileFromConfig(self::CONFIG_ROUTES_RNG);
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
            $route = $this->getRouteFromElement($file, $element);
            $routes[$route->getPath()] = $route;
        }

        return $routes;
    }

    /**
     * Gets a Route object from an XML element
     * @param zibo\library\filesystem\File $file the file which is being read
     * @param DomElement $element the element from which the route will be extracted
     * @return zibo\core\router\Route
     */
    private function getRouteFromElement(File $file, DOMElement $element) {
        $path = $this->getAttribute($file, $element, self::ATTRIBUTE_PATH);
        $controller = $this->getAttribute($file, $element, self::ATTRIBUTE_CONTROLLER);
        $action = $this->getAttribute($file, $element, self::ATTRIBUTE_ACTION, false);

        return new Route($path, $controller, $action);
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