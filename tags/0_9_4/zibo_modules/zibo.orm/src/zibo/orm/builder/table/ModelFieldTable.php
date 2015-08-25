<?php

namespace zibo\orm\builder\table;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;
use zibo\library\orm\definition\ModelTable;

use zibo\orm\builder\table\decorator\ModelFieldDecorator;
use zibo\orm\builder\table\decorator\ModelFieldFlagsDecorator;
use zibo\orm\builder\table\decorator\ModelFieldLabelDecorator;
use zibo\orm\builder\table\decorator\ModelFieldOptionDecorator;

/**
 * Extended table for model fields
 */
class ModelFieldTable extends ExtendedTable {

    /**
     * Name of the table
     * @var string
     */
    const NAME = 'tableModelField';

    /**
     * Constructs a new model field table
     * @param zibo\library\orm\definition\ModelTable $table Table containing the fields
     * @param string $fieldAction URL to the action of a field
     * @param string $modelAction URL to the action of a model
     * @return null
     */
    public function __construct(ModelTable $table, $tableAction, $fieldAction = null, $modelAction = null) {
        $fields = $table->getFields();
        unset($fields[ModelTable::PRIMARY_KEY]);

        parent::__construct($fields, $tableAction, self::NAME);

        $this->addDecorator(new ZebraDecorator(new ModelFieldDecorator($fieldAction, $modelAction)));
        $this->addDecorator(new ModelFieldLabelDecorator());
        $this->addDecorator(new ModelFieldFlagsDecorator());
    }

    /**
     * Gets the HTML of this table
     * @return string
     */
    public function getHtml() {
        if ($this->actions) {
            $this->addDecorator(new ModelFieldOptionDecorator(), null, true);
        }

        return parent::getHtml();
    }

}