<?php

use zibo\library\i18n\I18n;

function smarty_modifier_translate($string) {
    $translator = I18n::getInstance()->getTranslator();
    return $translator->translate($string);
}