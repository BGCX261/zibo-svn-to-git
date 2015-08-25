<?php

namespace zibo\library\html\form\field\decorator;

use zibo\library\i18n\locale\Locale;

/**
 * Decorator for a Locale object
 */
class LocaleCodeDecorator implements Decorator {

    /**
     * Decorates a Locale object into a string
     * @param mixed $value
     * @return string the locale code if the value is a Locale, '---' otherwise
     */
    public function decorate($value) {
        if (!($value instanceof Locale)) {
            return '---';
        }

        return $value->getCode();
    }

}