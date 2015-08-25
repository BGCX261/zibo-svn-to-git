<?php

namespace zibo\library\excel;

use zibo\core\Zibo;

use zibo\library\excel\format\Format;

use zibo\ZiboException;

use \PHPExcel_Cell;
use \PHPExcel_Worksheet;

/**
 * A worksheet of an Excel workbook
 */
class Worksheet {

    /**
     * Worksheet object of the vendor library
     * @var PHPExcel_Worksheet
     */
    private $worksheet;

    /**
     * Construct a new worksheet
     * @param Workbook workbook workbook containing this worksheet
     * @param string name name of this worksheet
     */
    public function __construct(PHPExcel_Worksheet $worksheet) {
        $this->worksheet = $worksheet;
    }

    /**
     * Perform automatic calculation of the column widths
     * @return null
     */
    public function calculateColumnWidths() {
        $highestColumn = $this->worksheet->getHighestColumn(); //e.g., 'G'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //e.g., 6

        for ($column = 0; $column < $highestColumnIndex; $column++) {
            $this->worksheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($column))->setAutoSize(true);
        }
    }

    /**
     * Set the width of a column
     * @param int column column number, starts with 1
     * @param int width width of the column, should be greater then 0
     * @return null
     */
    public function setColumnWidth($column, $width) {
        if (!is_numeric($column) || $column < 0) {
            throw new ZiboException('Invalid column, column should be a positive numeric value');
        }
        if (!is_numeric($width) || $width < 0) {
            throw new ZiboException('Invalid width, width should be a positive numeric value');
        }
//        $this->worksheet->setColumn($column, $column, $width);
    }

    /**
     * Set the width of multiple columns at once.
     * @param array widths array with the column number as key and the width as value
     * @return null
     */
    public function setColumnWidths(array $widths) {
        foreach ($widths as $column => $width) {
            $this->setColumnWidth($column, $width);
        }
    }

    /**
     * Gets the number of rows
     * @return integer
     */
    public function getRowCount() {
        return $this->worksheet->getHighestRow();
    }

    /**
     * Gets the number of columns
     * @return integer
     */
    public function getColumnCount() {
        return $this->worksheet->getHighestColumn();
    }

    /**
     * Read the contents of a cell
     * @param int row row number of the cell to read, starting with 1
     * @param int column column number of the cell to read, starting with 1
     * @return mixed
     */
    public function read($row, $column) {
        $cell = $this->worksheet->getCellByColumnAndRow($column, $row);
        return $cell->getValue();
    }

    /**
     * Write the contents of a cell
     * @param int row row number starting with 1
     * @param int column column number starting with 1
     * @param string content contents of the cell
     * @param Format format format properties of this cell (optional)
     * @return null
     */
    public function write($row, $column, $content, Format $format = null) {
        if ($content === null) {
            $content = '';
        }

        if (!is_scalar($content)) {
            throw new ZiboException('Provided cell content is not scalar ' . $content);
        }

        $this->worksheet->setCellValueByColumnAndRow($column, $row, $content);

        if (!$format) {
            return;
        }

        $style = $this->getStyleArrayFromFormat($format);
        $this->worksheet->getStyleByColumnAndRow($column, $row)->applyFromArray($style);
    }

    /**
     * Get a format array, for the vendor library, from a format object
     * @param zibo\library\excel\format\Format $format
     * @return array
     */
    private function getStyleArrayFromFormat(Format $format) {
        $font = array(
            'name' => $format->getFont(),
            'bold' => $format->getTextWeight(),
            'color' => array(
                'rgb' => str_replace('#', '', $format->getTextColor())
            ),
            'size' => $format->getTextSize(),
        );


        $alignment = array(
            'horizontal' => $format->getAlign(),
        );

        $style = array(
            'alignment' => $alignment,
            'font' => $font,
        );

        $backgroundColor = $format->getBackgroundColor();
        if ($backgroundColor) {
            $style['fill'] = array(
                'color' => array(
                    'rgb' => str_replace('#', '', $backgroundColor)
                ),
                'type' => 'solid',
            );
        }

        return $style;
    }

}