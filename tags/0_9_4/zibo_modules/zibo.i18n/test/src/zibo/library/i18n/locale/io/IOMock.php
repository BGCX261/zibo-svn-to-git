<?php

namespace zibo\library\i18n\locale\io;

use zibo\library\i18n\locale\Locale;

class IOMock implements LocaleIO {

    public function getAllLocales() {
        return array(
            'en'    => $this->getLocale('en'),
            'en_GB' => $this->getLocale('en_GB'),
            'nl'    => $this->getLocale('nl'),
            'fr'    => $this->getLocale('fr'),
        );
    }

    public function getLocale($code) {
        switch($code) {
            case 'en':
                return new Locale('en', 'English', 'English');
            case 'en_GB':
                return new Locale('en_GB', 'British English', 'British English');
            case 'nl':
                return new Locale('nl', 'Dutch', 'Nederlands');
            case 'fr':
                return new Locale('fr', 'French', 'français');
        }

        return null;
    }

}