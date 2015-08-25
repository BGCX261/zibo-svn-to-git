<?php

namespace zibo\install;

use zibo\core\Dispatcher;
use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

use zibo\install\model\config\io\InstallConfigIO;
use zibo\install\view\ExceptionView;

use zibo\library\config\Config;
use zibo\library\filesystem\File;

use \Exception;
use \ReflectionProperty;

class Module {

    const CONTROLLER_INSTALL = 'zibo\\install\\controller\\InstallController';

    public function initialize() {
        $this->zibo = Zibo::getInstance();

        $this->zibo->registerEventListener(Zibo::EVENT_POST_ROUTE, array($this, 'overrideRequest'));

        $this->zibo->clearEventListeners(Zibo::EVENT_ERROR);
        $this->zibo->registerEventListener(Zibo::EVENT_ERROR, array($this, 'setExceptionView'));

        $this->zibo->clearEventListeners(Zibo::EVENT_PRE_RESPONSE);

        $this->overrideZiboConfigIO();
    }

    public function overrideRequest() {
        $request = $this->zibo->getRequest();
        $route = $request->getRoute();

        if (substr($route, 0, 4) == '/web') {
            return;
        }

        $request = new Request($request->getBaseUrl(), $request->getBaseUrl() . '/install', self::CONTROLLER_INSTALL, Dispatcher::ACTION_ASTERIX, $request->getParameters());

        $this->zibo->setRequest($request);
    }

    /**
     * Sets an exception view for the provided exception to the response.
     * @param Exception $exception
     * @return null
     */
    public function setExceptionView(Exception $exception) {
        $view = new ExceptionView($exception);

        $response = Zibo::getInstance()->getResponse();
        $response->clearRedirect();
        $response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
        $response->setView($view);
    }

    /**
     * Overrides the Zibo config IO so we have control over certain config values like session path (dirty hack)
     * @return null
     */
    private function overrideZiboConfigIO() {
        $reflectionProperty = new ReflectionProperty(get_class($this->zibo), 'browser');
        $reflectionProperty->setAccessible(true);
        $browser = $reflectionProperty->getValue($this->zibo);
        $environment = $this->zibo->getEnvironment();

        $io = new InstallConfigIO($environment, $browser);
        $config = new Config($io);

        $reflectionProperty = new ReflectionProperty(get_class($this->zibo), 'config');
        $reflectionProperty->setAccessible(true);
        $config = $reflectionProperty->setValue($this->zibo, $config);
    }

    /**
     * Gets the temporary directory for the installation process
     * @param string $path The path in the temporary directory
     * @return zibo\library\filesystem\File
     */
    public static function getTempDirectory($path = null) {
        $rootDirectory = Zibo::getInstance()->getRootPath();
        $rootDirectory = $rootDirectory->getPath();

        $path = 'zibo-' . substr(md5($rootDirectory), 0, 7) . ($path ? '/' . $path : '');

        $temp = new File(sys_get_temp_dir(), $path);
        $temp->create();

        return $temp;
    }

}