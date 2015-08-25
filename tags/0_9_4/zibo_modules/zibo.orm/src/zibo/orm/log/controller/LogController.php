<?php

namespace zibo\orm\log\controller;

use zibo\library\html\table\decorator\DateDecorator;
use zibo\library\html\table\decorator\StaticDecorator;
use zibo\library\html\table\decorator\ValueDecorator;
use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;
use zibo\library\orm\model\LogModel;

use zibo\orm\log\form\LogFilterForm;
use zibo\orm\log\table\decorator\DataDecorator;
use zibo\orm\log\view\LogFilterView;
use zibo\orm\log\view\LogView;

use zibo\orm\scaffold\controller\ScaffoldController;
use zibo\orm\scaffold\table\ScaffoldTable;

/**
 * Controller to browse the history of the logged models
 */
class LogController extends ScaffoldController {

    /**
     * Translation for the date label
     * @var string
     */
    const TRANSLATION_DATE = 'orm.label.date';

    /**
     * Translation for the date added label
     * @var string
     */
    const TRANSLATION_DATE_ADDED = 'orm.label.date.added';

    /**
     * Translation for the user label
     * @var string
     */
    const TRANSLATION_USER = 'orm.label.user';

    /**
     * Translation for the action label
     * @var string
     */
    const TRANSLATION_ACTION = 'orm.label.action';

    /**
     * Translation for the action label
     * @var string
     */
    const TRANSLATION_DATA = 'orm.label.data';

    /**
     * Translation for the version label
     * @var string
     */
    const TRANSLATION_VERSION = 'orm.label.version';

    /**
     * Translation key for the title
     * @var string
     */
    const TRANSLATION_TITLE = 'orm.title.log';

    /**
     * Session key for the action include value
     * @var string
     */
    const SESSION_FILTER_INCLUDE = 'orm.log.filter.include';

    /**
     * Session key for the data model value
     * @var string
     */
    const SESSION_FILTER_DATA_MODEL = 'orm.log.filter.data.model';

    /**
     * Session key for the data id value
     * @var string
     */
    const SESSION_FILTER_DATA_ID = 'orm.log.filter.data.id';

    /**
     * Session key for the data field value
     * @var string
     */
    const SESSION_FILTER_DATA_FIELD = 'orm.log.filter.data.field';

    /**
     * The filter form
     * @var zibo\orm\log\form\LogFilterForm
     */
    private $filterForm;

    /**
     * Construct a new log controller
     * @return null
     */
    public function __construct() {
        $translator = $this->getTranslator();

        $search = array('action', 'dataModel', 'user');

        $order = array(
            $translator->translate(self::TRANSLATION_DATE_ADDED) => array(
                'ASC' => '{dateAdded} ASC, {id} ASC',
                'DESC' => '{dateAdded} DESC, {id} DESC',
            ),
        );

        parent::__construct(LogModel::NAME, true, $search, $order, true);

        $this->orderDirection = 'DESC';
        $this->translationTitle = self::TRANSLATION_TITLE;
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
        parent::initializeTable($table, $page, $rowsPerPage, $orderMethod, $orderDirection, $searchQuery);

        $session = $this->getSession();

        $include = $session->get(self::SESSION_FILTER_INCLUDE);
        $dataModel = $session->get(self::SESSION_FILTER_DATA_MODEL);
        $dataId = $session->get(self::SESSION_FILTER_DATA_ID);
        $dataField = $session->get(self::SESSION_FILTER_DATA_FIELD);

        $this->filterForm = new LogFilterForm($this->request->getBasePath(), $include, $dataModel, $dataId, $dataField);

        $query = $table->getModelQuery();

        if ($include) {
            $conditions = array();
            foreach ($include as $key => $action) {
                $conditions[] = '{action} = %' . $key . '%';
            }

            $query->addConditionWithVariables(implode(' OR ', $conditions), $include);
        }
        if ($dataModel) {
            $query->addCondition('{dataModel} = %1%', $dataModel);
        }
        if ($dataId) {
            $query->addCondition('{dataId} = %1%', $dataId);
        }
        if ($dataField) {
            $query->addCondition('{changes.fieldName} = %1%', $dataField);
        }
    }

    protected function processIndex(ExtendedTable $table) {
        if ($this->filterForm && $this->filterForm->isSubmitted()) {
            $include = $this->filterForm->getInclude();
            $dataModel = $this->filterForm->getDataModel();
            $dataId = $this->filterForm->getDataId();
            $dataField = $this->filterForm->getDataField();

            $session = $this->getSession();
            $session->set(self::SESSION_FILTER_INCLUDE, $include);
            $session->set(self::SESSION_FILTER_DATA_MODEL, $dataModel);
            $session->set(self::SESSION_FILTER_DATA_ID, $dataId);
            $session->set(self::SESSION_FILTER_DATA_FIELD, $dataField);

            $this->response->setRedirect($this->request->getBasePath());
        }

        parent::processIndex($table);
    }

    /**
     * Gets the index view for the scaffold
     * @param zibo\library\html\table\ExtendedTable $table Table with the model data
     * @param array $actions Array with the URL of the action as key and the label for the action as value
     * @return zibo\core\View
     */
    protected function getIndexView(ExtendedTable $table, array $actions = null) {
        $view = parent::getIndexView($table, $actions);

        if ($this->filterForm) {
            $filterView = new LogFilterView($this->filterForm);
            $view->getSidebar()->addPanel($filterView);
        }

        return $view;
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

        $urlBack = $this->setScaffoldReferer($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/' . $id, true);

        $view = new LogView($data, $urlBack);
        $view->setPageTitle($this->translationTitle, true);

        $this->response->setView($view);
    }

    /**
     * Gets a data table for the model
     * @param string $formAction URL where the table form will point to
     * @return zibo\library\html\table\ExtendedTable
     */
    protected function getTable($formAction) {
        $table = new ScaffoldTable($this->model, $formAction, $this->search, $this->order);

        $table->addDecorator(new ZebraDecorator(new DateDecorator('dateAdded', 'j M Y H:i:s')), new StaticDecorator(self::TRANSLATION_DATE, true));
        $table->addDecorator(new ValueDecorator('user'), new StaticDecorator(self::TRANSLATION_USER, true));
        $table->addDecorator(new ValueDecorator('action'), new StaticDecorator(self::TRANSLATION_ACTION, true));
        $table->addDecorator(new DataDecorator($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/'), new StaticDecorator(self::TRANSLATION_DATA, true));
        $table->addDecorator(new ValueDecorator('dataVersion'), new StaticDecorator(self::TRANSLATION_VERSION, true));

        return $table;
    }

}