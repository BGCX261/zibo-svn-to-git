<?php

namespace zibo\admin\table;

use zibo\admin\table\decorator\modules\ModuleDecorator;
use zibo\admin\table\decorator\modules\ReinstallActionDecorator;
use zibo\admin\table\decorator\modules\UninstallActionDecorator;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;

/**
 * Table to show an overview of the installed modules
 */
class ModulesTable extends ExtendedTable {

    /**
     * Name of the table form
     * @var string
     */
    const NAME = 'formModulesTable';

    /**
     * Constructs a new modules table
     * @param string $action URL where the form of the table will point to
     * @param array $modules Array with Module objects
     * @return null
     */
    public function __construct($action, array $modules) {
        $modules = $this->getModules($modules);

        $modules = $this->orderByName($modules);

        parent::__construct($modules, $action, self::NAME);

        $this->addDecorator(new ZebraDecorator(new ModuleDecorator()));
        $this->addDecorator(new ReinstallActionDecorator($action . '/reinstall/'));
        $this->addDecorator(new UninstallActionDecorator($action . '/uninstall/'));

//        $this->setPaginationOptions(array(5, 10, 25, 50, 100));
//        $this->setPagination(10);

        $this->setHasSearch(true);
    }

    private function getModules(array $modules) {
        $result = array();

        foreach ($modules as $module) {
            $result[] = $this->getArrayFromModule($module);
        }

        return $result;
    }

    private function getArrayFromModule($module) {
        $array = array();
        $array['name'] = $module->getName();
        $array['namespace'] = $module->getNamespace();
        $array['version'] = $module->getVersion();
        $array['path'] = $module->getPath();
        $array['dependencies'] = array();
        $array['usage'] = array();

        $dependencies = $module->getDependencies();
        foreach ($dependencies as $dependency) {
            $array['dependencies'][] = $this->getArrayFromModule($dependency);
        }
        $usage = $module->getUsage();
        foreach ($usage as $dependency) {
            $array['usage'][] = $this->getArrayFromModule($dependency);
        }

        $array['dependencies'] = $this->orderByName($array['dependencies']);
        $array['usage'] = $this->orderByName($array['usage']);

        return $array;
    }

    /**
     * Filters the modules of this table with the search query in the table form
     * @return null
     */
    protected function applySearch() {
        if (!$this->searchQuery) {
            return;
        }

        foreach ($this->values as $index => $module) {
            if (strpos($module['name'], $this->searchQuery) === false && strpos($module['namespace'], $this->searchQuery) === false) {
                unset($this->values[$index]);
            }
        }
    }

    public function orderByName($modules) {
        usort($modules, array($this, 'compareByName'));
        return $modules;
    }

    public function orderByNamespace($modules) {
        usort($modules, array($this, 'compareByNamespace'));
        return $modules;
    }

    private function compareByName($a, $b) {
        return strcmp($a['name'], $b['name']);
    }

    private function compareByNamespace($a, $b) {
        $result = strcmp($a['namespace'], $b['namespace']);

        if ($result != 0) {
            return $result;
        }

        return $this->compareByName($a, $b);
    }

}