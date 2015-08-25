<?php

namespace zibo\admin\view\table;

use zibo\admin\view\BaseView;

use zibo\library\html\table\export\ExportView;
use zibo\library\html\table\Row;

use zibo\jquery\Module as JQueryModule;

/**
 * Table printer export view
 */
class PrinterExportView extends BaseView implements ExportView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/table/export.printer';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_EXPORT = 'web/styles/admin/table.export.css';

    /**
     * Array with the header rows
     * @var array
     */
    private $headers;

    /**
     * Array with the data rows
     * @var array
     */
    private $rows;

    /**
     * Title of the export
     * @var string
     */
    private $title;

    /**
     * Index for the odd/even rows
     * @var integer
     */
    private $zebraIndex;

    /**
     * Constructs a new export view
     * @return null
     */
    public function __construct() {
        parent::__construct(null, self::TEMPLATE);
        $this->addStyle(self::STYLE_EXPORT);
        $this->removeStyle(self::STYLE_BASE);

        $this->addInlineJavascript("window.print();");

        $this->headers = array();
        $this->rows = array();

        $this->zebraIndex = 0;
    }

    /**
     * Renders this view
     * @param boolean $return True to return the rendered view, false to send it to the output
     * @return null|string
     */
    public function render($return = true) {
        $this->set('title', $this->title);
        $this->set('headers', $this->headers);
        $this->set('rows', $this->rows);

        return parent::render($return);
    }

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
     * @return null
     */
    public function addExportDataRow(Row $row, $isGroupRow) {
        $cells = $row->getCells();
        foreach ($cells as $cell) {
            $value = $cell->getValue();
            $cell->setValue(nl2br($value));
        }

        if ($isGroupRow) {
            $row->appendToClass('group');
        } else {
            if ($this->zebraIndex) {
                $row->appendToClass('even');
                $this->zebraIndex = 0;
            } else {
                $row->appendToClass('odd');
                $this->zebraIndex = 1;
            }
        }

        $this->rows[] = $row;
    }

}