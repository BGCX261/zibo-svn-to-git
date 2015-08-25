<?php

namespace zibo\library\i18n\locale\negotiator;

use zibo\library\i18n\locale\LocaleManager;

/**
 * Locale negotiator
 */
interface Negotiator {

    /**
     * Determines which locale to use
     *
     * @param zibo\library\i18n\locale\LocaleManager $manager Instance of the
     * locale manager
     * @return null|zibo\library\i18n\locale\Locale the locale
     */
    public function getLocale(LocaleManager $manager);

}