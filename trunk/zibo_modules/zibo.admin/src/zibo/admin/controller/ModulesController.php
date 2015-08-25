<?php

namespace zibo\admin\controller;

use zibo\admin\form\ModuleInstallForm;
use zibo\admin\model\module\Installer;
use zibo\admin\table\ModulesTable;
use zibo\admin\view\modules\ModulesView;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\validation\exception\ValidationException;

use zibo\repository\ClientModule;

use \Exception;

/**
 * Controller to install and uninstall modules
 */
class ModulesController extends AbstractController {

    /**
     * Class name of the repository client
     * @var string
     */
    const CLASS_REPOSITORY_CLIENT = 'zibo\repository\ClientModule';

    /**
     * Translation key for the page title
     * @var string
     */
    const TRANSLATION_TITLE = 'modules.title';

    /**
     * Translation key for the view description
     * @var string
     */
    const TRANSLATION_DESCRIPTION = 'modules.description';

    /**
     * Translation key for an install error message
     * @var string
     */
    const TRANSLATION_ERROR_INSTALL_FILE = 'modules.error.file.installed';

    /**
     * Translation key for an uninstall error message
     * @var string
     */
    const TRANSLATION_ERROR_UNINSTALL_FILE = 'modules.error.file.uninstalled';

    /**
     * Translation key for an install information message
     * @var string
     */
    const TRANSLATION_FILE_INSTALLED = 'modules.information.file.installed';

    /**
     * Translation key for an uninstall information message
     * @var string
     */
    const TRANSLATION_FILE_UNINSTALLED = 'modules.information.file.uninstalled';

    /**
     * Translation key for the action to the repository client
     * @var string
     */
    const TRANSLATION_REPOSITORY_CLIENT = 'modules.button.repository';

    /**
     * Installer of modules
     * @var zibo\admin\model\Installer
     */
    private $installer;

    /**
     * Initializes the module installer
     * @return null
     */
    public function preAction() {
        $this->installer = new Installer();
    }

    /**
     * Sets a modules index view to the response
     * @return null
     */
    public function indexAction() {
        $this->setIndexView();
    }

    /**
     * Installs a module provided by a ModuleInstallForm
     * @return null
     */
    public function installAction() {
        $form = new ModuleInstallForm($this->request->getBasePath() . '/install');
        if (!$form->isSubmitted()) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $exception = null;

        try {
            $form->validate();

            $file = $form->getModuleFile();

            $this->installer->installModule($file);
        } catch (ValidationException $validationException) {
            $this->setIndexView($form);
            return;
        } catch (Exception $installException) {
            $exception = $installException;
        }

        if ($file->exists()) {
            $file->delete();
        }

        if ($exception != null) {
            $this->addError(self::TRANSLATION_ERROR_INSTALL_FILE, array('error' => $exception->getMessage(), 'file' => $file->getName()));
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
        } else {
            $this->addInformation(self::TRANSLATION_FILE_INSTALLED, array('file' => $file->getName()));
        }

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Runs the install script on the provided path. The path is provided as path arguments.
     * @return null
     */
    public function reinstallAction() {
        $path = implode(File::DIRECTORY_SEPARATOR, func_get_args());

        if (empty($path)) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        try {
            $file = new File($path);

            $this->installer->runInstallScript($file);

            $this->addInformation(self::TRANSLATION_FILE_INSTALLED, array('file' => $file->getName()));
        } catch (Exception $exception) {
            $this->addError(self::TRANSLATION_ERROR_INSTALL_FILE, array('error' => $exception->getMessage(), 'file' => $file->getName()));
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
        }

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Uninstalls to module of the provided path. The path is provided as path arguments.
     * eg. to uninstall file modules/zibo.encryption-0.1.0.phar provide 2 arguments: modules and zibo.encryption-0.1.0.phar
     * @return null
     */
    public function uninstallAction() {
        $path = implode(File::DIRECTORY_SEPARATOR, func_get_args());

        if (empty($path)) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        try {
            $file = new File($path);

            $this->installer->uninstallModule($file);

            $this->addInformation(self::TRANSLATION_FILE_UNINSTALLED, array('file' => $file->getName()));
        } catch (Exception $exception) {
            $this->addError(self::TRANSLATION_ERROR_UNINSTALL_FILE, array('error' => $exception->getMessage(), 'file' => $file->getName()));
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
        }

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Sets the index view of the modules to the response
     * @param zibo\admin\form\ModuleInstallForm $form
     * @return zibo\admin\view\modules\ModulesView
     */
    private function setIndexView(ModuleInstallForm $form = null) {
        if (!$form) {
            $form = new ModuleInstallForm($this->request->getBasePath() . '/install');
        }

        $translator = $this->getTranslator();
        $modules = $this->installer->getModules();

        $table = new ModulesTable($this->request->getBasePath(), $modules);

        $view = new ModulesView($form, $table);
        $view->setPageTitle($translator->translate(self::TRANSLATION_TITLE));

        $sidebar = $view->getSidebar();
        $sidebar->setInformation($translator->translate(self::TRANSLATION_DESCRIPTION));

        if (class_exists(self::CLASS_REPOSITORY_CLIENT)) {
            $url = $this->request->getBaseUrl() . '/'. ClientModule::ROUTE;
            $sidebar->addAction($url, self::TRANSLATION_REPOSITORY_CLIENT, true);
        }

        $this->response->setView($view);
    }

}