<?php

namespace zibo\library\html\form\field;

use zibo\library\html\form\field\decorator\LocaleCodeDecorator;
use zibo\library\i18n\I18n;

/**
 * Locale list field
 */
class LocaleField extends ListField {

    /**
     * Initialize this field by setting the available locales as options
     * @return null
     */
    protected function init() {
        parent::init();

        $locales = I18n::getInstance()->getAllLocales();

        $this->setOptions($locales);
        $this->setValueDecorator(new LocaleCodeDecorator());
    }

}