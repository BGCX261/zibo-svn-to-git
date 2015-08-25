<?php

namespace zibo\orm\builder\table;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;

use zibo\orm\builder\table\decorator\ModelDecorator;
use zibo\orm\builder\table\decorator\ModelOptionDecorator;
use zibo\orm\Module;

class ModelTable extends ExtendedTable {

    const NAME = 'tableModel';

    public function __construct(array $models, $tableAction, $modelAction = null) {
        ksort($models);

        parent::__construct($models, $tableAction, self::NAME);

        $this->addDecorator(new ZebraDecorator(new ModelDecorator($modelAction)));
//        $this->addDecorator(new BuilderTableActionDecorator($basePath . '/scaffold/', 'orm.button.scaffold'));
    }

    public function getHtml() {
        if ($this->actions) {
            $this->addDecorator(new ModelOptionDecorator(), null, true);
        }

        return parent::getHtml();
    }

}