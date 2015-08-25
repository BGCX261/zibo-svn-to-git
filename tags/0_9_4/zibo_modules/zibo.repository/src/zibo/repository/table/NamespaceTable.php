<?php

namespace zibo\repository\table;

use zibo\library\html\table\ExtendedTable;

use zibo\repository\table\decorator\NamespaceDecorator;

/**
 * Table to show a list of namespaces
 */
class NamespaceTable extends ExtendedTable {

    /**
     * Name of the form of the table
     * @var string
     */
    const FORM_NAME = 'formNamespaceTable';

    /**
     * Constructs a new namespace table
     * @param array $namespaces Array with ModuleNamespace objects, the data of the table
     * @param string $namespaceAction URL where the name of a namespace will point to, the name of the namespace will be concatted to the action
     * @param string $tableAction URL where the form of this table will point to
     * @return null
     */
    public function __construct(array $namespaces, $namespaceAction = null, $tableAction = null) {
        parent::__construct($namespaces, $tableAction, self::FORM_NAME);

        $this->setHasSearch(true);

        $this->addDecorator(new NamespaceDecorator($namespaceAction));
    }

    /**
     * Applies the search query to the values in this table
     * @return null
     */
    protected function applySearch() {
        if (!$this->searchQuery) {
            return;
        }

        foreach ($this->values as $index => $namespace) {
            if (strpos($namespace->getName(), $this->searchQuery) === false) {
                unset($this->values[$index]);
            }
        }
    }

}