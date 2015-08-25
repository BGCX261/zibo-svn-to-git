<?php

/**
 * @package zibo-log-filter
 */
namespace zibo\log\filter;

use zibo\core\Zibo;

use zibo\library\String;

use zibo\log\LogItem;

use zibo\ZiboException;

class NameFilter extends AbstractInvertLogItemFilter {

	const CONFIG_NAMES = 'names';

	private $names = array();

	public function addAllowedName($name) {
		if (String::isEmpty($name)) {
			throw new ZiboException('Provided name is empty');
		}
		$this->names[] = $name;
	}

	public function setAllowedName($name) {
		$this->names = array();
		$this->addAllowedName($name);
	}

	public function setAllowedNames(array $names) {
		$this->names = array();
		foreach ($names as $index => $name) {
			$this->addAllowedName($name);
		}
	}

	public function allowLogItem(LogItem $item) {
		$name = $item->getName();

		$allowed = false;

		if (in_array($name, $this->names)) {
			$allowed = true;;
		}

		if ($this->willInvert()) {
			$allowed = !$allowed;
		}

		return $allowed;
	}

    public static function createFilterFromConfig(Zibo $zibo, $name, $configBase) {
        $filter = new self();

        parent::setInvertToCreatedFilter($filter, $zibo, $name, $configBase);

    	$configNames = $configBase . self::CONFIG_NAMES;
        $names = $zibo->getConfigValue($configNames);
        if ($names === null) {
        	return $filter;
        }

        $names = explode(',', $names);
        foreach ($names as $name) {
        	$name = trim($name);
        	$filter->addAllowedName($name);
        }

        return $filter;
    }

}