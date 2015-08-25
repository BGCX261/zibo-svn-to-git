<?php

/**
 * @package zibo-log-listener
 */
namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\library\config\Config;
use zibo\library\Boolean;

use zibo\log\filter\LogItemFilterFactory;
use zibo\log\filter\LogItemFilter;
use zibo\log\LogItem;
use zibo\log\Module;

/**
 * Writes log items which pass the filters to file
 */
abstract class AbstractFilteredLogListener implements LogListener {

	const CONFIG_FILTER = 'filter';
	const CONFIG_FILTER_ALL = 'all';
	const CONFIG_INVERT = 'invert';

    private $filters = array();
    private $filterAll = false;
    private $invert = null;

    public function addFilter(LogItemFilter $filter) {
    	if ($this->invert === null) {
    		$this->invert = false;
    	}
    	$this->filters[] = $filter;
    }

    public function setFilterAllFilters($flag) {
    	$this->filterAll = Boolean::getBoolean($flag);
    }

    public function willFilterAllFilters() {
    	return $this->filterAll;
    }

    public function setInvert($flag) {
    	$this->invert = Boolean::getBoolean($flag);
    }

    public function willInvert() {
        if ($this->invert === null) {
            $this->invert = true;
        }
    	return $this->invert;
    }

    public function addLogItem(LogItem $item) {
        $willWrite = $this->applyFilters($item);

        if ($this->willInvert()) {
            $willWrite = !$willWrite;
        }

        if ($willWrite) {
            $this->writeLogItem($item);
        }
    }

    abstract protected function writeLogItem(LogItem $item);

    protected function applyFilters(LogItem $item) {
        $filterAll = $this->willFilterAllFilters();
        $willWrite = false;

        foreach ($this->filters as $filter) {
            if ($filter->allowLogItem($item)) {
                $willWrite = true;
            } elseif ($filterAll) {
                $willWrite = false;
                break;
            }
        }

        return $willWrite;
    }

    protected static function addFiltersToCreatedListener(AbstractFilteredLogListener $listener, Zibo $zibo, $name, $configBase) {
    	$configBase .= Module::CONFIG_FILTER;

        $config = $zibo->getConfigValue($configBase);
        if (empty($config)) {
        	return;
        }

        if (isset($config[Module::CONFIG_FILTER_ALL])) {
        	$listener->setFilterAllFilters($config[Module::CONFIG_FILTER_ALL]);
            unset($config[Module::CONFIG_FILTER_ALL]);
        }
        if (isset($config[Module::CONFIG_INVERT])) {
        	$listener->setInvert($config[Module::CONFIG_INVERT]);
            unset($config[Module::CONFIG_INVERT]);
        }

        $filterFactory = new LogItemFilterFactory($zibo);

        foreach ($config as $name => $parameters) {
        	$filterConfigBase = $configBase . Config::TOKEN_SEPARATOR . $name . Config::TOKEN_SEPARATOR;
            $filter = $filterFactory->createFilter($name, $filterConfigBase);
            if ($filter != null) {
                $listener->addFilter($filter);
            }
        }
    }

}