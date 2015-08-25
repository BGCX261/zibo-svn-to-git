<?php

namespace zibo\orm\scaffold\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\orm\model\meta\ModelMeta;

/**
 * Decorator for a orm data object based on a provided data format
 */
class DataFormatDecorator implements Decorator {

    /**
     * Meta of the data model
     * @var zibo\library\orm\model\meta\ModelMeta
     */
    private $meta;

    /**
     * The name of the format to use when decorating the data
     * @var string
     */
    private $format;

    /**
     * Constructs a new data decorator
     * @param zibo\library\orm\model\meta\ModelMeta $meta
     * @param string $format The name of the format to use when decorating the data
     * @return null
     */
    public function __construct(ModelMeta $meta, $format) {
        $this->meta = $meta;
        $this->format = $format;
    }

    /**
     * Decorates the data in the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array with the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $data = $cell->getValue();

        if (!$this->meta->isValidData($data)) {
            $cell->setValue('');
        }

        $value = $this->meta->formatData($data, $this->format);

        $cell->setValue($value);
    }

}