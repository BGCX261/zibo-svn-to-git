<?php

namespace zibo\core;

use zibo\core\config\io\ConfigIO;
use zibo\core\config\Config;
use zibo\core\di\DependencyInjector;
use zibo\core\dispatcher\Dispatcher;
use zibo\core\environment\Environment;
use zibo\core\filesystem\FileBrowser;
use zibo\core\module\ConfigModuleLoader;
use zibo\core\router\Router;

use zibo\library\http\HeaderContainer;
use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * The kernel of Zibo
 */
class Zibo {

    /**
     * Configuration key for the dependency I/O implementation
     * @var string
     */
    const CONFIG_DEPENDENCY_IO = 'system.dependency.io';

    /**
     * Configuration key for the default locale of the server
     * @var string
     */
    const CONFIG_LOCALE = 'system.locale';

    /**
    * Configuration key for the maximum amount of system memory
     * @var string
    */
    const CONFIG_MEMORY = 'system.memory';

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
     * Class name of the dependency I/O interface
     * @var string
     */
    const INTERFACE_DEPENDENCY_IO = 'zibo\\core\\di\\io\\DependencyIO';

    /**
     * Class name of the dispatcher interface
     * @var string
     */
    const INTERFACE_DISPATCHER = 'zibo\\core\\dispatcher\\Dispatcher';

    /**
     * Class name of the environment interface
     * @var string
     */
    const INTERFACE_ENVIRONMENT = 'zibo\\core\\environment\\Environment';

    /**
     * Class name of the file browser interface
     * @var string
     */
    const INTERFACE_FILE_BROWSER = 'zibo\\core\\filesystem\\FileBrowser';

    /**
     * Class name of the module loader interface
     * @var string
     */
    const INTERFACE_MODULE_LOADER = 'zibo\\core\\module\\ModuleLoader';

    /**
     * Class name of the router interface
     * @var string
     */
    const INTERFACE_ROUTER = 'zibo\\core\\router\\Router';

    /**
     * Current version of the Zibo core
     * @var string
     */
    const VERSION = '0.10.0';

    /**
     * The file browser for Zibo
     * @var zibo\core\filesystem\Browser
     */
    protected $fileBrowser;

    /**
     * The environment we are running in
     * @var zibo\core\environment\Environment
     */
    protected $environment;

    /**
     * The configuration
     * @var zibo\library\config\Config
     */
    protected $config;

    /**
     * Instance of the dependency injector
     * @var zibo\library\di\DependencyInjector
     */
    protected $dependencyInjector;

    /**
     * Manager of the events
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Data container of the request
     * @var Request
     */
    protected $request;

    /**
     * Instance of the response
     * @var Response
     */
    protected $response;

    /**
     * Router to obtain the Request object
     * @var Router
     */
    protected $router;

    /**
     * Dispatcher of the actions in the controllers
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Flag to set wheter the core is running or not
     * @var boolean
     */
    protected $isRunning;

    /**
     * Constructs a new instance of the Zibo kernel
     * @param zibo\core\filesystem\FileBrowser $fileBrowser Instance of the file browser
     * @param zibo\core\config\io\ConfigIO $configIO Implementation of the configuration
     * input/output
     * @return null
     */
    public function __construct(FileBrowser $fileBrowser, ConfigIO $configIO) {
        $this->environment = null;
        $this->fileBrowser = $fileBrowser;

        $this->config = new Config($configIO);
        $this->dependencyInjector = null;
        $this->eventManager = new EventManager();

        $this->request = null;
        $this->response = new Response();
        $this->router = null;
        $this->dispatcher = null;

        $this->isRunning = false;

        $this->setMemoryLimit();
    }

    /**
     * Set the memory limit based on the maximum memory configuration value
     * @return null
     */
    protected function setMemoryLimit() {
        $memory = $this->getConfigValue(self::CONFIG_MEMORY, null);
        if (!$memory) {
            return;
        }

        ini_set('memory_limit', $memory);
    }

    /**
     * Sets the default timezone from the configuration
     * @param string|array $timezone The default timezone used when no timezone
     * set in the configuration
     * @return null
     */
    public function setDefaultTimeZone($timezone) {
        $timezone = $this->getConfigValue(self::CONFIG_TIMEZONE, $timezone);
        date_default_timezone_set($timezone);
    }

    /**
     * Sets the default locale from the configuration
     * @param string|array $locale The default locale used when no locale set
     * in the configuration
     * @return null
     */
    public function setDefaultLocale($locale) {
        $locale = $this->getConfigValue(self::CONFIG_LOCALE, $locale);
        setlocale(LC_ALL, $locale);
    }

    /**
     * Get the root path of this system
     * @return zibo\library\filesystem\File
     */
    public function getRootPath() {
        return $this->fileBrowser->getRootPath();
    }

    /**
     * Get the paths of the Zibo file system structure
     * @param boolean $refresh true to reread the include paths, false to use a cached list
     * @return array Array with File objects containing the paths of the Zibo file system structure
     */
    public function getIncludePaths($refresh = false) {
        return $this->fileBrowser->getIncludePaths($refresh);
    }

    /**
     * Get a file from the Zibo file system structure
     * @param string $file file name relative to the Zibo file system structure
     * @return zibo\library\filesystem\File
     */
    public function getFile($file) {
        return $this->fileBrowser->getFile($file);
    }

    /**
     * Get multiple files from the Zibo file system structure
     * @param string $file file name relative to the Zibo file system structure
     * @return array Array with File objects which have the provided name
     */
    public function getFiles($file) {
        return $this->fileBrowser->getFiles($file);
    }

    /**
     * Gets the relative file in the Zibo file structure for a given path.
     * @param string|zibo\library\filesystem\File $file File to get the
     * relative file from
     * @return zibo\library\filesystem\File relative file in the Zibo file
     * structure if located in the root of the Zibo installation
     * @throws zibo\ZiboException when the provided file is not in the root path
     * @throws zibo\ZiboException when the provided file is part of the Zibo
     * file system structure
     */
    public function getRelativeFile($file) {
        return $this->fileBrowser->getRelativeFile($file);
    }

    /**
     * Reinitialize the file browser
     * @return null
     */
    public function resetFileBrowser() {
        $this->fileBrowser->reset();
    }

    /**
     * Sets the environment
     * @param zibo\core\environment\Environment $environment
     * @return null
     */
    public function setEnvironment(Environment $environment) {
        $this->environment = $environment;
        $this->environment->setZibo($this);
    }

    /**
     * Get the environment we are running in
     * @return zibo\core\environment\Environment
     */
    public function getEnvironment() {
        if ($this->environment === null) {
            $this->setEnvironment(Environment::getEnvironment());
        }

        return $this->environment;
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
     * @param mixed $default default value for when the configuration value
     * is not set
     * @return mixed the configuration value or the provided default value
     * when the configuration value is set
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
     * Clears the cache of the configuration
     * @return null
     */
    public function clearConfigCache() {
        $this->config->clearCache();
    }

    private function setDependencyInjector() {
        // creates the dependency injector
        $this->dependencyInjector = new DependencyInjector();

        // gets the dependency container through the configuration
        $dependencyIO = $this->getConfigValue(self::CONFIG_DEPENDENCY_IO);
        if ($dependencyIO) {
            $objectFactory = new ObjectFactory();
            $dependencyIO = $objectFactory->create($dependencyIO, self::INTERFACE_DEPENDENCY_IO);

            $container = $dependencyIO->getContainer($this);

            $this->dependencyInjector->setContainer($container);
            $this->dependencyInjector->setInstance($objectFactory);
        }

        // set Zibo to the dependency injector
        $this->dependencyInjector->setInstance($this);
        $this->dependencyInjector->setInstance($this->getEnvironment(), self::INTERFACE_ENVIRONMENT);
        $this->dependencyInjector->setInstance($this->fileBrowser, self::INTERFACE_FILE_BROWSER);
    }

    /**
     * Gets a instance of a class through dependency injection
     * @param string $interface Full class name of the interface or parent class
     * @param string $id Set if a specific instance is required
     * @return mixed The instance of the requested interface
     * @throws zibo\ZiboException when the interface is not set
     */
    public function getDependency($interface, $id = null) {
        if ($this->dependencyInjector === null) {
            $this->setDependencyInjector();
        }

        return $this->dependencyInjector->get($interface, $id);
    }

    /**
     * Gets all defined instances of a class through dependency injection
     * @param string $interface Full class name of the interface or parent class
     * @return array The instances of the requested interface
     * @throws zibo\ZiboException when the interface is not set
     */
    public function getDependencies($interface) {
        if ($this->dependencyInjector === null) {
            $this->setDependencyInjector();
        }

        return $this->dependencyInjector->getAll($interface);
    }

    /**
     * Registers a event listener for an event
     * @param string $eventName name of the event
     * @param mixed $callback listener callback
     * @param int $weight weight of the listener to influence the order of listeners
     * @return null
     */
    public function registerEventListener($eventName, $callback, $weight = null) {
        $this->eventManager->registerEventListener($eventName, $callback, $weight);
    }

    /**
     * Clears the listeners.
     *
     * Removes all the event listeners for the provided event. If no event is
     * provided, all event listeners will be cleared.
     * @param string $eventName Name of the event
     * @return null
     */
    public function clearEventListeners($eventName = null) {
        $this->eventManager->clearEventListeners($eventName);
    }

    /**
     * Triggers an event.
     *
     * The instance of Zibo will always be passed as the first parameter to the
     * event listeners. All parameters passed after the event name of this
     * method call, will be passed through to the event listeners after the
     * Zibo instance.
     * @param string $eventName Name of the event
     * @return null
     * @throws zibo\ZiboException when trying to run a system event
     */
    public function triggerEvent($eventName) {
        $isEventAllowed = strlen($eventName) >= 7 && substr($eventName, 0, 7) == 'system.';
        if ($isEventAllowed) {
            throw new ZiboException('Can\'t run system events from outside the Zibo kernel.');
        }

        $arguments = func_get_args();
        $arguments[0] = $this;

        $this->eventManager->triggerEventWithArrayArguments($eventName, $arguments);
    }

    /**
     * Gets the response
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }

	/**
	 * Sets the request
     * @param Request $request
     * @return null
     */
    public function setRequest(Request $request = null) {
        $this->request = $request;
    }

    /**
     * Gets the request
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Sets the router
     * @param Router $router
     * @return null
     */
    public function setRouter(Router $router) {
        $this->router = $router;
    }

    /**
     * Gets the router
     * @return Router
     */
    public function getRouter() {
        if (!$this->router) {
            $this->router = $this->getDependency(self::INTERFACE_ROUTER);
        }

        return $this->router;
    }

    /**
     * Sets the dispatcher
     * @param zibo\core\dispatcher\Dispatcher $dispatcher
     * @return null
     */
    public function setDispatcher(Dispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Gets the dispatcher
     * @return zibo\core\dispatcher\Dispatcher
     */
    public function getDispatcher() {
        if (!$this->dispatcher) {
            $this->dispatcher = $this->getDependency(self::INTERFACE_DISPATCHER);
        }

        return $this->dispatcher;
    }

    /**
     * Loads the modules and invokes the boot method on them
     * @return null
     */
    public function bootModules() {
        $moduleLoader = $this->getDependency(self::INTERFACE_MODULE_LOADER);

        $modules = $moduleLoader->loadModules($this);
        foreach ($modules as $module) {
            $module->boot($this);
        }
    }

    /**
     * Gets whether this instance is running
     * @return boolean
     */
    public function isRunning() {
        return $this->isRunning;
    }

    /**
     * Runs the framework.
     *
     * The request will be retrieved from the router, dispatched to the controller
     * and the response will be sent to the client
     * @return null
     */
    public function main() {
        $this->checkRunning();

        try {
            $this->route();

            $request = $this->getRequest();

            $this->dispatch();

            $this->setRequest($request);
        } catch (Exception $exception) {
            $this->eventManager->triggerEvent(self::EVENT_ERROR, $exception);
        }

        $this->sendResponse();
    }

    /**
     * Sets this instance to the run state or throws an exception if this
     * instance is already in such a state.
     * @return null
     * @throws zibo\ZiboException when this instance is already running
     * @see main()
     */
    protected function checkRunning() {
        if ($this->isRunning) {
            throw new ZiboException('Zibo can only run once per request');
        }

        $this->isRunning = true;
    }

    /**
     * Gets a request object from the router and sets it to this instance of Zibo
     * @return null
     */
    protected function route() {
        $this->eventManager->triggerEvent(self::EVENT_PRE_ROUTE, $this);

        $environment = $this->getEnvironment();

        $baseUrl = $environment->getBaseUrl();
        $path = $environment->getRequestedPath();

        $request = $this->getRouter()->getRequest($baseUrl, $path);

        $this->setRequest($request);

        $this->eventManager->triggerEvent(self::EVENT_POST_ROUTE, $this);
    }

    /**
     * Dispatch the request to the action of the controller
     * @return null
     */
    protected function dispatch() {
        if (!$this->request) {
            // there is no request to start with, so we just return the
            // appropriate HTTP response status code
            $this->response->setStatusCode(Response::STATUS_CODE_NOT_IMPLEMENTED);
            return;
        }

        $dispatcher = $this->getDispatcher();

        // request chaining
        while ($this->request) {
            $this->eventManager->triggerEvent(self::EVENT_PRE_DISPATCH ,$this);

            if (!$this->request) {
                continue;
            }

            try {
                $chainedRequest = $dispatcher->dispatch($this->request, $this->response);

                if ($chainedRequest && !$chainedRequest instanceof Request) {
                    throw new ZiboException('Action returned a invalid value, return nothing or a new zibo\\core\\Request object for request chaining.');
                }

                $this->setRequest($chainedRequest);
            } catch (Exception $exception) {
                $this->eventManager->triggerEvent(self::EVENT_ERROR, $exception);
                $this->setRequest(null);
            }

            $this->eventManager->triggerEvent(self::EVENT_POST_DISPATCH, $this);
        }
    }

    /**
     * Sends the response to the client
     * @return null
     */
    protected function sendResponse() {
        $this->eventManager->triggerEvent(self::EVENT_PRE_RESPONSE, $this);

        $statusCode = $this->response->getStatusCode();
        $headers = $this->response->getHeaders();
        $this->sendHeaders($statusCode, $headers);

        if (!$this->response->willRedirect()) {
            $view = $this->response->getView();
            if ($view) {
                $view->render(false);
            }
        }

        $this->eventManager->triggerEvent(self::EVENT_POST_RESPONSE, $this);
    }

    /**
     * Sets the status code and sends the headers to the client
     * @param int $statusCode HTTP response status code
     * @param zibo\library\http\HeaderContainer $headers Container of the headers
     * @return null
     * @throws zibo\ZiboException when the output already started
     * @see zibo\library\http\Header
     */
    protected function sendHeaders($statusCode, HeaderContainer $headers) {
        if (!$headers->hasHeaders() && $statusCode === Response::STATUS_CODE_OK) {
            return;
        }

        if (headers_sent($file, $line)) {
            throw new ZiboException('Cannot send headers, output already started in ' . $file . ' on line ' . $line);
        }

        // set the status code
        $protocol = 'HTTP/1.0';
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            $protocol = $_SERVER['SERVER_PROTOCOL'];
        }
        header($protocol . ' ' . $statusCode);

        // set the headers
        foreach ($headers as $header) {
            header((string) $header, false);
        }
    }

}