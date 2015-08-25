<?php

namespace zibo\repository\table;

use zibo\library\html\table\ExtendedTable;

use zibo\repository\table\decorator\ModuleVersionDecorator;

/**
 * Table to show the content of the repository
 */
class ModuleVersionTable extends ExtendedTable {

    /**
     * Name of the form of this table
     * @var string
     */
    const FORM_NAME = 'formModuleVersionTable';

    /**
     * Constructs a new module version table
     * @param array $versions Array with Module objects
     * @param string $versionAction URL for the version link
     * @param string $tableAction URL for the table form
     * @return null
     */
    public function __construct(array $versions, $versionAction = null, $tableAction = null) {
        parent::__construct($versions, $tableAction, self::FORM_NAME);

        $this->addDecorator(new ModuleVersionDecorator($versionAction));
    }

}