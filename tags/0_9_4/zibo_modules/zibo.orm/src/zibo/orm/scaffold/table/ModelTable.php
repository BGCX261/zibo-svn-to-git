<?php

namespace zibo\orm\scaffold\table;

use zibo\library\html\table\decorator\BooleanDecorator;
use zibo\library\html\table\decorator\DateDecorator;
use zibo\library\html\table\decorator\StaticDecorator;
use zibo\library\html\table\decorator\ValueDecorator;
use zibo\library\html\table\ExtendedTable;
use zibo\library\orm\model\LocalizedModel;
use zibo\library\orm\model\Model;
use zibo\library\orm\ModelManager;

use zibo\orm\scaffold\table\decorator\DataOptionDecorator;

/**
 * Base data model table
 */
class ModelTable extends ExtendedTable {

    /**
     * Name of the form for the table, will be suffixed with the model name
     * @var string
     */
    const FORM_NAME = 'formTable';

    /**
     * Model used for the data of this table
     * @var zibo\library\orm\model\Model
     */
    protected $model;

    /**
     * Model query used to populate the rows of this table
     * @var zibo\library\orm\query\ModelQuery
     */
    protected $query;

    /**
     * Constructs a new model table
     * @param zibo\library\orm\model\Model $model
     * @param string $formAction URL where the form will point to
     */
    public function __construct(Model $model, $formAction) {
        $this->model = $model;
        $this->query = $model->createQuery();

        parent::__construct(array(), $formAction, self::FORM_NAME . $this->model->getName());
    }

    /**
     * Gets the model query of this table
     * @return zibo\library\orm\query\ModelQuery
     */
    public function getModelQuery() {
        return $this->query;
    }

    /**
     * Gets the view of the export. This method will first add default export decorators if none were added.
     * @param string $extension extension to get the export from
     * @return zibo\libray\html\table\export\ExportView view for the export
     */
    public function getExportView($extension) {
        if ($this->exportColumnDecorators || $this->exportGroupDecorators) {
            return parent::getExportView($extension);
        }

        $meta = $this->model->getMeta();

        $properties = $meta->getProperties();
        foreach ($properties as $fieldName => $property) {
            $type = $property->getType();
            switch ($type) {
                case 'boolean':
                    $decorator = new BooleanDecorator($fieldName);
                    break;
                case 'date':
                case 'datetime':
                    $decorator = new DateDecorator($fieldName);
                    break;
                default:
                    $decorator = new ValueDecorator($fieldName);
                    break;
            }

            $this->addExportDecorator($decorator, new StaticDecorator(ucfirst($fieldName)));
        }

        return parent::getExportView($extension);
    }

    /**
     * Gets the HTML of this table. Makes sure there is a option decorator when there are actions attached to this table
     * @return string
     */
    public function getHtml() {
        if ($this->actions) {
            $dataOptionDecorator = new DataOptionDecorator();
            $this->addDecorator($dataOptionDecorator, null, true);
        }

        return parent::getHtml();
    }

    /**
     * Processes the table for export
     * @return null
     */
    protected function processExport() {
        parent::processExport();

        $this->values = $this->query->query();
    }

    /**
     * Processes the form of this table, will execute the model query and assign the result to the values
     * of this table
     * @return boolean If the form was already processed, nothing is done and false returned. Otherwise the form
     *                 will be processed and true returned
     */
    public function processForm() {
        if (!parent::processForm()) {
            return false;
        }

        if (!$this->pageRows || ($this->pageRows && $this->countRows)) {
            $this->values = $this->query->query();
        }

        return true;
    }

    /**
     * Applies the pagination to the model query of this table
     * @return null
     */
    protected function applyPagination() {
        if (!$this->pageRows) {
            return;
        }

        $this->countRows = $this->countTotalRows();

        $this->pages = ceil($this->countRows / $this->pageRows);

        if ($this->page > $this->pages) {
            $this->page = 1;
        }

        $offset = ($this->page - 1) * $this->pageRows;

        $this->query->setLimit($this->pageRows, $offset);
    }

    /**
     * Performs a count on the model query of this table
     * @return integer Number of rows
     */
    protected function countTotalRows() {
        return $this->query->count();
    }

}