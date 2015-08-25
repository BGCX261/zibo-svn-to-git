<?php

namespace zibo\orm\builder\table;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\SimpleTable;
use zibo\library\orm\definition\ModelTable;

use zibo\orm\builder\table\decorator\DataFormatDecorator;

/**
 * Simple table for data formats
 */
class SimpleDataFormatTable extends SimpleTable {

    /**
     * Style id for this table
     * @var string
     */
    const STYLE_ID = 'tableDataFormat';

    /**
     * Constructs a new data format table
     * @param zibo\library\orm\definition\ModelTable $table Table containing the data formats
     * @param string $formatAction URL to the action for the format
     * @return null
     */
    public function __construct(ModelTable $table, $formatAction = null) {
        $formats = $table->getDataFormats();

        parent::__construct($formats);

        $this->setId(self::STYLE_ID);

        $this->addDecorator(new ZebraDecorator(new DataFormatDecorator($formatAction)));
    }

}