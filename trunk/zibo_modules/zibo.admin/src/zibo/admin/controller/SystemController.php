<?php

namespace zibo\admin\controller;

use zibo\admin\view\system\ConfigurationView;
use zibo\admin\view\system\PhpInfoView;
use zibo\admin\view\system\SystemView;
use zibo\admin\view\BaseView;

use zibo\core\environment\CliEnvironment;
use zibo\core\Zibo;

use zibo\library\config\Config;
use zibo\library\filesystem\File;
use zibo\library\security\SecurityManager;
use zibo\library\SoftwareDetector;
use zibo\library\Structure;

/**
 * System information controller
 */
class SystemController extends AbstractController {

    /**
     * Translation key for the title of the system view
     * @var string
     */
    const TRANSLATION_TITLE = 'system.title';

    /**
     * Translation key for the view description
     * @var string
     */
    const TRANSLATION_DESCRIPTION = 'system.description';

    /**
     * Translation key for the system information action
     * @var string
     */
    const TRANSLATION_SYS_INFO = 'system.button.information';

    /**
     * Translation key for the phpinfo action
     * @var string
     */
    const TRANSLATION_PHP_INFO = 'system.button.phpinfo';

    /**
     * Translation key for the clear cache action
     * @var string
     */
    const TRANSLATION_CLEAR_CACHE = 'system.button.cache.clear';

    /**
     * Translation key for the information message when the cache has been cleared
     * @var string
     */
    const TRANSLATION_CACHE_CLEARED = 'system.information.cache.cleared';

    /**
    * Translation key for the reset file browser action
     * @var string
    */
    const TRANSLATION_REINDEX_FILE_BROWSER = 'system.button.filebrowser.reindex';

    /**
     * Translation key for the information message when the file browser has been resetted
     * @var string
     */
    const TRANSLATION_FILE_BROWSER_REINDEXED = 'system.information.filebrowser.reindexed';

    /**
     * Action to show an overview of the system information
     * @return null
     */
    public function indexAction() {
        $zibo = Zibo::getInstance();

        $configuration = $this->getConfiguration($zibo);
        $routes = $this->getRoutes($zibo);
        $softwareDetector = new SoftwareDetector();

        $sm = SecurityManager::getInstance();
        $numVisitors = $sm->getNumVisitors();
        $numUsers = $sm->getNumUsers();
        $numGuests = $numVisitors - $numUsers;
        $currentUsers = $sm->getCurrentUsers();

        $view = new SystemView($configuration, $this->request->getBaseUrl(), $routes, $softwareDetector, $numVisitors, $numUsers, $numGuests, $currentUsers);
        $actions = array($this->request->getBasePath() . '/phpInfo' => self::TRANSLATION_PHP_INFO);

        $this->setView($view, $actions);
    }

    /**
     * Action to show the phpinfo() output
     * @return null
     */
    public function phpInfoAction() {
        $view = new PhpInfoView();

        $actions = array($this->request->getBasePath() => self::TRANSLATION_SYS_INFO);

        $this->setView($view, $actions);
    }

    /**
     * Action to show the full configuration in the cli environment
     * @return null
     */
    public function configAction() {
        $zibo = Zibo::getInstance();

        $environment = $zibo->getEnvironment();
        if ($environment->getName() != CliEnvironment::NAME) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $configuration = $this->getConfiguration($zibo);

        $view = new ConfigurationView($configuration);
        $this->response->setView($view);
    }

    /**
     * Action to clear the Zibo cache
     * @return null
     */
    public function clearCacheAction() {
        Zibo::getInstance()->clearCache();

        $this->addInformation(self::TRANSLATION_CACHE_CLEARED);

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Action to reindex the file browser, this will reindex the files if the indexed file browser is being used
     * @return null
     */
    public function reindexFileBrowserAction() {
        Zibo::getInstance()->resetFileBrowser();

        $this->addInformation(self::TRANSLATION_FILE_BROWSER_REINDEXED);

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Populates the sidebar and sets the provided view to the response
     * @param zibo\admin\view\BaseView $view System view to render
     * @param array $actions Array with the URL of the action as key and the label as value
     * @return null
     */
    private function setView(BaseView $view, array $actions) {
        $translator = $this->getTranslator();

        $actions[$this->request->getBasePath() . '/clearCache'] = self::TRANSLATION_CLEAR_CACHE;
        $actions[$this->request->getBasePath() . '/reindexFileBrowser'] = self::TRANSLATION_REINDEX_FILE_BROWSER;

        $view->setPageTitle($translator->translate(self::TRANSLATION_TITLE));

        $sidebar = $view->getSidebar();
        $sidebar->setInformation($translator->translate(self::TRANSLATION_DESCRIPTION));
        foreach ($actions as $url => $label) {
            $sidebar->addAction($url, $translator->translate($label));
        }

        $this->response->setView($view);
    }

    /**
     * Gets a simple array of the Zibo configuration
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return array Array with the full configuration key as key and the configuration value as value
     */
    private function getConfiguration(Zibo $zibo) {
        $config = $zibo->getAllConfigValues();
        $config = $this->parseConfigurationArray($config);

        ksort($config);

        return $config;
    }

    /**
     * Parses a hierarchic array into a simple array
     * @param array $configuration Hierarchic array  with configuration values to simplify
     * @param string $prefix Prefix for the keys of the configuration array (needed for recursive calls)
     * @return array Simplified configuration
     */
    private function parseConfigurationArray(array $configuration, $prefix = null) {
        $result = array();

        if ($prefix) {
            $prefix .= Config::TOKEN_SEPARATOR;
        }

        foreach ($configuration as $key => $value) {
            $prefixedKey = $prefix . $key;

            if (is_array($value)) {
                $result = Structure::merge($result, $this->parseConfigurationArray($value, $prefixedKey));
            } else {
                $result[$prefixedKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Gets all the routes from Zibo
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @return array Array with the route as key and a Route object as value
     */
    private function getRoutes(Zibo $zibo) {
        $router = $zibo->getRouter();
        $routes = $router->getRoutes();

        ksort($routes);

        return $routes;
    }

}