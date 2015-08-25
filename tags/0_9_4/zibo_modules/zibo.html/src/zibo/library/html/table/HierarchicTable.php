<?php

namespace zibo\library\html\table;

use zibo\library\html\Breadcrumbs;
use zibo\library\Structure;

class HierarchicTable extends ExtendedTable {

    protected $breadcrumbs;

    protected $parents;

    public function __construct(array $values, array $parents, $formAction, $formName = null) {
        parent::__construct($values, $formAction, $formName);

        $this->breadcrumbs = new Breadcrumbs();
        $this->parents = $parents;
    }

    public function hasRows() {
        return parent::hasRows() || !empty($this->parents);
    }

    public function getBreadcrumbs() {
        return $this->breadcrumbs;
    }

    protected function applyOrder() {
        $result = parent::applyOrder();
        if (!$result) {
            return false;
        }

        if ($this->orderDirection === self::ORDER_DIRECTION_ASC) {
            $this->parents = $this->orderMethods[$this->orderMethod]->invokeAscending($this->parents);
        } else {
            $this->parents = $this->orderMethods[$this->orderMethod]->invokeDescending($this->parents);
        }

        return true;
    }

    protected function applyPagination() {
        $this->countRows = count($this->parents) + count($this->values);

        $this->values = Structure::merge($this->parents, $this->values);

        if (!$this->pageRows) {
            return;
        }

        $this->paginationPages = ceil($this->countRows / $this->pageRows);
        if ($this->page > $this->pages || $this->page < 1) {
            $this->page = 1;
        }

        $offset = ($this->page - 1) * $this->pageRows;

        $this->values = array_slice($this->values, $offset, $this->pageRows, true);
    }

}