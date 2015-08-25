<?php

namespace zibo\library\html\table;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\export\ExportExtensionManager;
use zibo\library\html\table\export\ExportView;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Simpe table extended with export functionality
 */
class ExportableTable extends SimpleTable {

    /**
     * Title of the export
     * @var string
     */
    private $exportTitle;

    /**
     * Url to get the export
     * @var string
     */
    private $exportUrl;

    /**
     * Array with the value and header decorators for the decorators
     * @var array
     */
    protected $exportColumnDecorators;

    /**
     * Array the group decorators for the export
     * @var array
     */
    protected $exportGroupDecorators;

    /**
     * Constructs a new exportable table
     * @param array $values Array with the values for the table, values are passed to the decorators to populate the rows of the table
     * @return null
     */
    public function __construct(array $values) {
        parent::__construct($values);

        $this->exportColumnDecorators = array();
        $this->exportGroupDecorators = array();
    }

    /**
     * Sets the title of the export
     * @param string $title
     * @return null
     * @throws zibo\ZiboException when the provided URL is invalid or empty
     */
    public function setExportTitle($title) {
        if ($title !== null && String::isEmpty($title)) {
            throw new ZiboException('Provided export title is empty or invalid');
        }

        $this->exportTitle = $title;
    }

    /**
     * Gets the title of the export
     * @return string
     */
    public function getExportTitle() {
        return $this->exportTitle;
    }

    /**
     * Sets the URL to the export
     * @param string $url
     * @return null
     * @throws zibo\ZiboException when the provided URL is empty or invalid
     */
    public function setExportUrl($url) {
        if (String::isEmpty($url)) {
            throw new ZiboException('Provided export URL is empty or invalid');
        }

        $this->exportUrl = $url;
    }

    /**
     * Gets the URL to the export
     * @return string
     */
    public function getExportUrl() {
        return $this->exportUrl;
    }

    /**
     * Adds the decorators for a export column. A column decorator gets a specific value from the table value and formats it for the column value.
     * @param zibo\library\html\table\decorator\Decorator $valueDecorator Decorator to decorate the values of the table into a column
     * @param zibo\library\html\table\decorator\Decorator $headerDecorator Decorator to decorate the header of the column
     * @return null
     */
    public function addExportDecorator(Decorator $valueDecorator, Decorator $headerDecorator) {
        $this->exportColumnDecorators[] = new ColumnDecorator($valueDecorator, $headerDecorator);
    }

    /**
     * Adds the group decorator to the table. Group decorators should return a boolean to set whether to add the group row or not
     * @param zibo\library\html\table\decorator\Decorator $groupDecorator Decorator to use for group rows
     * @return null
     */
    public function addExportGroupDecorator(Decorator $groupDecorator) {
        $this->exportGroupDecorators[] = $groupDecorator;
    }

    /**
     * Gets the view of the export
     * @param string $extension extension to get the export from
     * @return zibo\libray\html\table\export\ExportView view for the export
     */
    public function getExportView($extension) {
        if (empty($this->exportColumnDecorators)) {
            throw new ZiboException('No decorators set for the export view. Add a export decorator before trying to get the export view.');
        }

        $exportView = ExportExtensionManager::getInstance()->getExportView($extension);

        if ($this->exportTitle) {
            $exportView->setExportTitle($this->exportTitle);
        }

        $this->addHeaderToExport($exportView);
        $this->addRowsToExport($exportView);

        return $exportView;
    }

    /**
     * Adds the header row to the export of the table based on the header decorators
     * @return null
     */
    private function addHeaderToExport(ExportView $exportView) {
        $row = new Row();

        foreach ($this->exportColumnDecorators as $columnDecorator) {
            $cell = new HeaderCell();

            $headerDecorator = $columnDecorator->getHeaderDecorator();
            if ($headerDecorator) {
                $headerDecorator->decorate($cell, $row, 0, array());
            }

            $row->addCell($cell);
        }

        $exportView->addExportHeaderRow($row);
    }

    /**
     * Populates the rows of the export based on the provided values and the added decorators
     * @param zibo\library\html\table\export\ExportView $exportView View of the export
     * @return null
     */
    private function addRowsToExport(ExportView $exportView) {
        if (empty($this->values)) {
            return;
        }

        $rowNumber = 1;
        while ($value = array_shift($this->values)) {
            $this->addGroupRowToExport($exportView, $value, $rowNumber);
            $this->addDataRowToExport($exportView, $value, $rowNumber);

            $rowNumber++;
        }
    }

    /**
     * Adds a group row to the export of the table if necessairy, group decorators should return a boolean to set whether to add the group row or not
     * @param zibo\library\html\table\export\ExportView $exportView View of the export
     * @param mixed $value Value of the current row
     * @param integer $rowNumber Number of the current row
     * @return null
     */
    private function addGroupRowToExport(ExportView $exportView, $value, $rowNumber) {
        if (!$this->exportGroupDecorators) {
            return;
        }

        $row = new Row();
        $addRow = false;

        $neededCells = max(count($this->exportColumnDecorators), count($this->exportGroupDecorators));

        $numCells = 0;
        foreach ($this->exportGroupDecorators as $groupDecorator) {
            $cell = new Cell();
            $cell->setValue($value);

            $result = $groupDecorator->decorate($cell, $row, $rowNumber, $this->values);
            if ($result) {
                $addRow = true;
            }

            $row->addCell($cell);
            $numCells++;
        }

        for ($i = $numCells; $i < $neededCells; $i++) {
            $row->addCell(new Cell());
        }

        if ($addRow) {
            $exportView->addExportDataRow($row, true);
        }
    }

    /**
     * Adds a data row to the export of the table
     * @param zibo\library\html\table\export\ExportView $exportView View of the export
     * @param mixed $value Value to decorate and add as table row
     * @param integer $rowNumber Number of the current row
     * @return null
     */
    private function addDataRowToExport(ExportView $exportView, $value, $rowNumber) {
        $row = new Row();

        foreach ($this->exportColumnDecorators as $columnDecorator) {
            $cell = new Cell();
            $cell->setValue($value);

            $valueDecorator = $columnDecorator->getValueDecorator();
            $valueDecorator->decorate($cell, $row, $rowNumber, $this->values);

            $row->addCell($cell);
        }

        $exportView->addExportDataRow($row, false);
    }

}