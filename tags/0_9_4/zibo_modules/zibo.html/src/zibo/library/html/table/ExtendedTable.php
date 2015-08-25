<?php

namespace zibo\library\html\table;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\i18n\I18n;
use zibo\library\Callback;
use zibo\library\Number;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Extended table implementation with search, order and pagination functionality
 */
class ExtendedTable extends ExportableTable {

    /**
     * Name of the field for the row id's
     * @var string
     */
    const FIELD_ID = 'id';

    /**
     * Name of the action field
     * @var string
     */
    const FIELD_ACTION = 'action';

    /**
     * Name of the search query field
     * @var string
     */
    const FIELD_SEARCH_QUERY = 'searchQuery';

    /**
     * Name of the field for the number of item per page
     * @var string
     */
    const FIELD_PAGE_ROWS = 'pageRows';

    /**
     * Name of the order field
     * @var string
     */
    const FIELD_ORDER_METHOD = 'orderMethod';

    /**
     * Ascending order direction identifier
     * @var string
     */
    const ORDER_DIRECTION_ASC = 'ASC';

    /**
     * Descending order direction identifier
     * @var string
     */
    const ORDER_DIRECTION_DESC = 'DESC';

    /**
     * Translation key for the first action option
     * @var unknown_type
     */
    const TRANSLATION_ACTIONS = 'table.label.actions';

    /**
     * Total number of rows in this table (ignores the pagination)
     * @var integer
     */
    protected $countRows;

    /**
     * The form of this table
     * @var zibo\library\html\form\Form
     */
    protected $form;

    /**
     * Flag to set whether this form has been processed or not
     * @var boolean
     */
    protected $isProcessed;

    /**
     * Array with the label for the action as key and the callback to the action as value
     * @var array
     */
    protected $actions;

    /**
     * Array with the label of the action as key and the confirmation message for the action as value
     * @var array
     */
    protected $actionConfirmationMessages;

    /**
     * Array with the label of the order method as key and a OrderMethod object as value
     * @var unknown_type
     */
    protected $orderMethods;

    /**
     * Label of the current order method
     * @var string
     */
    protected $orderMethod;

    /**
     * Current order direction
     * @var string
     */
    protected $orderDirection;

    /**
     * Url for the order direction
     * @var string
     */
    protected $orderUrl;

    /**
     * Number of rows per page
     * @var integer
     */
    protected $pageRows;

    /**
     * Array with the different options for number of rows per page
     * @var array
     */
    protected $paginationOptions;

    /**
     * Url for the pagination
     * @var string
     */
    protected $paginationUrl;

    /**
     * Number of the current page
     * @var integer
     */
    protected $page;

    /**
     * Number of pages
     * @var integer
     */
    protected $pages;

    /**
     * Flag to seth whether the applySearch method is implemented
     * @var boolean
     */
    protected $hasSearch;

    /**
     * Search query submitted by the form
     * @var string
     */
    protected $searchQuery;

    /**
     * Constructs a new extended table
     * @param array $values Values for the table
     * @param string $formAction URL where the table form will point to
     * @param string $formName Name of the table form
     * @return null
     */
    public function __construct(array $values, $formAction, $formName = null) {
        parent::__construct($values);

        $this->form = new Form($formAction, $formName);
        $this->form->removeFromClass(Form::STYLE_FORM);
        $this->form->appendToClass(self::STYLE_TABLE);

        $this->isProcessed = false;

        $this->hasSearch = false;
        $this->searchQuery = null;

        $this->actions = array();
        $this->actionConfirmationMessages = array();

        $this->orderDirection = self::ORDER_DIRECTION_ASC;
        $this->orderMethod = null;
        $this->orderMethods = array();

        $this->page = 1;
        $this->pages = 1;
    }

    /**
     * Gets the form of this table
     * @return zibo\library\html\form\Form
     */
    public function getForm() {
        $this->processForm();

        return $this->form;
    }

    /**
     * Gets whether this table has rows
     * @return boolean True if the table has rows, false otherwise
     */
    public function hasRows() {
        $this->processForm();

        return parent::hasRows();
    }

    /**
     * Gets the number of rows set to this table
     * @return integer Number of rows
     */
    public function countRows() {
        $this->processForm();

        return $this->countRows;
    }

    /**
     * Gets the number of rows set to the current page of this table
     * @return integer Number of rows on the current page
     */
    public function countPageRows() {
        $this->processForm();

        return parent::countRows();
    }

    /**
     * Adds an action to this table
     * @param string $label Label for the action
     * @param string|array|zibo\library\Callback $callback Callback to the action
     * @param string $confirmationMessage Message for a confirmation dialog before performing the action
     * @return null
     * @throws zibo\ZiboException when the provided label or confirmation message is empty or invalid
     */
    public function addAction($label, $callback, $confirmationMessage = null) {
        if (String::isEmpty($label)) {
            throw new ZiboException('Provided label for the action is empty');
        }

        $this->actions[$label] = new Callback($callback);

        if ($confirmationMessage === null) {
            return;
        }

        if (String::isEmpty($confirmationMessage)) {
            throw new ZiboException('Provided confirmation message for the action is empty');
        }

        $this->actionConfirmationMessages[$label] = $confirmationMessage;
    }

    /**
     * Gets all the confirmation messages for the actions
     * @return array Array with the label of the action as key and the confirmation message as value
     */
    public function getActionConfirmationMessages() {
        return $this->actionConfirmationMessages;
    }

    /**
     * Gets whether this table has actions
     * @return boolean True when the table has action, false otherwise
     */
    public function hasActions() {
        return !empty($this->actions);
    }

    /**
     * Sets whether this table has the search field implemented
     * @param boolean $flag True if applySearch method is implemented, false otherwise
     * @return null
     */
    protected function setHasSearch($flag) {
        $this->hasSearch = $flag;
    }

    /**
     * Gets whether this table has the search field implemented
     * @return boolean True if the applySearch method is implemented; false otherwise
     */
    public function hasSearch() {
        return $this->hasSearch;
    }

    /**
     * Sets the search query for this table
     * @param string $query Search query
     * @return null
     * @throws zibo\ZiboException when the search is disabled on this table
     */
    public function setSearchQuery($query) {
        if (!$this->hasSearch()) {
            throw new ZiboException('Cannot set the search query: no search enabled on this table');
        }

        $this->searchQuery = $query;
    }

    /**
     * Gets the search query for this table
     * @return string
     */
    public function getSearchQuery() {
        return $this->searchQuery;
    }

    /**
     * Adds a new order method to the table. Provide extra arguments to pass argument for the order callbacks.
     * @param string $label Label for the order method
     * @param string|array|zibo\library\Callback $callbackAscending Callback to order ascending
     * @param string|array|zibo\library\Callback $callbackDescending Callback to order descending
     * @return null
     * @throws zibo\ZiboException when the provided label is empty or invalid
     */
    public function addOrderMethod($label, $callbackAscending, $callbackDescending) {
        if (String::isEmpty($label)) {
            throw new ZiboException('Provided label for the order method is empty');
        }

        $arguments = array_slice(func_get_args(), 3);

        $this->orderMethods[$label] = new OrderMethod($callbackAscending, $callbackDescending, $arguments);

        if (empty($this->orderMethod)) {
            $this->orderMethod = $label;
        }
    }

    /**
     * Gets whether this table has order methods
     * @return boolean True when order methods have been added, false otherwise
     */
    public function hasOrderMethods() {
        return !empty($this->orderMethods);
    }

    /**
     * Sets the current order method
     * @param string $label Label of the order method
     * @return null
     * @throws zibo\ZiboException when the provided label is not set as a order method
     */
    public function setOrderMethod($label) {
        if (!array_key_exists($label, $this->orderMethods)) {
            throw new ZiboException('Provided label is not a set order method');
        }

        $this->orderMethod = $label;
    }

    /**
     * Gets the label of the current order method
     * @return string
     */
    public function getOrderMethod() {
        return $this->orderMethod;
    }

    /**
     * Sets the current order direction
     * @param string $direction Order direction
     * @return null
     * @throws zibo\ZiboException when an invalid order direction has been provided
     */
    public function setOrderDirection($direction) {
        if ($direction != self::ORDER_DIRECTION_ASC && $direction != self::ORDER_DIRECTION_DESC) {
            throw new ZiboException('Provided order direction is not valid , try ORDER_DIRECTION_ASC or ORDER_DIRECTION_DESC');
        }

        $this->orderDirection = $direction;
    }

    /**
     * Gets the current order direction
     * @return string
     */
    public function getOrderDirection() {
        return $this->orderDirection;
    }

    /**
     * Sets the URL for the order direction
     * @param string $url
     * @return null
     */
    public function setOrderDirectionUrl($url) {
        $this->orderUrl = $url;
    }

    /**
     * Gets the URL for the order direction
     * @return string
     */
    public function getOrderDirectionUrl() {
        return $this->orderUrl;
    }

    /**
     * Sets the options for rows per page
     * @param array $options Array with different rows per page values
     * @return null
     * @throws zibo\ZiboException when an a negative or invalid option is provided
     */
    public function setPaginationOptions(array $options = null) {
        if (!$options) {
            $this->paginationOptions = null;
            return;
        }

        $paginationOptions = array();
        foreach ($options as $option) {
            if (Number::isNegative($option)) {
                throw new ZiboException('Pagination option cannot be negative');
            }

            $paginationOptions[$option] = $option;
        }

        $this->paginationOptions = $paginationOptions;
    }

    /**
     * Gets the options for rows per page
     * @return array Array with different rows per page values
     */
    public function getPaginationOptions() {
        return $this->paginationOptions;
    }

    /**
     * Gets whether the pagination options are set
     * @return boolean True if there are pagination options, false otherwise
     */
    public function hasPaginationOptions() {
        return $this->paginationOptions != null;
    }

    /**
     * Sets the URL for the pagination
     * @param string $url
     * @return null
     */
    public function setPaginationUrl($url) {
        $this->paginationUrl = $url;
    }

    /**
     * Gets the URL for the pagination
     * @return string
     */
    public function getPaginationUrl() {
        return $this->paginationUrl;
    }

    /**
     * Sets the number of rows per page
     * @param integer $rowsPerPage Number of rows per page
     * @return null
     * @throws zibo\ZiboException when the provided number of rows per page is invalid
     * @throws zibo\ZiboException when the provided number of rows per page is not available in the pagination options
     */
    public function setRowsPerPage($rowsPerPage = 10) {
        if ($rowsPerPage === null) {
            $this->pageRows = null;
            $this->page = 1;
            return;
        }

        if (Number::isNegative($rowsPerPage) || $rowsPerPage == 0) {
            throw new ZiboException('Provided number of rows per page is not a positive number');
        }

        if ($this->paginationOptions && !array_key_exists($rowsPerPage, $this->paginationOptions)) {
            throw new ZiboException('Provided number of rows per page is not available in the pagination options');
        }

        $this->pageRows = $rowsPerPage;
        $this->page = 1;
    }

    /**
     * Gets the number of rows per page
     * @return integer
     */
    public function getRowsPerPage() {
        return $this->pageRows;
    }

    /**
     * Sets the current page number
     * @param integer $page New page number
     * @return null
     */
    public function setPage($page) {
        if ($this->pageRows == null) {
            throw new ZiboException('No pagination set, use setRowsPerPage first');
        }

        if (Number::isNegative($page)) {
            throw new ZiboException('Provided page number is not a positive number');
        }

        $this->page = $page;
    }

    /**
     * Gets the current page number
     * @return integer
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * Gets the number of pages
     * @return integer
     */
    public function getPages() {
        $this->processForm();

        return $this->pages;
    }

    /**
     * Gets the HTML of this table. This will make sure the form has been processed before generating the HTML.
     * @return string
     */
    public function getHtml() {
        $this->processForm();

        return $this->getTableHtml();
    }

    /**
     * Gets the HTML of this table. Makes sure the action field is empty.
     * @return string
     */
    protected final function getTableHtml() {
        if ($this->hasActions()) {
            $fieldAction = $this->form->getField(self::FIELD_ACTION);
            $fieldAction->setValue(null);
        }

        return parent::getHtml();
    }

    /**
     * Gets the view of the export
     * @param string extension extension to get the export from
     * @return zibo\libray\html\table\export\ExportView view for the export
     * @throws zibo\ZiboException when processForm is already called, the export doesn't want pagination
     */
    public function getExportView($extension) {
        if ($this->isProcessed) {
            throw new ZiboException('Cannot export this table, processForm is already called.');
        }

        $this->processExport();

        return parent::getExportView($extension);
    }

    /**
     * Processes the search and order for the export
     * @return null
     */
    protected function processExport() {
        $this->prepareForm();

        if ($this->form->isSubmitted()) {
            $this->processSearch();
            $this->processOrder();
        }

        $this->applySearch();
        $this->applyOrder();
    }

    /**
     * Processes and applies the actions, search, order and pagination of this table
     * @return null
     */
    public function processForm() {
        if ($this->isProcessed) {
            return false;
        }

        $this->prepareForm();

        if ($this->form->isSubmitted()) {
            $this->processAction();
            $this->processSearch();
            $this->processOrder();
            $this->processPagination();
        }

        $this->applySearch();
        $this->applyOrder();
        $this->applyPagination();

        return $this->isProcessed = true;
    }

    /**
     * Applies the search query to the values in this table
     * @return null
     */
    protected function applySearch() {

    }

    /**
     * Applies the order method to the values in this table
     * @return boolean True when the values have been ordered, false otherwise
     */
    protected function applyOrder() {
        if (!array_key_exists($this->orderMethod, $this->orderMethods)) {
            return false;
        }

        if ($this->orderDirection === self::ORDER_DIRECTION_ASC) {
            $this->values = $this->orderMethods[$this->orderMethod]->invokeAscending($this->values);
        } else {
            $this->values = $this->orderMethods[$this->orderMethod]->invokeDescending($this->values);
        }

        return true;
    }

    /**
     * Applies the pagination to the values in this table
     * @return null
     */
    protected function applyPagination() {
        $this->countRows = count($this->values);

        if (!$this->pageRows) {
            return;
        }

        $this->pages = ceil($this->countRows / $this->pageRows);
        if ($this->page > $this->pages || $this->page < 1) {
            $this->page = 1;
        }

        $offset = ($this->page - 1) * $this->pageRows;

        $this->values = array_slice($this->values, $offset, $this->pageRows, true);
    }

    /**
     * Processes and invokes the action if provided and submitted
     * @return null
     */
    private function processAction() {
        if (!$this->hasActions()) {
            return;
        }

        $action = $this->form->getValue(self::FIELD_ACTION);

        if (!array_key_exists($action, $this->actions)) {
            return;
        }

        $values = $this->form->getValue(self::FIELD_ID);

        $this->actions[$action]->invoke($values);

        $this->form->setValue(self::FIELD_ACTION, 0);
    }

    /**
     * Gets the search query from the form and sets it to this table
     * @return null
     */
    private function processSearch() {
        if (!$this->hasSearch() || !$this->form->hasField(self::FIELD_SEARCH_QUERY)) {
            return;
        }

        $searchQuery = trim($this->form->getValue(self::FIELD_SEARCH_QUERY));

        if ($searchQuery != $this->searchQuery && $this->pageRows) {
            $this->setPage(1);
        }

        $this->searchQuery = $searchQuery;
    }

    /**
     * Gets the order from the form and sets it to this table
     * @return null
     */
    private function processOrder() {
        if (!$this->form->hasField(self::FIELD_ORDER_METHOD)) {
            return;
        }

        $order = $this->form->getValue(self::FIELD_ORDER_METHOD);

        $this->setOrderMethod($order);
    }

    /**
     * Gets the number of rows per page from the form and sets it to this table
     * @return null
     */
    private function processPagination() {
        if (!$this->form->hasField(self::FIELD_PAGE_ROWS)) {
            return;
        }

        $number = $this->form->getValue(self::FIELD_PAGE_ROWS);
        if ($number !== null) {
            $this->setRowsPerPage($number);
        }
    }

    /**
     * Adds the necessairy fields to the form of this table
     * @return null
     */
    protected function prepareForm() {
        $factory = FieldFactory::getInstance();

        if ($this->hasActions()) {
            $options = $this->getOptionsFromKeys($this->actions);

            $translator = I18n::getInstance()->getTranslator();
            array_unshift($options, $translator->translate(self::TRANSLATION_ACTIONS));

            $actionField = $this->createListField($factory, $options, self::FIELD_ACTION);

            $idField = $factory->createField(FieldFactory::TYPE_OPTION, self::FIELD_ID);
            $idField->setIsMultiple(true);
            $idField->setOptions($this->values);

            $this->form->addField($actionField);
            $this->form->addField($idField);
        }

        if ($this->hasSearch()) {
            $searchQueryField = $factory->createField(FieldFactory::TYPE_STRING, self::FIELD_SEARCH_QUERY, $this->searchQuery);

            $this->form->addField($searchQueryField);
        }

        if ($this->hasOrderMethods()) {
            $options = $this->getOptionsFromKeys($this->orderMethods);

            $orderField = $this->createListField($factory, $options, self::FIELD_ORDER_METHOD, $this->orderMethod);

            $this->form->addField($orderField);
        }

        if ($this->paginationOptions) {
            $pageItemsField = $this->createListField($factory, $this->paginationOptions, self::FIELD_PAGE_ROWS, $this->pageRows);

            $this->form->addField($pageItemsField);
        }
    }

    /**
     * Gets the keys of an array
     * @param array $list Array with options
     * @return array Array with the key of the provided list as key and value
     */
    private function getOptionsFromKeys(array $list) {
        $options = array();

        foreach ($list as $key => $value) {
            $options[$key] = $key;
        }

        return $options;
    }

    /**
     * Creates an form list field
     * @param zibo\library\html\form\field\FieldFactory $factory Factory for form fields
     * @param array $options Array with the options for the field
     * @param string $name Name for the field
     * @param mixed $value Value for the field
     * @return zibo\library\html\form\field\Field
     */
    private function createListField(FieldFactory $factory, array $options, $name, $value = null) {
        $field = $factory->createField(FieldFactory::TYPE_LIST, $name, $value);
        $field->setOptions($options);

        return $field;
    }

}