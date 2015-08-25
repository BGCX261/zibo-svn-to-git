<?php

namespace zibo\repository\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\model\module\Installer;

use zibo\repository\model\Client;
use zibo\repository\model\Module;
use zibo\repository\table\decorator\ModuleInstallUpgradeActionDecorator;
use zibo\repository\table\ModuleTable;
use zibo\repository\table\ModuleVersionTable;
use zibo\repository\table\NamespaceTable;
use zibo\repository\view\ModuleView;
use zibo\repository\view\RepositoryView;
use zibo\repository\ClientModule;

use zibo\core\Zibo;

use zibo\library\html\Breadcrumbs;
use zibo\library\Number;
use zibo\library\Timer;

use zibo\ZiboException;

use Exception;

/**
 * Controller to browse the repository and to install modules thereof
 */
class ClientController extends AbstractController {

    /**
     * Name of the action to display the details of a module
     * @var string
     */
    const ACTION_MODULE = 'module';

    /**
     * Name of the action to display the modules of a namespace
     * @var string
     */
    const ACTION_NAMESPACE = 'namespace';

    /**
     * Name of the action to install a new module
     * @var string
     */
    const ACTION_INSTALL = 'install';

    /**
     * Name of the action to upgrade a new module
     * @var string
     */
    const ACTION_UPGRADE = 'upgrade';

    /**
     * Name for the page parameter
     * @var string
     */
    const PARAMETER_PAGE = 'page';

    /**
     * Name for the rows parameter
     * @var string
     */
    const PARAMETER_ROWS = 'rows';

    /**
     * Name for the search parameter
     * @var string
     */
    const PARAMETER_SEARCH = 'search';

    /**
     * Translation key for the label of the namespace breadcrumbs
     * @var string
     */
    const TRANSLATION_NAMESPACE = 'repository.label.namespace';

    /**
     * Translation key for the home of the namespace breadcrumbs
     * @var string
     */
    const TRANSLATION_NAMESPACE_HOME = 'repository.label.namespace.home';

    /**
     * Translation key for the module installed information message
     * @var string
     */
    const TRANSLATION_MODULE_INSTALLED = 'repository.information.module.installed';

    /**
     * Translation key for the module install error message
     * @var string
     */
    const TRANSLATION_MODULE_INSTALL_FAILED = 'repository.error.module.install.failed';

    /**
     * Translation key for the module upgraded information message
     * @var string
     */
    const TRANSLATION_MODULE_UPGRADED = 'repository.information.module.upgraded';

    /**
     * Translation key for the module upgrade error message
     * @var string
     */
    const TRANSLATION_MODULE_UPGRADE_FAILED = 'repository.error.module.upgrade.failed';

    /**
     * Module installer
     * @var zibo\admin\model\Installer
     */
    private $installer;

    /**
     * The repository client
     * @var zibo\repository\model\Client
     */
    private $client;

    /**
     * Initializes the installer and the repository before every action
     * @return null
     */
    public function preAction() {
        $this->installer = new Installer();
        $this->client = ClientModule::getClient($this->installer);

        if (!$this->client) {
            throw new ZiboException('The client module needs to be configured, missing setting ' . ClientModule::CONFIG_REPOSITORY);
        }

        $translator = $this->getTranslator();

        $this->breadcrumbs = new Breadcrumbs();
        $this->breadcrumbs->setLabel($translator->translate(self::TRANSLATION_NAMESPACE));
        $this->breadcrumbs->addBreadcrumb($this->request->getBasePath(), $translator->translate(self::TRANSLATION_NAMESPACE_HOME));
    }

    /**
     * Action to show an overview of the namespaces
     * @return null
     */
    public function indexAction() {
        $arguments = $this->parseArguments(func_get_args());
        if (!$arguments) {
            $url = $this->request->getBasePath() . '/' . self::PARAMETER_PAGE . '/1/' . self::PARAMETER_ROWS . '/10';
            $this->response->setRedirect($url);
            return;
        }

        $page = $arguments[self::PARAMETER_PAGE];
        $rows = $arguments[self::PARAMETER_ROWS];

        $query = null;
        if (array_key_exists(self::PARAMETER_SEARCH, $arguments)) {
            $query = urldecode($arguments[self::PARAMETER_SEARCH]);
        }

        $table = $this->getNamespacesTable($page, $rows, $query);
        $table->processForm();

        $newPage = $table->getPage();
        $newRows = $table->getRowsPerPage();
        $newQuery = $table->getSearchQuery();

        if ($newPage != $page || $newRows != $rows || $newQuery != $query) {
            $url = $this->request->getBasePath() . '/' . self::PARAMETER_PAGE . '/' . $newPage;
            $url .= '/' . self::PARAMETER_ROWS . '/' . $newRows;

            if ($newQuery) {
                $url .= '/' . self::PARAMETER_SEARCH . '/' . urlencode($newQuery);
            }

            $this->response->setRedirect($url);
            return;
        }

        $view = new RepositoryView(ClientModule::TRANSLATION_TITLE, $table, null, $this->breadcrumbs);
        $this->response->setView($view);
    }

    /**
     * Action to show the modules of a namespace
     * @return null
     */
    public function namespaceAction() {
        $arguments = func_get_args();
        if (!$arguments) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $namespace = array_shift($arguments);
        $arguments = $this->parseArguments($arguments);

        if (!$arguments) {
            $url = $this->request->getBasePath() . '/' . self::ACTION_NAMESPACE . '/' . $namespace . '/' . self::PARAMETER_PAGE . '/1/' . self::PARAMETER_ROWS . '/10';
            $this->response->setRedirect($url);
            return;
        }

        $page = $arguments[self::PARAMETER_PAGE];
        $rows = $arguments[self::PARAMETER_ROWS];

        $query = null;
        if (array_key_exists(self::PARAMETER_SEARCH, $arguments)) {
            $query = urldecode($arguments[self::PARAMETER_SEARCH]);
        }

        $table = $this->getModulesTable($namespace, $page, $rows, $query);
        $table->processForm();

        $newPage = $table->getPage();
        $newRows = $table->getRowsPerPage();
        $newQuery = $table->getSearchQuery();

        if ($newPage != $page || $newRows != $rows || $newQuery != $query) {
            $url = $this->request->getBasePath() . '/' . self::ACTION_NAMESPACE . '/' . $namespace;
            $url .= '/' . self::PARAMETER_PAGE . '/' . $newPage;
            $url .= '/' . self::PARAMETER_ROWS . '/' . $newRows;

            if ($newQuery) {
                $url .= '/' . self::PARAMETER_SEARCH . '/' . urlencode($newQuery);
            }

            $this->response->setRedirect($url);
            return;
        }

        $view = new RepositoryView(ClientModule::TRANSLATION_TITLE, $table, null, $this->breadcrumbs);
        $this->response->setView($view);
    }

    /**
     * Action to show the details of a module
     * @param string $namespace The namespace of the modules
     * @param string $name The name of the module
     * @return null
     */
    public function moduleAction($namespace = null, $name = null) {
        $module = $this->client->getModule($namespace, $name);

        $table = $this->getModuleTable($module);

        $basePath = $this->request->getBasePath();
        $repositoryUrl = $basePath . '/' . self::ACTION_NAMESPACE . '/' . $namespace;
        $moduleUrl = $basePath . '/' . self::ACTION_MODULE . '/';

        $view = new ModuleView($module, $table, $repositoryUrl, $moduleUrl);
        $this->response->setView($view);
    }

    /**
     * Action to install a module from the repository
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version to install
     * @return null
     */
    public function installAction($namespace = null, $name = null, $version = null) {
        $this->installModule($namespace, $name, $version, self::TRANSLATION_MODULE_INSTALLED, self::TRANSLATION_MODULE_INSTALL_FAILED);
    }

    /**
     * Action to upgrade a module to the provided version
     * @param string $namespace The namespace of the modle
     * @param string $name The name of the module
     * @param string $version The version of the module
     * @return null
     */
    public function upgradeAction($namespace = null, $name = null, $version = null) {
        $this->installModule($namespace, $name, $version, self::TRANSLATION_MODULE_UPGRADED, self::TRANSLATION_MODULE_UPGRADE_FAILED);
    }

    /**
     * Install a module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version of the module
     * @param string $translationSuccess The translation key for the success message
     * @param string $translationFail The translation key for the fail message
     * @return null
     */
    private function installModule($namespace, $name, $version, $translationSuccess, $translationFail) {
        $parameters = array(
            'namespace' => $namespace,
            'name' => $name,
            'version' => $version,
        );

        try {
            $this->client->installModuleVersion($namespace, $name, $version);
            $this->addInformation($translationSuccess, $parameters);
        } catch (Exception $exception) {
            $parameters['error'] = $exception->getMessage();
            $this->addError($translationFail, $parameters);
        }

        $this->response->setRedirect($this->getReferer());
    }

    /**
     * Creates a table for the namespaces
     * @param integer $page The number of the current page
     * @param integer $rows The number of rows per page
     * @param string|null $query The search query
     * @return zibo\repository\table\NamespaceTable
     */
    private function getNamespacesTable($page, $rows, $query = null) {
        $namespaces = $this->client->getNamespaces();

        $basePath = $this->request->getBasePath();
        $namespaceAction = $basePath . '/' . self::ACTION_NAMESPACE . '/';
        $tableAction = $basePath . '/' . self::PARAMETER_PAGE . '/' . $page . '/' . self::PARAMETER_ROWS . '/' . $rows;
        if ($query) {
            $tableAction .= '/' . self::PARAMETER_SEARCH . '/' . urlencode($query);
        }

        $table = new NamespaceTable($namespaces, $namespaceAction, $tableAction);
        $table->setPaginationOptions(array(5, 10, 25, 50, 100));
        $table->setRowsPerPage($rows);
        $table->setPage($page);
        $table->setPaginationUrl(str_replace(self::PARAMETER_PAGE . '/' . $page, self::PARAMETER_PAGE . '/%page%', $tableAction));

        if ($query) {
            $table->setSearchQuery($query);
        }

        return $table;
    }

    /**
     * Creates a table for the modules of the provided namespace
     * @param string $namespace The namespace to get the modules of
     * @param integer $page The number of the current page
     * @param integer $rows The number of rows per page
     * @param string|null $query The search query
     * @return zibo\repository\table\ModuleTable
     */
    private function getModulesTable($namespace, $page, $rows, $query = null) {
        $basePath = $this->request->getBasePath();
        $moduleAction = $basePath . '/' . self::ACTION_MODULE . '/' . $namespace . '/';
        $tableAction = $basePath . '/' . self::ACTION_NAMESPACE . '/' . $namespace . '/' . self::PARAMETER_PAGE . '/' . $page . '/' . self::PARAMETER_ROWS . '/' . $rows;
        if ($query) {
            $tableAction .= '/' . self::PARAMETER_SEARCH . '/' . urlencode($query);
        }

        $installAction = $basePath . '/' . self::ACTION_INSTALL . '/';
        $upgradeAction = $basePath . '/' . self::ACTION_UPGRADE . '/';
        $decorator = new ModuleInstallUpgradeActionDecorator($this->installer, $installAction, $upgradeAction);

        $namespace = $this->client->getNamespace($namespace);
        $modules = $namespace->getModules();

        $table = new ModuleTable($modules, $moduleAction, $tableAction);
        $table->addDecorator($decorator);
        $table->setPaginationOptions(array(5, 10, 25, 50, 100));
        $table->setRowsPerPage($rows);
        $table->setPage($page);
        $table->setPaginationUrl(str_replace(self::PARAMETER_PAGE . '/' . $page, self::PARAMETER_PAGE . '/%page%', $tableAction));

        if ($query) {
            $table->setSearchQuery($query);
        }

        $this->breadcrumbs->addBreadcrumb($tableAction, $namespace->getName());

        return $table;
    }

    /**
     * Creates a table for the version of the provided module
     * @param Module $module
     * @return zibo\repository\table\ModuleVersionTable
     */
    private function getModuleTable(Module $module) {
        $basePath = $this->request->getBasePath() . '/';
        $installAction = $basePath . self::ACTION_INSTALL . '/';
        $upgradeAction = $basePath . self::ACTION_UPGRADE . '/';
        $decorator = new ModuleInstallUpgradeActionDecorator($this->installer, $installAction, $upgradeAction);

        $table = new ModuleVersionTable($module->getVersions());
        $table->addDecorator($decorator);

        return $table;
    }

}