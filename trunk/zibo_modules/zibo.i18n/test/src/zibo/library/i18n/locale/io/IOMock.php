<?php

namespace zibo\library\i18n\locale\io;

use zibo\library\i18n\locale\Locale;

class IOMock implements LocaleIO {

    public function getLocales() {
        return array(
            'nl'    => $this->getLocale('nl'),
            'en'    => $this->getLocale('en'),
            'en_GB' => $this->getLocale('en_GB'),
            'fr'    => $this->getLocale('fr'),
        );
    }

    public function getLocale($code) {
        switch($code) {
            case 'en':
                return new Locale('en', 'English');
            case 'en_GB':
                return new Locale('en_GB', 'British English');
            case 'nl':
                return new Locale('nl', 'Nederlands');
            case 'fr':
                return new Locale('fr', 'français');
        }

        return null;
    }

}