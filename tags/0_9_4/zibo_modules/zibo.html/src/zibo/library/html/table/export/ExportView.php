<?php

namespace zibo\library\html\table\export;

use zibo\core\View;

use zibo\library\html\table\Row;

/**
 * Interface to implement an export of a table
 */
interface ExportView extends View {

    /**
     * Set the title of the export
     * @param string $title title of the export
     * @return null
     */
    public function setExportTitle($title);

    /**
     * Add a header row to the export
     * @param zibo\library\html\table\Row $row The header row to set
     * @return null
     */
    public function addExportHeaderRow(Row $row);

    /**
     * Add a data row to the export
     * @param zibo\library\html\table\Row $row The data row to add
     * @param boolean $isGroupRow Flag to see if the provided row is a group row
     * @return null
     */
    public function addExportDataRow(Row $row, $isGroupRow);

}