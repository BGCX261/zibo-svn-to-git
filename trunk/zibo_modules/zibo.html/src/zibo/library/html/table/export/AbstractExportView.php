<?php

namespace zibo\library\html\table\export;

use zibo\library\html\table\Row;

/**
 * Abstract export view
 */
abstract class AbstractExportView implements ExportView {

    /**
     * Array with the header rows
     * @var array
     */
    protected $headers = array();

    /**
     * Array with the data rows
     * @var array
     */
    protected $rows = array();

    /**
     * Array with the indexes in $rows of the group rows
     * @var array
     */
    protected $groupRows = array();

    /**
     * Title of the export
     * @var string
     */
    protected $title;

    /**
     * Sets the title of the export
     * @param string $title Title for the export
     * @return null
     */
    public function setExportTitle($title) {
        $this->title = $title;
    }

    /**
     * Adds a header row to the export
     * @param zibo\library\html\table\Row $row Header row
     * @return null
     */
    public function addExportHeaderRow(Row $row) {
        $this->headers[] = $row;
    }

    /**
     * Adds a data row to the export
     * @param zibo\library\html\table\Row $row Data row
     * @param boolean $isGroupRow Flag to see if this row is a group row
     * @return null
     */
    public function addExportDataRow(Row $row, $isGroupRow) {
        $this->rows[] = $row;

        if ($isGroupRow) {
            $this->groupRows[] = count($this->rows) - 1;
        }
    }

}