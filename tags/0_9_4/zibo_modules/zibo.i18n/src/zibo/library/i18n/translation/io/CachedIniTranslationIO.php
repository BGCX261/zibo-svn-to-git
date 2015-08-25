<?php

namespace zibo\library\i18n\translation\io;

/**
 * Cached INI implementation of the TranslationIO
 */
class CachedIniTranslationIO extends CachedTranslationIO {

    /**
     * Constructs a new cached INI TranslationIO
     * @return null
     */
    public function __construct() {
        parent::__construct(new IniTranslationIO());
    }

}