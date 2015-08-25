<?php

namespace zibo\repository\table;

use zibo\library\html\table\ExtendedTable;

use zibo\repository\table\decorator\ModuleDecorator;

/**
 * Table to show a list of modules
 */
class ModuleTable extends ExtendedTable {

    /**
     * Name of the form of the table
     * @var string
     */
    const FORM_NAME = 'formModuleTable';

    /**
     * Constructs a new module table
     * @param array $modules Array with Module objects, the data of the table
     * @param string $moduleAction URL where the name of a module will point to, the name of the module will be concatted to the action
     * @param string $tableAction URL where the form of this table will point to
     * @return null
     */
    public function __construct(array $modules, $moduleAction = null, $tableAction = null) {
        parent::__construct($modules, $tableAction, self::FORM_NAME);

        $this->setHasSearch(true);

        $this->addDecorator(new ModuleDecorator($moduleAction));
    }

    /**
     * Applies the search query to the values in this table
     * @return null
     */
    protected function applySearch() {
        if (!$this->searchQuery) {
            return;
        }

        foreach ($this->values as $index => $module) {
            if (strpos($module->getName(), $this->searchQuery) === false) {
                unset($this->values[$index]);
            }
        }
    }

}