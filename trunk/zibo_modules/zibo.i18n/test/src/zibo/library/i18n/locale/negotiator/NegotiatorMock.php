<?php

namespace zibo\library\i18n\locale\negotiator;

use zibo\library\i18n\locale\io\LocaleIO;

class NegotiatorMock implements Negotiator {

    public function getLocale(LocaleIO $io) {
        return null;
    }

}