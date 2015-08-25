<?php

namespace zibo\orm\builder\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\view\DownloadView;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\database\admin\controller\DatabaseController;
use zibo\database\admin\Module as DatabaseModule;

use zibo\library\filesystem\File;
use zibo\library\html\meta\RefreshMeta;
use zibo\library\orm\builder\ModelBuilder;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\erd\Erd;
use zibo\library\orm\model\LocalizedModel;
use zibo\library\orm\model\Model;
use zibo\library\orm\ModelManager;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\ObjectFactory;

use zibo\orm\builder\form\ModelFieldOrderForm;
use zibo\orm\builder\form\ModelFilterForm;
use zibo\orm\builder\form\ModelImportForm;
use zibo\orm\builder\table\decorator\ModelActionDecorator;
use zibo\orm\builder\table\decorator\ModelFieldOrderDecorator;
use zibo\orm\builder\table\ModelTable;
use zibo\orm\builder\table\ModelFieldTable;
use zibo\orm\builder\view\wizard\BuilderWizardView;
use zibo\orm\builder\view\BuilderView;
use zibo\orm\builder\view\ModelDetailView;
use zibo\orm\builder\view\ModelFilterView;
use zibo\orm\builder\view\ModelImportView;
use zibo\orm\builder\wizard\BuilderWizard;
use zibo\orm\scaffold\controller\ScaffoldController;
use zibo\orm\Module;

use \Exception;

/**
 * Controller for the model builder
 */
class BuilderController extends AbstractController {

    /**
     * Action to define the models
     * @var string
     */
    const ACTION_DEFINE = 'define';

    /**
     * Action to view the detail of a model
     * @var string
     */
    const ACTION_DETAIL = 'detail';

    /**
     * Action to download the export file
     * @var string
     */
    const ACTION_DOWNLOAD = 'download';

    /**
     * Action to download a ERD
     * @var string
     */
    const ACTION_ERD = 'erd';

    /**
     * Action to export a model
     * @var string
     */
    const ACTION_EXPORT = 'export';

    /**
     * Action to import a model
     * @var string
     */
    const ACTION_IMPORT = 'import';

    /**
     * Action to order the fields of a model
     * @var string
     */
    const ACTION_ORDER_FIELDS = 'orderFields';

    /**
     * Action to scaffold a model
     * @var string
     */
    const ACTION_SCAFFOLD = 'scaffold';

    /**
     * Path to the export models file
     * @var string
     */
    const FILE_EXPORT = 'application/data/models.xml';

    /**
     * Translation key the information label of the sidebar
     * @var string
     */
    const TRANSLATION_INFORMATION_BUILDER = 'orm.label.builder.description';

    /**
     * Translation key the information label of the sidebar
     * @var string
     */
    const TRANSLATION_INFORMATION_MODEL = 'orm.label.model.description';

    /**
     * Translation key for the title
     * @var string
     */
    const TRANSLATION_TITLE = 'orm.title.models';

    /**
     * Translation key for the add action
     * @var string
     */
    const TRANSLATION_ADD = 'orm.button.model.add';

    /**
     * Translation key for the back action
     * @var string
     */
    const TRANSLATION_BACK = 'orm.button.model.back';

    /**
     * Translation key for the define action
     * @var string
     */
    const TRANSLATION_DEFINE = 'orm.button.model.define';

    /**
     * Translation key for the erd action
     * @var string
     */
    const TRANSLATION_ERD = 'orm.button.erd';

    /**
     * Translation key for the edit action
     * @var string
     */
    const TRANSLATION_EDIT = 'orm.button.model.edit';

    /**
     * Translation key for the delete action
     * @var string
     */
    const TRANSLATION_DELETE = 'button.delete';

    /**
     * Translation key for the export action
     * @var string
     */
    const TRANSLATION_EXPORT = 'button.export';

    /**
     * Translation key for the database export action
     * @var string
     */
    const TRANSLATION_EXPORT_DATABASE = 'orm.button.database.export';

    /**
     * Translation key for the model export action
     * @var string
     */
    const TRANSLATION_EXPORT_MODEL = 'orm.button.model.export';

    /**
     * Translation key for the scaffold action
     * @var string
     */
    const TRANSLATION_SCAFFOLD = 'orm.button.scaffold';

    /**
     * Translation key for the scaffold action
     * @var string
     */
    const TRANSLATION_SCAFFOLD_MODEL = 'orm.button.model.scaffold';

    /**
     * Translation key for the delete confirmation message
     * @var string
     */
    const TRANSLATION_DELETE_CONFIRM = 'orm.label.model.delete.confirm';

    /**
     * Translation key for the error message of the delete action
     * @var string
     */
    const TRANSLATION_DELETE_ERROR = 'orm.error.model.delete';

    /**
     * Translation key for the success message of the delete action
     * @var string
     */
    const TRANSLATION_DELETE_SUCCESS = 'orm.message.model.delete';

    /**
     * Translation key for the success message of the delete action but the model still exists
     * @var string
     */
    const TRANSLATION_DELETE_SUCCESS_MODULE = 'orm.message.model.delete.module';

    /**
     * Translation key for the delete fields confirmation message
     * @var string
     */
    const TRANSLATION_DELETE_FIELDS_CONFIRM = 'orm.label.field.delete.confirm';

    /**
     * Translation key for the error message of the delete fields action
     * @var string
     */
    const TRANSLATION_DELETE_FIELDS_ERROR = 'orm.error.field.delete';

    /**
     * Translation key for the success message of the delete fields action
     * @var string
     */
    const TRANSLATION_DELETE_FIELDS_SUCCESS = 'orm.message.field.delete';

    /**
     * Translation key for the error message of the delete fields action
     * @var string
     */
    const TRANSLATION_ORDER_FIELDS_ERROR = 'orm.error.field.order';

    /**
     * Translation key for the success message of the delete fields action
     * @var string
     */
    const TRANSLATION_ORDER_FIELDS_SUCCESS = 'orm.message.field.order';

    /**
     * Translation key for the error message of the import action
     * @var string
     */
    const TRANSLATION_IMPORT_ERROR = 'orm.error.model.import';

    /**
     * Translation key for the success message of the import action
     * @var string
     */
    const TRANSLATION_IMPORT_SUCCESS = 'orm.message.model.import';

    /**
     * Configuration key for the layout of the ERD
     * @var string
     */
    const CONFIG_ERD_LAYOUT = 'orm.erd.layout';

    /**
     * The default class of the ERD layout
     * @var string
     */
    const DEFAULT_ERD_LAYOUT = 'zibo\\library\\orm\\erd\\layout\\GridLayout';

    /**
     * The interface of the ERD layout
     * @var string
     */
    const INTERFACE_ERD_LAYOUT = 'zibo\\library\\orm\\erd\\layout\\Layout';

    /**
     * Class name of the ERD object
     * @var string
     */
    const CLASS_ERD = 'zibo\\library\\orm\\erd\\Erd';

    /**
     * Class name of the database admin module
     * @var string
     */
    const CLASS_DATABASE_ADMIN = 'zibo\\database\\admin\\Module';

    /**
     * Flag to see if the user has read only
     * @var boolean
     */
    private $isReadOnly;

    /**
     * Hook before the action
     * @return null
     */
    public function preAction() {
        $this->isReadOnly = !SecurityManager::getInstance()->isPermissionAllowed(Module::PERMISSION_MODELS);
    }

    /**
     * Action to process and view the main builder view
     * @return null
     */
    public function indexAction() {
        $basePath = $this->request->getBasePath();

        $filterForm = new ModelFilterForm($basePath);
        $filterForm->isSubmitted();

        $table = $this->getModelTable($basePath, $filterForm);
        $table->processForm();

        if ($this->response->getView() || $this->response->willRedirect()) {
            return;
        }

        $importForm = new ModelImportForm($basePath . '/' . self::ACTION_IMPORT);

        $this->setBuilderView($table, $filterForm, $importForm);
    }

    /**
     * Action to view the detail of a model
     * @param string $modelName Name of the model
     * @return null
     */
    public function detailAction($modelName) {
        $basePath = $this->request->getBasePath();
        $modelAction = $basePath . '/' . self::ACTION_DETAIL . '/';
        if (!$this->isReadOnly) {
            $editModelAction = $this->request->getBaseUrl() . '/' . Module::ROUTE_WIZARD . '/' . WizardController::ACTION_MODEL . '/' . $modelName;
            $editAction = $editModelAction . '/' . WizardController::ACTION_FIELD . '/';
        } else {
            $editModelAction = null;
            $editAction = null;
        }
        $tableAction = $modelAction . $modelName;

        $orderForm = null;

        $this->model = ModelManager::getInstance()->getModel($modelName);

        $table = new ModelFieldTable($this->model->getMeta()->getModelTable(), $tableAction, $editAction, $modelAction);

        if (!$this->isReadOnly) {
            $translator = $this->getTranslator();

            $table->addDecorator(new ModelFieldOrderDecorator(), null, true);

            $table->addAction(
                $translator->translate(self::TRANSLATION_DELETE),
                array($this, 'deleteFieldsAction'),
                $translator->translate(self::TRANSLATION_DELETE_FIELDS_CONFIRM)
            );

            $table->processForm();

            $orderAction = $basePath . '/' . self::ACTION_ORDER_FIELDS . '/' . $modelName;
            $orderForm = new ModelFieldOrderForm($orderAction);
        }

        if ($this->response->getView() || $this->response->willRedirect()) {
            return;
        }

        $view = $this->getModelDetailView($this->model, $table, $orderForm, $editModelAction);

        $this->response->setView($view);
    }

    /**
     * Action to delete the fields of a model
     * @param string|array $fields
     * @return null
     */
    public function deleteFieldsAction($fields) {
        if (!$this->model) {
            return;
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $fieldNames = array();
        foreach ($fields as $field) {
            if ($field instanceof ModelField) {
                $fieldNames[] = $field->getName();
            } else {
                $fieldNames[] = $field;
            }
        }

        $modelTable = $this->model->getMeta()->getModelTable();
        foreach ($fieldNames as $fieldName) {
            $modelTable->removeField($fieldName);
        }

        try {
            $modelBuilder = new ModelBuilder();
            $modelBuilder->registerModel($this->model);
            $this->addInformation(self::TRANSLATION_DELETE_FIELDS_SUCCESS);
        } catch (Exception $exception) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
            $this->addError(self::TRANSLATION_DELETE_FIELDS_ERROR, array('error' => $exception->getMessage()));
        }

        $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_DETAIL . '/' . $this->model->getName());
    }

    /**
     * Action to order the fields of a model
     * @param string $modelName Name of the model
     * @return null
     */
    public function orderFieldsAction($modelName) {
        $basePath = $this->request->getBasePath();

        $orderForm = new ModelFieldOrderForm($basePath . '/' . self::ACTION_ORDER_FIELDS . '/' . $modelName);
        if (!$orderForm->isSubmitted()) {
            $this->request->setRedirect($basePath);
            return;
        }

        $model = ModelManager::getInstance()->getModel($modelName);
        $modelTable = $model->getMeta()->getModelTable();

        $fieldOrder = $orderForm->getOrder();

        try {
            $modelTable->orderFields($fieldOrder);

            $modelBuilder = new ModelBuilder();
            $modelBuilder->registerModel($model);

            $this->addInformation(self::TRANSLATION_ORDER_FIELDS_SUCCESS);
        } catch (Exception $exception) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
            $this->addError(self::TRANSLATION_ORDER_FIELDS_ERROR, array('error' => $exception->getMessage()));
        }

        $this->response->setRedirect($basePath . '/' . self::ACTION_DETAIL . '/' . $modelName);
    }

    /**
     * Action to delete models from the system
     * @param string|zibo\library\orm\modelModel|array $models
     * @return null
     */
    public function deleteAction($models = null) {
        if (!$models) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        if (!is_array($models)) {
            $models = array($models);
        }

        $modelBuilder = new ModelBuilder();
        foreach ($models as $model) {
            if ($model instanceof Model) {
                $model = $model->getName();
            }

            try {
                $status = $modelBuilder->unregisterModel($model, true);

                if ($status) {
                    $message = self::TRANSLATION_DELETE_SUCCESS;
                } else {
                    $message = self::TRANSLATION_DELETE_SUCCESS_MODULE;
                }

                $this->addInformation($message, array('model' => $model));
            } catch (Exception $exception) {
                $this->addError(self::TRANSLATION_DELETE_ERROR, array('model' => $model, 'error' => $exception->getMessage()));
            }
        }

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Action to import the definition of models
     * @return null
     */
    public function importAction() {
        $basePath = $this->request->getBasePath();

        $importForm = new ModelImportForm($basePath . '/' . self::ACTION_IMPORT);
        if (!$importForm->isSubmitted()) {
            $this->response->setRedirect($basePath);
            return;
        }

        $file = null;
        try {
            $importForm->validate();

            $file = $importForm->getFile();

            $modelBuilder = new ModelBuilder();
            $modelBuilder->importModels($file, true);

            $this->addInformation(self::TRANSLATION_IMPORT_SUCCESS);

            $this->response->setRedirect($basePath);
            return;
        } catch (ValidationException $exception) {

        } catch (Exception $exception) {
            $this->addError(self::TRANSLATION_IMPORT_ERROR, array('error' => $exception->getMessage()));
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
        }

        $importForm->clearFile();
        if ($file && $file->exists()) {
            $file->delete();
        }

        $filterForm = new ModelFilterForm($basePath);
        $table = $this->getModelTable($basePath, $filterForm);

        $this->setBuilderView($table, $filterForm, $importForm);
    }

    /**
     * Action to export the definition of the provided models
     * @param string|zibo\library\orm\modelModel|array $models
     * @return null
     */
    public function exportAction($models = null, $download = false) {
        if (!$models) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $exportFile = new File(self::FILE_EXPORT);

        if (!is_array($models)) {
            $models = array($models);
        }

        $modelNames = array();
        foreach ($models as $model) {
            if ($model instanceof Model) {
                $model = $model->getName();
            }

            $modelNames[] = $model;
        }

        $modelBuilder = new ModelBuilder();
        $modelBuilder->exportModels($exportFile, $modelNames);

        if ($download) {
            $this->setDownloadView($exportFile);
        } else {
            $this->downloadFile = $exportFile;
        }
    }

    /**
     * Action to create a diagram of the models
     * @return null
     */
    public function erdAction() {
        if (!class_exists(self::CLASS_ERD) || func_num_args()) {
            $this->setError404();
            return;
        }

        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');

        $models = ModelManager::getInstance()->getModels(true);

        $objectFactory = new ObjectFactory();
        $layout = $objectFactory->createFromConfig(self::CONFIG_ERD_LAYOUT, self::DEFAULT_ERD_LAYOUT, self::INTERFACE_ERD_LAYOUT);

        $erd = new Erd();
        $erd->setLayout($layout);

        $file = $erd->getFile($models);

        $this->setDownloadView($file);
    }

    /**
     * Action to define the tables in the database
     * @return null
     */
    public function defineAction() {
        if (SecurityManager::getInstance()->isPermissionAllowed(Module::PERMISSION_MODELS)) {
            ModelManager::getInstance()->defineModels();
        }

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Action to scaffold a model
     * @param string $modelName Name of the model to scaffold
     * @return null
     */
    public function scaffoldAction($modelName = null) {
        if (!SecurityManager::getInstance()->isPermissionAllowed(Module::PERMISSION_MODELS)) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        if (!$modelName) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $controllerClass = ScaffoldController::generateScaffoldController($modelName);

        return $this->forward($controllerClass, null, 2);
    }

    /**
     * Sets the main view of the builder to the response
     * @param zibo\orm\builder\table\ModelTable $table
     * @return null
     */
    private function setBuilderView(ModelTable $table, ModelFilterForm $filterForm, ModelImportForm $importForm) {
        if ($this->downloadFile) {
            $this->setDownloadView($this->downloadFile);
            return;
        }

        $view = new BuilderView($table);
        $view->setPageTitle(self::TRANSLATION_TITLE, true);

        $sidebar = $view->getSidebar();
        $sidebar->addPanel(new ModelFilterView($filterForm));

        $basePath = $this->request->getBasePath() . '/';

        if (!$this->isReadOnly) {
            $addAction = $this->request->getBaseUrl() . '/' . Module::ROUTE_WIZARD . '/' . WizardController::ACTION_RESET;
            $defineAction = $basePath . self::ACTION_DEFINE;
            $scaffoldAction = $basePath . self::ACTION_SCAFFOLD . '/';

            $table->addDecorator(new ModelActionDecorator($scaffoldAction, self::TRANSLATION_SCAFFOLD));

            $sidebar->addPanel(new ModelImportView($importForm));
            $sidebar->addAction($addAction, self::TRANSLATION_ADD, true);
            $sidebar->addAction($defineAction, self::TRANSLATION_DEFINE, true);
        }

        if (class_exists(self::CLASS_ERD)) {
            $sidebar->addAction($basePath . self::ACTION_ERD, self::TRANSLATION_ERD, true);
        }

        $databaseExportAction = $this->getDatabaseExportAction();
        if (SecurityManager::getInstance()->isPermissionAllowed(Module::PERMISSION_EXPORT) && $databaseExportAction) {
            $sidebar->addAction($databaseExportAction, self::TRANSLATION_EXPORT_DATABASE, true);
        }

        $sidebar->setInformation(self::TRANSLATION_INFORMATION_BUILDER, true);

        $this->response->setView($view);
    }

    /**
     * Gets the model detail vieiw
     * @param zibo\library\orm\model\Model $model
     * @param zibo\orm\builder\table\ModelFieldTable $fieldTable
     * @param zibo\orm\builder\form\ModelFieldOrderForm $orderForm
     * @return zibo\orm\builder\view\ModelDetailView
     */
    public function getModelDetailView(Model $model, ModelFieldTable $fieldTable, ModelFieldOrderForm $orderForm = null, $editAction = null) {
        $basePath = $this->request->getBasePath();
        $modelName = $model->getName();

        if (!$this->isReadOnly && $editAction) {
            $editModelAction = $editAction . '/' . WizardController::ACTION_LIMIT . '/' . BuilderWizard::LIMIT_MODEL;
            $addFieldsAction = $editAction . '/' . WizardController::ACTION_FIELD;
            $editFormatAction = $editAction . '/' . WizardController::ACTION_LIMIT . '/' . BuilderWizard::LIMIT_DATA_FORMAT;
            $editIndexAction = $editAction . '/' . WizardController::ACTION_LIMIT . '/' . BuilderWizard::LIMIT_INDEX;
        } else {
            $editModelAction = null;
            $addFieldsAction = null;
            $editFormatAction = null;
            $editIndexAction = null;
        }

        $view = new ModelDetailView($model, $fieldTable, $orderForm, $editModelAction, $addFieldsAction, $editFormatAction, $editIndexAction);

        $sidebar = $view->getSidebar();

        if (!$this->isReadOnly) {
            $editAction = $this->request->getBaseUrl() . '/' . Module::ROUTE_WIZARD . '/' . WizardController::ACTION_MODEL . '/' . $modelName;
            $scaffoldAction = $basePath . '/' . self::ACTION_SCAFFOLD . '/' . $modelName;
            $exportAction = $basePath . '/' . self::ACTION_EXPORT . '/' . $modelName . '/1';

            $sidebar->addAction($editAction, self::TRANSLATION_EDIT, true);
            $sidebar->addAction($scaffoldAction, self::TRANSLATION_SCAFFOLD_MODEL, true);
            $sidebar->addAction($exportAction, self::TRANSLATION_EXPORT_MODEL, true);
        }

        $sidebar->addAction($basePath, self::TRANSLATION_BACK, true);

        $sidebar->setInformation(self::TRANSLATION_INFORMATION_MODEL, true);

        return $view;
    }

    /**
     * Gets the model table
     * @param string $tableAction
     * @param zibo\orm\builder\form\ModelFilterForm $filterForm
     * @return null
     */
    private function getModelTable($tableAction, ModelFilterForm $filterForm) {
        $models = $this->getModels(
            $filterForm->includeCustomModels(),
            $filterForm->includeModuleModels(),
            $filterForm->includeLocalizedModels(),
            $filterForm->includeLinkModels()
        );

        $table = new ModelTable($models, $tableAction, $this->request->getBasePath() . '/' . self::ACTION_DETAIL . '/');

        if (!$this->isReadOnly) {
            $translator = $this->getTranslator();

            $table->addAction(
                $translator->translate(self::TRANSLATION_DELETE),
                array($this, 'deleteAction'),
                $translator->translate(self::TRANSLATION_DELETE_CONFIRM)
            );

            $table->addAction(
                $translator->translate(self::TRANSLATION_EXPORT),
                array($this, 'exportAction')
            );
        }

        return $table;
    }

    /**
     * Gets the defined models in the system
     * @param boolean $includeCustomModels
     * @param boolean $includeModuleModels
     * @param boolean $includeLocalizedModels
     * @param boolean $includeLinkModels
     * @return array Array with Model objects
     */
    private function getModels($includeCustomModels, $includeModuleModels, $includeLocalizedModels, $includeLinkModels) {
        if (!$includeCustomModels && !$includeModuleModels && !$includeLocalizedModels && !$includeLinkModels) {
            return array();
        }

        $modelBuilder = new ModelBuilder();

        if (!$includeModuleModels && !$includeLocalizedModels && !$includeLinkModels) {
            return $modelBuilder->getBuilderModels();
        }

        if (!$includeCustomModels && !$includeLocalizedModels && !$includeLinkModels) {
            return $modelBuilder->getModuleModels();
        }

        $models = ModelManager::getInstance()->getModels(true);

        if ($includeCustomModels && $includeModuleModels && $includeLocalizedModels && $includeLinkModels) {
            return $models;
        }

        $builderModels = $modelBuilder->getBuilderModels();
        $moduleModels = $modelBuilder->getModuleModels();

        if (!$includeCustomModels) {
            foreach ($models as $modelName => $model) {
                if (array_key_exists($modelName, $builderModels) && (!$includeModuleModels || !array_key_exists($modelName, $moduleModels))) {
                    unset($models[$modelName]);
                }
            }
        }

        if (!$includeModuleModels) {
            foreach ($models as $modelName => $model) {
                if (array_key_exists($modelName, $moduleModels) && (!$includeCustomModels || !array_key_exists($modelName, $builderModels))) {
                    unset($models[$modelName]);
                }
            }
        }

        if (!$includeLocalizedModels) {
            foreach ($models as $modelName => $model) {
                if (preg_match('/^(.*)' . LocalizedModel::MODEL_SUFFIX . '$/', $modelName)) {
                    unset($models[$modelName]);
                }
            }
        }

        if (!$includeLinkModels) {
            foreach ($models as $modelName => $model) {
                if (!array_key_exists($modelName, $builderModels) && !array_key_exists($modelName, $moduleModels) && !preg_match('/^(.*)' . LocalizedModel::MODEL_SUFFIX . '$/', $modelName)) {
                    unset($models[$modelName]);
                }
            }

        }

        return $models;
    }

    /**
     * Gets the action to export the database export
     * @return string URL to the export of the database
     */
    private function getDatabaseExportAction() {
        if (!class_exists(self::CLASS_DATABASE_ADMIN)) {
            return false;
        }

        return $this->request->getBaseUrl() . '/' . DatabaseModule::ROUTE_ADMIN . '/' . DatabaseController::ACTION_EXPORT;
    }

}
