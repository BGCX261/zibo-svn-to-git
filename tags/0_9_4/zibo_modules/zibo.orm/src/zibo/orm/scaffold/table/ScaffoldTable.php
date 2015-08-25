<?php

namespace zibo\orm\scaffold\table;

use zibo\admin\controller\LocalizeController;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\orm\definition\field\extended\VersionField;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\orm\definition\ModelTable as LibraryModelTable;
use zibo\library\orm\model\LocalizedModel;
use zibo\library\orm\model\Model;
use zibo\library\orm\query\ModelQuery;

use zibo\orm\scaffold\controller\ScaffoldController;
use zibo\orm\scaffold\table\decorator\DataDecorator;
use zibo\orm\scaffold\table\decorator\LocalizeDecorator;

use zibo\ZiboException;

/**
 * Table for a scaffolded model
 */
class ScaffoldTable extends ModelTable {

    /**
     * Meta of the model
     * @var zibo\library\orm\model\meta\ModelMeta
     */
    private $meta;

    /**
     * Base URL for this table
     * @var string
     */
    private $basePath;

    /**
     * Array with the field names to search in
     * @var array
     */
    private $searchFields;

    /**
     * Array with the statements for the order functionality
     * @var array
     */
    private $orderStatements;

    /**
     * Constructs a new scaffold table
     * @param zibo\library\orm\model\Model $model Model for the data of the table
     * @param string $basePath URL to the base path of the table, this is also the URL where the form of the table will point to
     * @param boolean|array $search False to disable search, True to search all properties or an array with the fields to query
     * @param boolean|array $order False to disable order, True to order all properties or an array with the fields to order
     * @return null
     */
    public function __construct(Model $model, $basePath, $search = true, $order = true) {
        $this->basePath = $basePath;

        parent::__construct($model, $this->basePath);

        $this->meta = $model->getMeta();

        if ($this->meta->isLocalized()) {
            $this->query->setLocale(LocalizeController::getLocale());
            $this->query->setWillIncludeUnlocalizedData(ModelQuery::INCLUDE_UNLOCALIZED_FETCH);
            $this->query->setWillAddIsLocalizedOrder(true);
        }

        if ($search) {
            if ($search === true) {
                $search = array();
            }
            $this->setSearchFields($search);
        }

        if ($order) {
            if ($order === true) {
                $order = array();
            }
            $this->setOrderFields($order);
        }
    }

    /**
     * Enables the search on this table and sets the provided fields as search query fields
     * @param array $fieldNames Array with the field names to search in. If none provided, all the properties of the model will be queried
     * @return null
     */
    protected function setSearchFields(array $fieldNames) {
        $this->setHasSearch(true);

        if ($fieldNames) {
            $this->searchFields = $fieldNames;
            return;
        }

        $this->searchFields = $this->getModelPropertyNames();
    }

    /**
     * Enables the order on this table and sets the provided property fields as order fields
     * @param array $fieldNames Array with the field names to search in. If none provided, all the properties of the model will be added
     * @return null
     */
    protected function setOrderFields(array $fieldNames) {
        $meta = $this->model->getMeta();

        if (!$fieldNames) {
            $fieldNames = $this->getModelPropertyNames();
        }

        $this->orderStatements = array();

        foreach ($fieldNames as $index => $fieldName) {
            $callback = array($this, 'addOrderToQuery');

            if (is_array($fieldName)) {
                if (!array_key_exists('ASC', $fieldName)) {
                    throw new ZiboException('Provided order method ' . $index . ' has no ASC statement');
                }
                if (!array_key_exists('DESC', $fieldName)) {
                    throw new ZiboException('Provided order method ' . $index . ' has no DESC statement');
                }

                $label = $index;
                $orderStatements = $fieldName;
            } else {
                $field = $meta->getField($fieldName);

                if ($field instanceof RelationField) {
                    continue;
                }

                $label = ucfirst($fieldName);
                $orderStatements = array(
                    'ASC' => '{' . $fieldName . '} ASC',
                    'DESC' => '{' . $fieldName . '} DESC',
                );
            }

            $this->orderStatements[$label] = $orderStatements;

            $this->addOrderMethod($label, $callback, $callback, $label);
        }
    }

    /**
     * Gets the names of the model fields usefull for the search or the order functionality
     * @return array Array with the field name as key and the field object as value
     */
    private function getModelPropertyNames() {
        $meta = $this->model->getMeta();

        $fields = $meta->getProperties();

        unset($fields[LibraryModelTable::PRIMARY_KEY]);
        if (isset($fields[VersionField::NAME])) {
            unset($fields[VersionField::NAME]);
        }

        return array_keys($fields);
    }

    /**
     * Gets the HTML of this table. Makes sure there is a decorator added to the table
     * @return string
     */
    public function getHtml() {
        if (!$this->columnDecorators && !$this->groupDecorators) {
            $editAction = $this->basePath . '/' . ScaffoldController::ACTION_EDIT . '/';

            $dataDecorator = new DataDecorator($this->meta, $editAction);
            $dataDecorator = new ZebraDecorator($dataDecorator);

            $this->addDecorator($dataDecorator, null, true);

            if ($this->meta->isLocalized()) {
                $localizeDecorator = new LocalizeDecorator($this->model, $editAction);
                $this->addDecorator($localizeDecorator);
            }
        }

        return parent::getHtml();
    }

    /**
     * Adds the condition for the search query to the model query of this table
     * @return null
     */
    protected function applySearch() {
        if (empty($this->searchQuery) || empty($this->searchFields)) {
            return;
        }

        $value = '%' . $this->searchQuery . '%';

        $condition = '';
        foreach ($this->searchFields as $field) {
            $condition .= ($condition == '' ? '' : ' OR ') . '{' . $field . '} LIKE %1%';
        }

        $this->query->addCondition($condition, $value);
    }

    /**
     * Adds the order by to the query of this table
     * @param array $values Values of the table
     * @param string $label Label of the order method
     * @return null
     */
    public function addOrderToQuery(array $values, $label) {
        if (!isset($this->orderStatements[$label])) {
            throw new ZiboException($label . ' not found in the order method list');
        }

        $direction = $this->getOrderDirection();

        $this->query->addOrderBy($this->orderStatements[$label][$direction]);
    }

}