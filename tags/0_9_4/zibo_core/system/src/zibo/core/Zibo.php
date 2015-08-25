<?php

namespace zibo\core;

use zibo\core\environment\Environment;
use zibo\core\router\GenericRouter;

use zibo\library\config\io\ConfigIO;
use zibo\library\config\Config;
use zibo\library\filesystem\browser\Browser;
use zibo\library\filesystem\File;
use zibo\library\ObjectFactory;

use zibo\ZiboException;

use \Exception;

/**
 * Main Zibo object
 */
final class Zibo {

    /**
     * Configuration key for the maximum amount of system memory
     * @var string
     */
    const CONFIG_MEMORY = 'system.memory';

    /**
     * Configuration key for the default locale of the server
     * @var string
     */
    const CONFIG_LOCALE = 'system.locale';

    /**
     * Configuration key for the default timezone
     * @var string
     */
    const CONFIG_TIMEZONE = 'system.timezone';

    /**
     * Name of the application directory
     * @var string
     */
    const DIRECTORY_APPLICATION = 'application';

    /**
     * Name of the cache directory
     * @var string
     */
    const DIRECTORY_CACHE = 'cache';

    /**
     * Name of the config directory
     * @var string
     */
    const DIRECTORY_CONFIG = 'config';

    /**
     * Name of the data directory
     * @var string
     */
    const DIRECTORY_DATA = 'data';

    /**
     * Name of localization directory
     * @var string
     */
    const DIRECTORY_L10N = 'l10n';

    /**
     * Name of the modules directory
     * @var string
     */
    const DIRECTORY_MODULES = 'modules';

    /**
     * Name of the public directory
     * @var string
     */
    const DIRECTORY_PUBLIC = 'public';

    /**
     * Name of the source directory
     * @var string
     */
    const DIRECTORY_SOURCE = 'src';

    /**
     * Name of the system directory
     * @var string
     */
    const DIRECTORY_SYSTEM = 'system';

    /**
     * Name of the vendor directory
     * @var string
     */
    const DIRECTORY_VENDOR = 'vendor';

    /**
     * Name of the view directory
     * @var string
     */
    const DIRECTORY_VIEW = 'view';

    /**
     * Name of the web directory
     * @var string
     */
    const DIRECTORY_WEB = 'web';

    /**
     * Name of the event to clear the cache
     * @var string
     */
    const EVENT_CLEAR_CACHE = 'system.cache.clear';

    /**
     * Name of the event run when an error occurs
     * @var string
     */
    const EVENT_ERROR = 'system.error';

    /**
     * Name of the event to log an action
     * @var string
     */
    const EVENT_LOG = 'log';

    /**
     * Name of the event which is run before routing
     * @var string
     */
    const EVENT_PRE_ROUTE = 'system.route.pre';

    /**
     * Name of the event which is run after routing
     * @var string
     */
    const EVENT_POST_ROUTE = 'system.route.post';

    /**
     * Name of the event which is run before dispatching
     * @var string
     */
    const EVENT_PRE_DISPATCH = 'system.dispatch.pre';

    /**
     * Name of the event which is run after dispatching
     * @var string
     */
    const EVENT_POST_DISPATCH = 'system.dispatch.post';

    /**
     * Name of the event which is run before sending the response
     * @var string
     */
    const EVENT_PRE_RESPONSE = 'system.response.pre';

    /**
     * Name of the event which is run after sending the response
     * @var string
     */
    const EVENT_POST_RESPONSE = 'system.reponse.post';

    /**
     * Name of the module initialization script
     * @var string
     */
    const MODULE_SCRIPT = 'index.php';

    /**
     * Current version of the Zibo core
     * @var string
     */
    const VERSION = '0.9.4';

    /**
     * The instance of the Zibo core
     * @var Zibo
     */
    private static $instance;

    /**
     * Exception of the first getInstance call
     * @var zibo\ZiboException
     */
    private static $firstInstanceCallException;

    /**
     * The file browser for Zibo
     * @var zibo\library\filesystem\browser\Browser
     */
    private $browser;

    /**
     * The configuration
     * @var zibo\library\config\Config
     */
    private $config;

    /**
     * Dispatcher of the actions in the controllers
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * The environment we are running in
     * @var zibo\core\environment\Environment
     */
    private $environment;

    /**
     * Manager of the events
     * @var EventManager
     */
    private $eventManager;

    /**
     * Data container of the request
     * @var Request
     */
    private $request;

    /**
     * Data container of the response
     * @var Response
     */
    private $response;

    /**
     * Router to obtain the Request object
     * @var Router
     */
    private $router;

    /**
     * Flag to set wheter the core is running or not
     * @var boolean
     */
    private $isRunning;

    /**
     * Construct the Zibo instance
     * @param zibo\library\filesystem\browser\Browser $browser
     * @param zibo\library\config\io\ConfigIO $configIO
     * @return null
     */
    private function __construct(Browser $browser, ConfigIO $configIO) {
        $this->browser = $browser;
        $this->config = new Config($configIO);
        $this->eventManager = new EventManager();
        $this->response = new Response();
        $this->isRunning = false;
    }

    /**
     * Clone the Zibo instance is not allowed
     * @return null
     * @throws zibo\ZiboException
     */
    public function __clone() {
        throw new ZiboException('Cannot clone Zibo');
    }

    /**
     * Get the Zibo instance
     * @param zibo\library\filesystem\browser\Browser $browser
     * @param zibo\library\config\io\ConfigIO $configIO
     * @return Zibo the Zibo instance
     * @throws zibo\ZiboException when this is the first getInstance call and no Browser and/or a ConfigIO are provided
     * @throws zibo\ZiboException when this is not the first getInstance call and a Browser and/or a ConfigIO are provided
     */
    public static function getInstance(Browser $browser = null, ConfigIO $configIO = null) {
        if (self::$instance == null) {
            self::$firstInstanceCallException = new ZiboException('First instance call');
            if ($browser == null) {
                throw new ZiboException('The first getInstance call needs a zibo\\library\\filesystem\\browser\\Browser');
            }
            if ($configIO == null) {
                throw new ZiboException('The first getInstance call needs a zibo\\library\\config\\io\\ConfigIO');
            }
            self::$instance = new self($browser, $configIO);
        } elseif ($browser != null) {
            throw new ZiboException('Can\'t pass a Browser after the first getInstance call', 0, self::$firstInstanceCallException);
        } elseif ($configIO != null) {
            throw new ZiboException('Can\'t pass a ConfigIO after the first getInstance call', 0, self::$firstInstanceCallException);
        }

        return self::$instance;
    }

    /**
     * Clear the cache by running the clear cache event
     * @return null
     * @see EVENT_CLEAR_CACHE
     */
    public function clearCache() {
        $this->eventManager->runEvent(self::EVENT_CLEAR_CACHE);
    }

    /**
     * Get the root path of this system
     * @return zibo\library\filesystem\File
     */
    public function getRootPath() {
        return $this->browser->getRootPath();
    }

    /**
     * Get the paths of the Zibo file system structure
     * @param boolean $refresh true to reread the include paths, false to use a cached list
     * @return array Array with File objects containing the paths of the Zibo file system structure
     */
    public function getIncludePaths($refresh = false) {
        return $this->browser->getIncludePaths($refresh);
    }

    /**
     * Get a file from the Zibo file system structure
     * @param string $file file name relative to the Zibo file system structure
     * @return zibo\library\filesystem\File
     */
    public function getFile($file) {
        return $this->browser->getFile($file);
    }

    /**
     * Get multiple files from the Zibo file system structure
     * @param string $file file name relative to the Zibo file system structure
     * @return array Array with File objects which have the provided name
     */
    public function getFiles($file) {
        return $this->browser->getFiles($file);
    }

    /**
     * Reinitialize the file browser
     * @return null
     */
    public function resetFileBrowser() {
        $this->browser->reset();
    }

    /**
     * Get the relative file in the Zibo file structure for a given absolute file.
     * @param zibo\library\filesystem\File $file absolute file to get the relative file from
     * @return zibo\library\filesystem\File relative file in the Zibo file structure if located in the root of the Zibo installation
     * @throws zibo\ZiboException when the provided file is not in the root path
     * @throws zibo\ZiboException when the provided file is part of the Zibo file system structure
     */
    public function getRelativeFile(File $file) {
        $absoluteFile = $file->getAbsolutePath();

        $rootPath = $this->getRootPath();

        $isPhar = $file->hasPharProtocol();

        $file = str_replace($rootPath->getPath() . File::DIRECTORY_SEPARATOR, '', $absoluteFile);
        if ($file == $absoluteFile) {
            throw new ZiboException($file . ' is not in the root path');
        }

        if ($isPhar) {
            $file = substr($file, 7);
        }

        $tokens = explode(File::DIRECTORY_SEPARATOR, $file);
        $token = array_shift($tokens);

        if ($token == self::DIRECTORY_APPLICATION || $token == self::DIRECTORY_SYSTEM) {
            $token = array_pop($tokens);
            return new File(implode(File::DIRECTORY_SEPARATOR, $tokens), $token);
        }

        if ($token !== self::DIRECTORY_MODULES || count($tokens) < 2) {
            throw new ZiboException($file . ' is not in the Zibo file system structure (' . $token . ')');
        }

        array_shift($tokens);
        $token = array_pop($tokens);

        return new File(implode(File::DIRECTORY_SEPARATOR, $tokens), $token);
    }

    /**
     * Get all the configuration values
     * @return array
     */
    public function getAllConfigValues() {
        return $this->config->getAll();
    }

    /**
     * Get a configuration value
     * @param string $key key of the configuration value
     * @param mixed $default default value for when the configuration value is not set
     * @return mixed the configuration value or the provided default value when the configuration value is set
     */
    public function getConfigValue($key, $default = null) {
        return $this->config->get($key, $default);
    }

    /**
     * Set a new configuration value
     * @param string $key
     * @param mixed $value
     * @return null
     */
    public function setConfigValue($key, $value) {
        $this->config->set($key, $value);
    }

    /**
     * Clear the cache of the configuration
     * @return null
     */
    public function clearConfigCache() {
        $this->config->clearCache();
    }

    /**
     * Set the environment
     * @param zibo\core\environment\Environment $environment
     * @return null
     */
    public function setEnvironment(Environment $environment) {
        $this->environment = $environment;
    }

    /**
     * Get the environment we are running in
     * @return zibo\core\environment\Environment
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * Get the response
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Set the request
     * @param Request $request
     * @return null
     */
    public function setRequest(Request $request = null) {
        $this->request = $request;
    }

    /**
     * Get the request
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Set the router
     * @param Router $router
     * @return null
     */
    public function setRouter(Router $router) {
        $this->router = $router;
    }

    /**
     * Get the router
     * @return Router
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * Set the dispatcher
     * @param Dispatcher $dispatcher
     * @return null
     */
    public function setDispatcher(Dispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get the dispatcher
     * @return Dispatcher
     */
    public function getDispatcher() {
        return $this->dispatcher;
    }

    /**
     * Register a event listener for an event
     * @param string $eventName name of the event
     * @param mixed $callback listener callback
     * @param int $weight weight of the listener to influence the order of listeners
     * @return null
     */
    public function registerEventListener($eventName, $callback, $weight = null) {
        $this->eventManager->registerEventListener($eventName, $callback, $weight);
    }

    /**
     * Clears the listeners for the provided event
     * @param string $eventName Name of the event
     * @return null
     */
    public function clearEventListeners($eventName) {
        $this->eventManager->clearEventListeners($eventName);
    }

    /**
     * Run an event
     * @param string $eventName name of the event
     * @return null
     * @throws zibo\ZiboException when trying to run a system event
     */
    public function runEvent($eventName) {
        $isEventAllowed = strlen($eventName) >= 7 && substr($eventName, 0, 7) == 'system.';
        if ($isEventAllowed) {
            throw new ZiboException('Can\'t run system events from outside the Zibo core');
        }

        $arguments = func_get_args();
        unset($arguments[0]);

        $this->eventManager->runEventWithArrayArguments($eventName, $arguments);
    }

    /**
     * Run Zibo: perform the routing, dispatch the controller and send back te response
     * @param boolean $initModules set to false to skip initializing the modules
     * @return null
     */
    public function run($initModules = true) {
        $this->checkRunning();

        $this->setMemoryLimit();

        if ($initModules) {
            $this->initModules();
        }

        try {
            $this->route();

            $request = $this->getRequest();
            $this->dispatch();
            $this->setRequest($request);
        } catch (Exception $exception) {
            $this->eventManager->runEvent(self::EVENT_ERROR, $exception);
        }

        $this->sendResponse();
    }

    /**
     * Check whether Zibo is already running
     * @return null
     *
     * @throws zibo\ZiboException when Zibo is already running
     */
    private function checkRunning() {
        if ($this->isRunning) {
            throw new ZiboException('Zibo can only run once per request');
        }
        $this->isRunning = true;
    }

    /**
     * Set the memory limit based on the maximum memory configuration value
     * @return null
     */
    private function setMemoryLimit() {
        $memory = $this->getConfigValue(self::CONFIG_MEMORY, null);
        if (!$memory) {
            return;
        }
        ini_set('memory_limit', $memory);
    }

    /**
     * Initialize the modules by running their initialization script
     * @return null
     */
    private function initModules() {
        $scripts = $this->getFiles(self::DIRECTORY_SOURCE . File::DIRECTORY_SEPARATOR . self::MODULE_SCRIPT);
        foreach ($scripts as $script) {
            include($script);
        }
    }

    /**
     * Perform the routing: get a Request object from the router and set it to this object
     * @return null
     */
    private function route() {
        $this->eventManager->runEvent(self::EVENT_PRE_ROUTE);

        if ($this->router == null) {
            $this->router = new GenericRouter();
        }

        $request = $this->router->getRequest();

        $this->setRequest($request);

        $this->eventManager->runEvent(self::EVENT_POST_ROUTE);
    }

    /**
     * Dispatch the request to the action of the controller
     * @return null
     */
    private function dispatch() {
        if (!$this->dispatcher) {
            $this->dispatcher = new Dispatcher(new ObjectFactory());
        }

        while ($this->request != null) {
            $this->eventManager->runEvent(self::EVENT_PRE_DISPATCH);

            if ($this->request == null) {
                continue;
            }

            try {
                $chainedRequest = $this->dispatcher->dispatch($this->request, $this->response);
                $this->setRequest($chainedRequest);
            } catch (Exception $e) {
                $this->eventManager->runEvent(self::EVENT_ERROR, $e);
                $this->setRequest(null);
            }

            $this->eventManager->runEvent(self::EVENT_POST_DISPATCH);
        }
    }

    /**
     * Send the response to the client
     * @return null
     */
    private function sendResponse() {
        $this->eventManager->runEvent(self::EVENT_PRE_RESPONSE);

        $statusCode = $this->response->getStatusCode();
        $headers = $this->response->getHeaders();
        $this->sendHeaders($statusCode, $headers);

        if (!$this->response->willRedirect()) {
            $view = $this->response->getView();
            if (!empty($view)) {
                $view->render(false);
            }
        }

        $this->eventManager->runEvent(self::EVENT_POST_RESPONSE);
    }

    /**
     * Send the headers of the response to the client
     * @param int $statusCode Http status code
     * @param array $headers Array with Header objects
     * @return null
     */
    private function sendHeaders($statusCode, $headers) {
        $emptyHeaders = empty($headers);
        if ($emptyHeaders && $statusCode == Response::STATUS_CODE_OK) {
            return;
        }

        if (headers_sent($file, $line)) {
            throw new ZiboException('Cannot send headers, output already started in ' . $file . ' on line ' . $line);
        }

        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $statusCode);
        }

        foreach ($headers as $header) {
            header($header->__toString(), false);
        }
    }

}
