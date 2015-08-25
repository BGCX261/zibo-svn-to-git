<?php

namespace zibo\orm;

use zibo\admin\model\module\Installer;
use zibo\admin\view\taskbar\Menu;
use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Controller;
use zibo\core\Dispatcher;
use zibo\core\Zibo;

use zibo\library\database\DatabaseManager;
use zibo\library\filesystem\File;
use zibo\library\i18n\I18n;
use zibo\library\orm\query\CachedModelQuery;
use zibo\library\orm\ModelManager;

/**
 * Initializator of the ORM module
 */
class Module {

    /**
     * Permission needed to perform maintance on the models
     * @var string
     */
    const PERMISSION_MODELS = 'orm.models';

    /**
     * Permission needed to get an export of the database
     * @var string
     */
    const PERMISSION_EXPORT = 'orm.models.export';

    /**
     * Route to the models administration
     * @var string
     */
    const ROUTE_ADMIN = 'admin/orm/models';

    /**
     * Route to the log of the ORM
     * @var string
     */
    const ROUTE_LOG = 'admin/orm/log';

    /**
     * Route to the ajax controller of the log of the ORM
     * @var string
     */
    const ROUTE_LOG_AJAX = 'ajax/orm/log';

    /**
     * Route to the wizard of the administration
     * @var string
     */
    const ROUTE_WIZARD = 'admin/orm/models/wizard';

    /**
     * Name for the log messages of this module
     * @var string
     */
    const LOG_NAME = 'orm';

    /**
     * Install the orm module, check for a valid database connection
     * @return null
     * @throws zibo\library\database\exception\DatabaseException when no connection is setup
     */
	public function install() {
//	    DatabaseManager::getInstance()->getConnection();
	}

	/**
	 * Initialize the orm module for the request, register orm event listeners
	 * @return null
	 */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(Zibo::EVENT_CLEAR_CACHE, array($this, 'clearCache'));
        $zibo->registerEventListener(Installer::EVENT_PRE_INSTALL_SCRIPT, array($this, 'defineModelsForInstalledModules'));
        $zibo->registerEventListener(Installer::EVENT_POST_UNINSTALL_MODULE, array($this, 'deleteModelsForUninstalledModules'));
        $zibo->registerEventListener(Dispatcher::EVENT_PRE_DISPATCH, array($this, 'prepareController'));
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Clears the cache of the ORM module
     * @return null
     */
    public function clearCache() {
        ModelManager::getInstance()->clearCache();
    }

    /**
     * Defines the models for the installed modules
     * @param zibo\admin\model\Installer $installer Instance of the installer
     * @param zibo\library\filesystem\File $modulePath Path of the modules
     * @param array $modules The modules which are installed
     * @return null
     */
    public function defineModelsForInstalledModules(Installer $installer, File $modulePath, array $modules) {
        $connections = DatabaseManager::getInstance()->getConnections();
        if (!$connections) {
            return;
        }

        ModelManager::getInstance()->defineModels();
    }

    /**
     * Deletes the models for the uninstalled modules
     * @param zibo\admin\model\Installer $installer Instance of the installer
     * @param zibo\library\filesystem\File $modulePath Path of the modules
     * @param array $modules The modules which are uninstalled
     * @return null
     */
    public function deleteModelsForUninstalledModules(Installer $installer, File $modulePath, array $modules) {
        $connections = DatabaseManager::getInstance()->getConnections();
        if (!$connections) {
            return;
        }

        ModelManager::getInstance()->defineModels();
    }

    /**
     * Prepare the controller by loading the needed models into the $models variable based on the $useModels variable in the controller
     *
     * The $useModels variable should be defined public in your controller. This can be a string with 1 model name or an array with multiple model names. Those models
     * will be loaded into the $models variable of your controller before the controller is dispatched.
     *
     * @param zibo\core\Controller $controller Instance of the controller to be dispatched
     * @param string $actionName The name of the action to execute
     * @param array $parameters The parameters for the action
     * @return null
     */
    public function prepareController(Controller $controller, $actionName, array $parameters) {
        if (!isset($controller->useModels)) {
            return;
        }

        if (!is_array($controller->useModels)) {
            $models = array($controller->useModels);
        } else {
            $models = $controller->useModels;
        }

        $manager = ModelManager::getInstance();

        $controller->models = array();
        foreach ($models as $modelName) {
            $controller->models[$modelName] = $manager->getModel($modelName);
        }
    }

    /**
     * Add the orm menu to the taskbar
     * @param zibo\admin\view\taskbar\Taskbar $taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $translator = I18n::getInstance()->getTranslator();

        $ormMenu = new Menu($translator->translate('orm.title'));
        $ormMenu->addMenuItem(new MenuItem($translator->translate('orm.title.models'), self::ROUTE_ADMIN));
        $ormMenu->addMenuItem(new MenuItem($translator->translate('orm.title.log'), self::ROUTE_LOG));

        $settingsMenu = $taskbar->getSettingsMenu();
        $settingsMenu->addMenu($ormMenu);
    }

}