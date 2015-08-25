<?php

/**
 * @package zibo-log-filter
 */
namespace zibo\log\filter;

use zibo\core\Zibo;

use zibo\library\Boolean;

use zibo\log\Module;

abstract class AbstractInvertLogItemFilter implements LogItemFilter {

	private $invert = false;

    public function setInvert($flag) {
        $this->invert = Boolean::getBoolean($flag);
    }

    public function willInvert() {
        return $this->invert;
    }

    protected static function setInvertToCreatedFilter(AbstractInvertLogItemFilter $filter, Zibo $zibo, $name, $configBase) {
        $configInvert = $configBase . Module::CONFIG_INVERT;
        $invert = $zibo->getConfigValue($configInvert);
        if ($invert !== null) {
            $filter->setInvert($invert);
        }
    }

}