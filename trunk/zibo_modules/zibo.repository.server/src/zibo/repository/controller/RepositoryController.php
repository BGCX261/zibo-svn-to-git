<?php

namespace zibo\repository\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\view\DownloadView;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\html\Breadcrumbs;
use zibo\library\validation\exception\ValidationException;

use zibo\repository\form\ModuleUploadForm;
use zibo\repository\model\exception\ModuleVersionAlreadyExistsException;
use zibo\repository\model\exception\ModuleNotFoundException;
use zibo\repository\model\Module;
use zibo\repository\model\Repository;
use zibo\repository\table\ModuleTable;
use zibo\repository\table\ModuleVersionTable;
use zibo\repository\table\NamespaceTable;
use zibo\repository\view\ModuleView;
use zibo\repository\view\RepositoryView;
use zibo\repository\ServerModule;

use \Exception;

/**
 * Repository management controller
 */
class RepositoryController extends AbstractController {

    /**
     * Name of the action to add a module to the repository
     * @var string
     */
    const ACTION_ADD = 'add';

    /**
     * Name of the action to download  a module
     * @var string
     */
    const ACTION_DOWNLOAD = 'download';

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
     * Name of the action to delete a module from the repository
     * @var string
     */
    const ACTION_DELETE = 'delete';

    /**
     * Translation key for the module added information message
     * @var string
     */
    const TRANSLATION_MODULE_ADDED = 'repository.information.module.added';

    /**
     * Translation key for the module removed information message
     * @var string
     */
    const TRANSLATION_MODULE_DELETED_VERSION = 'repository.information.module.deleted.version';

    /**
     * Translation key for the error message when a uploaded module already exists with the provided version
     * @var string
     */
    const TRANSLATION_MODULE_EXISTS = 'repository.error.module.exists';

    /**
     * Translation key for the download button
     * @var string
     */
    const TRANSLATION_DOWNLOAD = 'repository.button.download';

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
     * The repository of the modules
     * @var zibo\repository\model\Repository
     */
    private $repository;

    /**
     * The breadcrumbs of the repository
     * @var zibo\library\html\Breadcrumbs
     */
    private $breadcrumbs;

    /**
     * Initializes the repository before every action
     * @return null
     */
    public function preAction() {
        $translator = $this->getTranslator();

        $path = Zibo::getInstance()->getConfigValue(ServerModule::CONFIG_DIRECTORY_REPOSITORY, ServerModule::DIRECTORY_REPOSITORY);
        $directory = new File($path);

        $this->repository = new Repository($directory);

        $this->breadcrumbs = new Breadcrumbs();
        $this->breadcrumbs->setLabel($translator->translate(self::TRANSLATION_NAMESPACE));
        $this->breadcrumbs->addBreadcrumb($this->request->getBasePath(), $translator->translate(self::TRANSLATION_NAMESPACE_HOME));
    }

    /**
     * Action to show the namespaces of the repository
     * @return null
     */
    public function indexAction() {
        $view = $this->getNamespacesView();
        $this->response->setView($view);
    }

    /**
     * Action to show the modules of a namespace
     * @param $namespace
     * @param $page
     */
    public function namespaceAction($namespace = null) {
        $view = $this->getModulesView($namespace);
        $this->response->setView($view);
    }

    /**
     * Action to show the detail of a module
     * @param string $namespace Namespace of the module
     * @param string $name Name of the module
     * @return null
     */
    public function moduleAction($namespace = null, $name = null) {
        try {
            $module = $this->repository->getModule($namespace, $name);
        } catch (ModuleNotFoundException $exception) {
            $this->setError404();
            return;
        }

        $view = $this->getModuleView($module);
        $this->response->setView($view);
    }

    /**
     * Action to download a module
     * @param string $namespace The namespace of the module
     * @param string $name The name of the module
     * @param string $version The version of the module
     * @return null
     */
    public function downloadAction($namespace = null, $name = null, $version = null) {
        try {
            $file = $this->repository->getModuleFileForVersion($namespace, $name, $version);
        } catch (ModuleNotFoundException $exception) {
            $this->setError404();
            return;
        }

        $view = new DownloadView($file);
        $this->response->setView($view);
    }

    /**
     * Action to add a module to the repository
     * @return null
     */
    public function addAction() {
        $form = $this->createModuleUploadForm();
        if (!$form->isSubmitted()) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $this->response->setRedirect($this->getReferer());
        $exception = null;

        try {
            $form->validate();

            $file = $form->getModule();

            $this->repository->addModule($file);

            $this->addInformation(self::TRANSLATION_MODULE_ADDED, array('file' => $file->getName()));
        } catch (ModuleVersionAlreadyExistsException $e) {
            $parameters = array(
                'namespace' => $e->getNamespace(),
                'name' => $e->getName(),
                'version' => $e->getVersion(),
            );

            $this->addError(self::TRANSLATION_MODULE_EXISTS, $parameters);
        } catch (ValidationException $e) {
            $this->response->setRedirect($this->getReferer());
        } catch (Exception $e) {
            $this->response->clearRedirect();
            $exception = $e;
        }

        if (isset($file) && $file->exists()) {
            $file->delete();
        }

        if ($exception != null) {
            throw $exception;
        }
    }

    /**
     * Action to remove a module from the repository
     * @param string $namespace Namespace of the module
     * @param string $name Name of the module
     * @param string $version Version of the module
     * @return null
     */
    public function deleteAction($namespace = null, $name = null, $version = null) {
        $status = $this->repository->deleteModuleVersion($namespace, $name, $version);

        $parameters = array(
            'namespace' => $namespace,
            'name' => $name,
            'version' => $version,
        );
        $this->addInformation(self::TRANSLATION_MODULE_DELETED_VERSION, $parameters);

        if ($status === Repository::DELETED_MODULE) {
            $this->response->setRedirect($this->request->getBasePath());
        } else {
            $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_MODULE . '/' . $namespace . '/' . $name);
        }
    }

    /**
     * Creates a view for the namespaces
     * @param zibo\repository\form\ModuleUploadForm $form Form to upload new modules
     * @return zibo\repository\view\RepositoryView
     */
    private function getNamespacesView(ModuleUploadForm $form = null) {
        $namespaces = $this->repository->getNamespaces();

        $basePath = $this->request->getBasePath();
        $namespaceAction = $basePath . '/' . self::ACTION_NAMESPACE . '/';

        $table = new NamespaceTable($namespaces, $namespaceAction, $basePath);

        if (!$form) {
            $form = $this->createModuleUploadForm();
        }

        return new RepositoryView(ServerModule::TRANSLATION_TITLE, $table, $form, $this->breadcrumbs);
    }

    /**
     * Creates a view for the modules in the provided namespace
     * @param string $namespace Namespace of the modules
     * @return zibo\repository\view\RepositoryView
     */
    private function getModulesView($namespace) {
        $modules = $this->repository->getModules($namespace);

        $basePath = $this->request->getBasePath();
        $tableAction = $basePath . '/' . self::ACTION_NAMESPACE . '/' . $namespace;
        $moduleAction = $basePath . '/' . self::ACTION_MODULE . '/' . $namespace . '/';

        $table = new ModuleTable($modules, $moduleAction, $tableAction);
        $form = $this->createModuleUploadForm();

        $this->breadcrumbs->addBreadcrumb($tableAction, $namespace);

        return new RepositoryView(ServerModule::TRANSLATION_TITLE, $table, $form, $this->breadcrumbs);
    }

    /**
     * Gets the detail view of a module
     * @param Module $module Module to display in the view
     * @return zibo\repository\view\ModuleView
     */
    private function getModuleView(Module $module) {
        $namespace = $module->getNamespace();
        $name = $module->getName();

        $basePath = $this->request->getBasePath() . '/';
        $repositoryUrl = $basePath . self::ACTION_NAMESPACE . '/' . $namespace;
        $moduleUrl = $basePath . self::ACTION_MODULE . '/';
        $downloadUrl = $basePath . self::ACTION_DOWNLOAD . '/' . $namespace . '/' . $name . '/';

        $table = new ModuleVersionTable($module->getVersions(), $downloadUrl, $moduleUrl . $namespace . '/' . $name);

        $this->breadcrumbs->addBreadcrumb($repositoryUrl, $namespace);
        $this->breadcrumbs->addBreadcrumb($moduleUrl . '/' . $namespace . '/' . $name, $name);

        return new ModuleView($module, $table, $repositoryUrl, $moduleUrl, self::TRANSLATION_DOWNLOAD, $downloadUrl . $module->getVersion());
    }

    /**
     * Creates a new module upload form
     * @return zibo\repository\form\ModuleUploadForm
     */
    private function createModuleUploadForm() {
        return new ModuleUploadForm($this->request->getBasePath() . '/' . self::ACTION_ADD);
    }

}