<?php

namespace zibo\orm\scaffold\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\controller\LocalizeController;
use zibo\admin\message\Message as AdminMessage;
use zibo\admin\Module;

use zibo\core\Zibo;

use zibo\library\html\form\Form;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\html\table\ExtendedTable;
use zibo\library\message\Message as CoreMessage;
use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\ModelManager;
use zibo\library\security\exception\UnauthorizedException;
use zibo\library\validation\exception\ValidationException;
use zibo\library\Number;

use zibo\orm\scaffold\form\ScaffoldForm;
use zibo\orm\scaffold\table\ScaffoldTable;
use zibo\orm\scaffold\view\ScaffoldFormView;
use zibo\orm\scaffold\view\ScaffoldIndexView;

use \Exception;

/**
 * Controller to scaffold a model
 */
class ScaffoldController extends AbstractController {

    /**
     * Suffix of the class name
     * @var string
     */
    const CLASS_NAME_SUFFIX = 'Controller';

    /**
     * Action to add data
     * @var string
     */
    const ACTION_ADD = 'add';

    /**
     * Action to delete data
     * @var string
     */
    const ACTION_DELETE = 'delete';

    /**
     * Action to edit data
     * @var string
     */
    const ACTION_EDIT = 'edit';

    /**
     * Action to save data
     * @var string
     */
    const ACTION_SAVE = 'save';

    /**
     * Argument name for the export action
     * @var string
     */
    const ARGUMENT_EXPORT = 'export';

    /**
     * Argument name of the order method
     * @var string
     */
    const ARGUMENT_ORDER_METHOD = 'order';

    /**
     * Argument name of the order direction
     * @var string
     */
    const ARGUMENT_ORDER_DIRECTION = 'direction';

    /**
     * Argument name of the page
     * @var string
     */
    const ARGUMENT_PAGE = 'page';

    /**
     * Argument name of the number of rows per page
     * @var string
     */
    const ARGUMENT_ROWS = 'rows';

    /**
     * Argument name of the search query
     * @var string
     */
    const ARGUMENT_SEARCH_QUERY = 'search';

    /**
     * Session key to hold the referer
     * @var string
     */
    const SESSION_REFERER = 'orm.scaffold.referer';

    /**
     * Translation key for the add new records button
     * @var string
     */
    const TRANSLATION_ADD = 'orm.button.add';

    /**
     * Translation key for the edit action
     * @var string
     */
    const TRANSLATION_EDIT = 'button.edit';

    /**
     * Translation key for the delete action
     * @var string
     */
    const TRANSLATION_DELETE = 'button.delete';

    /**
     * Translation key for the delete confirmation message
     * @var string
     */
    const TRANSLATION_DELETE_CONFIRM = 'table.label.delete.confirm';

    /**
     * Translation key for the delete confirmation message
     * @var string
     */
    const TRANSLATION_DELETE_SUCCESS = 'orm.message.data.delete';

    /**
     * Translation key for the save success message
     * @var string
     */
    const TRANSLATION_SAVE_SUCCESS = 'orm.message.data.save';

    /**
     * The model for scaffolding
     * @var zibo\library\orm\model\Model
     */
    protected $model;

    /**
     * Flag to see if the model is localized
     * @var boolean
     */
    protected $isLocalized;

    /**
     * Flag to see if the scaffolding is read only
     * @var boolean
     */
    protected $isReadOnly;

    /**
     * Flag to disable the delete function when the scaffolding is not read only
     * @var boolean
     */
    protected $isDeleteAllowed;

    /**
     * Boolean to enable or disable the search functionality, an array of field names to query is also allowed to enable the search
     * @var boolean|array
     */
    protected $search;

    /**
     * Boolean to enable or disable the order functionality, an array of field names to order is also allowed to enable the order
     * @var boolean|array
     */
    protected $order;

    /**
     * Variable to set the initial order method
     * @var string
     */
    protected $orderMethod;

    /**
     * Variable to set the initial order direction
     * @var string
     */
    protected $orderDirection;

    /**
     * Boolean to enable or disable the pagination functionality, an array of pagination options is also allowed to enable the pagination
     * @var boolean|array
     */
    protected $pagination;

    /**
     * Recursive depth used when retrieving data
     * @var integer|null
     */
    protected $recursiveDepth;

    /**
     * Translation key for the add title
     * @var string
     */
    protected $translationAdd;

    /**
     * Translation key for the general title
     * @var string
     */
    protected $translationTitle;

    /**
     * Constructs a new scaffold controller
     * @param string $modelName Name of the model to scaffold, if not provided the name will be retrieved from the class name
     * @param boolean $isReadOnly Set to true to make the scaffolding read only
     * @param boolean|array $search Boolean to enable or disable the search functionality, an array of field names to query is also allowed to enable the search
     * @param boolean|array $order Boolean to enable or disable the order functionality, an array of field names to order is also allowed to enable the order
     * @param boolean|array $pagination Boolean to enable or disable the pagination functionality, an array of pagination options is also allowed to enable the pagination
     * @return null
     */
    public function __construct($modelName = null, $isReadOnly = false, $search = true, $order = true, $pagination = true) {
        if ($modelName === null) {
            $modelName = $this->getModelNameFromClass();
        }

        if ($pagination === true) {
            $pagination = array(5, 10, 25, 50, 100, 250, 500);
        }

        $this->model = ModelManager::getInstance()->getModel($modelName);

        $this->recursiveDepth = 1;
        $this->isLocalized = $this->model->getMeta()->isLocalized();

        $this->pagination = $pagination;
        $this->search = $search;
        $this->order = $order;
        $this->orderMethod = null;
        $this->orderDirection = null;

        $this->isReadOnly = $isReadOnly;
        $this->isDeleteAllowed = true;
    }

    /**
     * Gets the model name from the class name
     * @return string Name of the model
     */
    private function getModelNameFromClass() {
        $modelName = get_class($this);
        if (!preg_match('/' . self::CLASS_NAME_SUFFIX . '$/', $modelName)) {
            return $modelName;
        }

        $tokens = explode('\\', $modelName);

        $modelName = array_pop($tokens);
        $modelName = substr($modelName, 0, strlen(self::CLASS_NAME_SUFFIX) * -1);

        return $modelName;
    }

    /**
     * Processes and sets a data table view to the response
     * @return null
     */
    public function indexAction() {
        $arguments = $this->parseArguments(func_get_args());
        $basePath = $this->request->getBasePath();

        $page = 1;
        $rowsPerPage = 10;
        $orderMethod = $this->orderMethod;
        $orderDirection = $this->orderDirection;
        $searchQuery = null;

        $this->getTableArguments($arguments, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery);

        $table = $this->getTable($basePath);
        $this->initializeTable($table, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery);

        if (!$arguments && ($this->pagination || $this->search || $this->order)) {
            $page = $table->getPage();
            $rowsPerPage = $table->getRowsPerPage();
            $orderMethod = $table->getOrderMethod();
            $orderDirection = $table->getOrderDirection();

            $url = $this->getTableUrl($basePath, $table, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery);

            $this->response->setRedirect($url);
            return;
        }

        if (array_key_exists(self::ARGUMENT_EXPORT, $arguments)) {
            $this->performExport($table, $arguments[self::ARGUMENT_EXPORT]);
            return;
        }

        $this->processIndex($table);

        $isTableChanged = $this->isTableChanged($table, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery);

        $url = $this->getTableUrl($basePath, $table, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery);

        if ($isTableChanged || (($this->pagination || $this->order) && func_num_args() == 0)) {
            $this->response->setRedirect($url);
        }

        if ($this->response->willRedirect() || $this->response->getView()) {
            return;
        }

        $table->getForm()->setAction($url);
        $table->setExportUrl($url . '/' . self::ARGUMENT_EXPORT . '/%extension%');
        if ($this->pagination) {
            $table->setPaginationUrl(str_replace(self::ARGUMENT_PAGE . '/' . $page, self::ARGUMENT_PAGE . '/%page%', $url));
        }
        if ($this->order) {
            $table->setOrderDirectionUrl(str_replace(self::ARGUMENT_ORDER_DIRECTION. '/' . strtolower($orderDirection), self::ARGUMENT_ORDER_DIRECTION . '/%direction%', $url));
        }

        $view = $this->getIndexView($table);

        $this->response->setView($view);
    }

    /**
     * Performs an export of the provided table and sets the view of the export to the response
     * @param zibo\library\html\table\ExtendedTable $table Table to get the export of
     * @param string $extension The extension for the export
     * @return null
     */
    protected function performExport(ExtendedTable $table, $extension) {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '500');
        ini_set('max_input_time', '500');

        $title = $this->getViewTitle();

        $table->setExportTitle($title);

        $view = $table->getExportView($extension);

        $this->response->setView($view);
    }

    /**
     * Processes the index action
     * @param zibo\library\html\table\ExtendedTable $table Table of the index view
     * @return null
     */
    protected function processIndex(ExtendedTable $table) {
        $table->processForm();
    }

    /**
     * Gets the URL for the table
     * @param string $basePath
     * @param zibo\library\html\table\ExtendedTable $table
     * @param int $page The current page
     * @param int $rowsPerPage Number or rows to display on each page
     * @param string $orderMethod Name of the order method to use
     * @param string $orderDirection Name of the order direction
     * @param string $searchQuery Value for the search query
     * @return null
     */
    protected function getTableUrl($basePath, ExtendedTable $table, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery) {
        $url = $basePath;

        if ($this->pagination) {
            $url .= '/' . self::ARGUMENT_PAGE . '/' . $page . '/' . self::ARGUMENT_ROWS . '/' . $rowsPerPage;
        }
        if ($table->hasOrderMethods() && ($orderMethod || $orderDirection)) {
            $url .= '/' . self::ARGUMENT_ORDER_METHOD . '/' . urlencode($orderMethod) . '/' . self::ARGUMENT_ORDER_DIRECTION . '/' . strtolower($orderDirection);
        }
        if ($table->hasSearch() && $searchQuery) {
            $url .= '/' . self::ARGUMENT_SEARCH_QUERY . '/' . urlencode($searchQuery);
        }

        return $url;
    }

    /**
     * Gets the table arguments from the argument array
     * @param array $arguments Arguments array with the name as key and the argument as value
     * @param int $page The current page
     * @param int $rowsPerPage Number or rows to display on each page
     * @param string $orderMethod Name of the order method to use
     * @param string $orderDirection Name of the order direction
     * @param string $searchQuery Value for the search query
     * @return null
     */
    protected function getTableArguments($arguments, &$page, &$rowsPerPage, &$orderMethod, &$orderDirection, &$searchQuery) {
        if (array_key_exists(self::ARGUMENT_PAGE, $arguments)) {
            $page = $arguments[self::ARGUMENT_PAGE];
        }

        if (array_key_exists(self::ARGUMENT_ROWS, $arguments)) {
            $rowsPerPage = $arguments[self::ARGUMENT_ROWS];
        }

        if (array_key_exists(self::ARGUMENT_ORDER_METHOD, $arguments)) {
            $orderMethod = urldecode($arguments[self::ARGUMENT_ORDER_METHOD]);
        }

        if (array_key_exists(self::ARGUMENT_ORDER_DIRECTION, $arguments)) {
            $orderDirection = strtoupper($arguments[self::ARGUMENT_ORDER_DIRECTION]);
        }

        if (array_key_exists(self::ARGUMENT_SEARCH_QUERY, $arguments)) {
            $searchQuery = urldecode($arguments[self::ARGUMENT_SEARCH_QUERY]);
        }
    }

    /**
     * Checks if the table arguments have changed
     * @param zibo\library\html\table\ExtendedTable $table
     * @param int $page The current page
     * @param int $rowsPerPage Number or rows to display on each page
     * @param string $orderMethod Name of the order method to use
     * @param string $orderDirection Name of the order direction
     * @param string $searchQuery Value for the search query
     * @return boolean
     */
    protected function isTableChanged(ExtendedTable $table, &$page, &$rowsPerPage, &$orderMethod, &$orderDirection, &$searchQuery) {
        $isTableChanged = false;

        if ($table->getPaginationOptions()) {
            if ($table->getPage() != $page) {
                $isTableChanged = true;
                $page = $table->getPage();
            }

            if ($table->getRowsPerPage() != $rowsPerPage) {
                $isTableChanged = true;
                $rowsPerPage = $table->getRowsPerPage();
                $page = 1;
            }
        }

        if ($table->hasOrderMethods()) {
            if ($table->getOrdermethod() != $orderMethod) {
                $isTableChanged = true;
                $orderMethod = $table->getOrderMethod();
            }

            if ($table->getOrderDirection() != $orderDirection) {
                $isTableChanged = true;
                $orderDirection = $table->getOrderDirection();
            }
        }

        if ($table->hasSearch() && $table->getSearchQuery() != $searchQuery) {
            $isTableChanged = true;
            $searchQuery = $table->getSearchQuery();
            $page = 1;
        }

        return $isTableChanged;
    }

    /**
     * Initialize the pagination, search and order of the table
     * @param zibo\library\html\table\ExtendedTable $table
     * @param int $page The current page
     * @param int $rowsPerPage Number or rows to display on each page
     * @param string $orderMethod Name of the order method to use
     * @param string $orderDirection Name of the order direction
     * @param string $searchQuery Value for the search query
     * @return null
     */
    protected function initializeTable(ExtendedTable $table, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery) {
        if (!$this->isReadOnly && $this->isDeleteAllowed) {
            $translator = $this->getTranslator();

            $table->addAction(
                $translator->translate(self::TRANSLATION_DELETE),
                array($this, 'deleteAction'),
                $translator->translate(self::TRANSLATION_DELETE_CONFIRM)
            );
        }

        if ($this->pagination) {
            $table->setPaginationOptions($this->pagination);
            $table->setRowsPerPage($rowsPerPage);
            $table->setPage($page);
        }

        if ($table->hasOrderMethods()) {
            if ($orderMethod) {
                $table->setOrderMethod($orderMethod);
            }
            if ($orderDirection) {
                $table->setOrderDirection($orderDirection);
            }
        }

        if ($table->hasSearch()) {
            $table->setSearchQuery($searchQuery);
        }
    }

    /**
     * Action to set an empty form to the view
     * @return null
     */
    public function addAction() {
        if ($this->isReadOnly) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $this->setScaffoldReferer($this->request->getBasePath() . '/' . self::ACTION_ADD);

        $form = $this->getForm();

        $view = $this->getFormView($form);

        $this->response->setView($view);
    }

    /**
     * Action to set a form with a data object to the view
     * @param integer $id Primary key of the data object
     * @param string $locale Locale code of the data
     * @return null
     */
    public function editAction($id, $locale = null) {
        if ($this->isLocalized && $locale !== null) {
            LocalizeController::setLocale($locale);
            $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/' . $id);
            return;
        }

        $data = $this->getData($id);
        if ($data == null) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $this->setScaffoldReferer($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/' . $id);

        $form = $this->getForm($data);

        if ($this->isReadOnly) {
            $form->setIsDisabled(true, SubmitCancelForm::BUTTON_SUBMIT);
        }

        $view = $this->getFormView($form, $data);

        $this->response->setView($view);
    }

    /**
     * Action to get the data from the form and save it to the model.
     * @return null
     */
    public function saveAction() {
        $data = $this->model->createData(false);

        $form = $this->getForm($data);

        if (!$form->isSubmitted()) {
            $this->response->setRedirect($this->getScaffoldReferer());
            return;
        }

        if ($form->isCancelled()) {
            $this->response->setRedirect($this->getScaffoldReferer());
            return;
        }

        if ($this->isReadOnly) {
            throw new UnauthorizedException();
        }

        try {
            $form->validate();

            $data = $this->getFormData($form);

            if ($this->isLocalized) {
                $data->dataLocale = LocalizeController::getLocale();
            }

            $this->saveData($data);

            $meta = $this->model->getMeta();
            $data = $meta->formatData($data, DataFormatter::FORMAT_TITLE);

            $this->addInformation(self::TRANSLATION_SAVE_SUCCESS, array('data' => $data));

            $this->response->setRedirect($this->getScaffoldReferer());
            return;
        } catch (ValidationException $exception) {
            $form->setValidationException($exception);
        }

        $view = $this->getFormView($form, $data);

        $this->response->setView($view);
    }

    /**
     * Gets the data for the edit action
     * @param integer $id Primary key of the data to retrieve
     * @return mixed Data object for the provided id
     */
    protected function getData($id) {
        if (!$this->isLocalized) {
            return $this->model->findById($id, $this->recursiveDepth);
        }

        $locale = LocalizeController::getLocale();
        return $this->model->findById($id, $this->recursiveDepth, $locale, true);
    }

    /**
     * Gets the data object from the provided form
     * @param zibo\library\html\form\Form $form
     * @return mixed Data object
     */
    protected function getFormData(Form $form) {
        return $form->getData();
    }

    /**
     * Saves the data to the model
     * @param mixed $data
     * @return null
     */
    protected function saveData($data) {
        $this->model->save($data);
    }

    /**
     * Action to delete the data from the model
     * @param integer|array $data Primary key or an array of primary keys
     * @return null
     */
    public function deleteAction($data = null) {
        if ($this->isReadOnly || !$this->isDeleteAllowed) {
            throw new UnauthorizedException();
        }

        $referer = $this->getSession()->get(Module::SESSION_REFERER, $this->request->getBasePath());

        $this->response->setRedirect($referer);

        if ($data == null) {
            return;
        }

        try {
            $this->model->delete($data);

            $this->addInformation(self::TRANSLATION_DELETE_SUCCESS);
        } catch (Exception $exception) {
            if ($exception instanceof ValidationException) {
                $errors = $exception->getAllErrors();
                foreach ($errors as $fieldName => $fieldErrors) {
                    foreach ($fieldErrors as $fieldError) {
                        $this->addError($fieldError->getCode(), $fieldError->getParameters());
                    }
                }
            } else {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
                $this->response->addMessage(new CoreMessage($exception->getMessage(), AdminMessage::TYPE_ERROR));
            }
        }
    }

    /**
     * Gets the index view for the scaffold
     * @param zibo\library\html\table\ExtendedTable $table Table with the model data
     * @param array $actions Array with the URL of the action as key and the label for the action as value
     * @return zibo\core\View
     */
    protected function getIndexView(ExtendedTable $table, array $actions = null) {
        $translator = $this->getTranslator();

        $meta = $this->model->getMeta();
        $title = $this->getViewTitle();

        $viewActions = array();

        if (!$this->isReadOnly) {
            $translationAdd = $this->translationAdd;
            if (!$translationAdd) {
                $translationAdd = self::TRANSLATION_ADD;
            }

            $addAction = $this->request->getBasePath() . '/' . self::ACTION_ADD;
            $viewActions[$addAction] = $translator->translate($translationAdd);
        }

        if ($actions) {
            $viewActions += $actions;
        }

        return $this->constructIndexView($meta, $table, $title, $viewActions);
    }

    /**
     * Creates the actual form view
     * @param zibo\library\orm\model\meta\ModelMeta $meta
     * @param zibo\library\html\table\ExtendedTable $table
     * @param string $title
     * @param array $viewActions
     * @return zibo\core\View
     */
    protected function constructIndexView(ModelMeta $meta, ExtendedTable $table, $title, $viewActions) {
        return new ScaffoldIndexView($meta, $table, $title, $viewActions);
    }

    /**
     * Gets the form view for the scaffold
     * @param zibo\library\html\form\Form $form Form of the data
     * @param mixed $data Data object
     * @return zibo\core\View
     */
    protected function getFormView(Form $form, $data = null) {
        $meta = $this->model->getMeta();
        $title = $this->getViewTitle($data);

        $localizeAction = null;
        if ($data && $data->id) {
            $localizeAction = $this->request->getBasePath() . '/' . self::ACTION_EDIT . '/' . $data->id;
        }

        return $this->constructFormView($meta, $form, $title, $data, $localizeAction);
    }

    /**
     * Creates the actual form view
     * @param zibo\library\orm\model\meta\ModelMeta $meta
     * @param zibo\library\html\form\Form $form
     * @param string $title
     * @param mixed $data
     * @param string $localizeAction
     * @return zibo\core\View
     */
    protected function constructFormView(ModelMeta $meta, Form $form, $title, $data = null, $localizeAction = null) {
        return new ScaffoldFormView($meta, $form, $title, $data, $localizeAction);
    }

    /**
     * Gets a title for the view
     * @param mixed $data The data which is being displayed, used only with the form view
     * @return string
     */
    protected function getViewTitle($data = null) {
        $meta = $this->model->getMeta();

        if ($this->translationTitle) {
            $title = $this->getTranslator()->translate($this->translationTitle);
        } else {
            $title = $meta->getName();
        }

        if ($data && $data->id) {
            $title .= ': ' . $meta->formatData($data);
        }

        return $title;
    }

    /**
     * Gets the form for the data of the model
     * @param mixed $data Data object to preset the form
     * @return zibo\library\html\form\Form
     */
    protected function getForm($data = null) {
        return new ScaffoldForm($this->request->getBasePath() . '/' . self::ACTION_SAVE, $this->model, $data);
    }

    /**
     * Gets a data table for the model
     * @param string $formAction URL where the table form will point to
     * @return zibo\library\html\table\ExtendedTable
     */
    protected function getTable($formAction) {
        return new ScaffoldTable($this->model, $formAction, $this->search, $this->order);
    }

    /**
     * Gets a model from the model manager
     * @param string $modelName
     * @return zibo\library\orm\model\Model
     */
    protected function getModel($modelName) {
        return ModelManager::getInstance()->getModel($modelName);
    }

    /**
     * Sets the referer to redirect to when performing a save action
     * @param string $url URL which will not be allowed as referer
     * @return string The set referer
     */
    protected function setScaffoldReferer($url = null, $skipWhenNoReferer = false) {
        $session = $this->getSession();

        $referer = $session->get(Module::SESSION_REFERER);
        if ($referer == null || $referer == $url) {
            $referer = null;
        }

        if (!$skipWhenNoReferer || ($referer && $skipWhenNoReferer)) {
            $session->set(self::SESSION_REFERER, $referer);
        }

        return $this->getScaffoldReferer();
    }

    /**
     * Gets the referer to redirect to when performing a save action, when not set the base path will be returned
     * @return string URL to redirect to
     */
    protected function getScaffoldReferer() {
        $session = $this->getSession();

        $referer = $session->get(self::SESSION_REFERER);

        if (!$referer) {
            $referer = $this->request->getBasePath();
        }

        return $referer;
    }

    /**
     * Generates a generic scaffold controller for the provided model
     * @param string $modelName Name of the model
     * @return string Class name of the generated controller
     */
    public static function generateScaffoldController($modelName) {
        $controllerName = $modelName . self::CLASS_NAME_SUFFIX;

        $index = 2;
        while (class_exists($controllerName)) {
            $controllerName = $modelName . self::CLASS_NAME_SUFFIX . $index;
            $index++;
        }

        eval('class ' . $controllerName . ' extends ' . __CLASS__ . ' { }');

        return $controllerName;
    }

}