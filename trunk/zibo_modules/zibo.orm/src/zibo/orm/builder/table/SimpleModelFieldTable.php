<?php

namespace zibo\orm\builder\table;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\SimpleTable;
use zibo\library\orm\definition\ModelTable;

use zibo\orm\builder\table\decorator\ModelFieldDecorator;
use zibo\orm\builder\table\decorator\ModelFieldLabelDecorator;

/**
 * Simple table for model fields
 */
class SimpleModelFieldTable extends SimpleTable {

    /**
     * Style id for this table
     * @var string
     */
    const STYLE_ID = 'tableModelField';

    /**
     * Constructs a new model field table
     * @param zibo\library\orm\definition\ModelTable $table Table containing the fields
     * @param string $fieldAction URL to the action for the field
     * @return null
     */
    public function __construct(ModelTable $table, $fieldAction = null) {
        $fields = $table->getFields();
        unset($fields[ModelTable::PRIMARY_KEY]);

        parent::__construct($fields);

        $this->setId(self::STYLE_ID);

        $this->addDecorator(new ZebraDecorator(new ModelFieldDecorator($fieldAction)));
        $this->addDecorator(new ModelFieldLabelDecorator());
    }

}