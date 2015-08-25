<?php

namespace zibo\orm\builder\table;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\SimpleTable;
use zibo\library\orm\definition\ModelTable;

use zibo\orm\builder\table\decorator\IndexDecorator;

/**
 * Simple table for model indexes
 */
class SimpleModelIndexTable extends SimpleTable {

    /**
     * Style id for this table
     * @var string
     */
    const STYLE_ID = 'tableModelIndex';

    /**
     * Constructs a new model index table
     * @param zibo\library\orm\definition\ModelTable $table Table containing the indexes
     * @param string $indexAction URL to the action for the index
     * @return null
     */
    public function __construct(ModelTable $table, $indexAction = null) {
        $indexes = $table->getIndexes();
        if (!$indexes) {
            $indexes = array();
        }

        parent::__construct($indexes);

        $this->setId(self::STYLE_ID);

        $this->addDecorator(new ZebraDecorator(new IndexDecorator($indexAction)));
    }

}