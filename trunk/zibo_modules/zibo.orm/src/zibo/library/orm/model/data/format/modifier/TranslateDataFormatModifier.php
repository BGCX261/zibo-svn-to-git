<?php

namespace zibo\library\orm\model\data\format\modifier;

use zibo\library\i18n\I18n;

/**
 * Modifier to convert new lines into br HTML tags
 */
class TranslateDataFormatModifier implements DataFormatModifier {

    /**
     * Converts all new lines into br HTML tags
     * @param string $value Value to convert all the new lines from
     * @param array $arguments Array with arguments for this modifier (not used)
     * @return string
     */
    public function modifyValue($value, array $arguments) {
        return I18n::getInstance()->getTranslator()->translate($value);
    }

}