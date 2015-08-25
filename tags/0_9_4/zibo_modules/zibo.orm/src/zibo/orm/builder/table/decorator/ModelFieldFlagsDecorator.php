<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Image;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\field\ModelField;

/**
 * Decorator for the flags of a model field
 */
class ModelFieldFlagsDecorator implements Decorator {

    /**
     * Path to the image of a localized field
     * @var string
     */
    const IMAGE_LOCALIZED = 'web/images/orm/localized.png';

    /**
     * Translation key for the alternate text of the localized image
     * @var string
     */
    const TRANSLATION_LOCALIZED = 'orm.label.localized';

    /**
     * The HTML of the localized image
     * @var string
     */
    private $localizedImage = null;

    /**
     * Decorates the cell
     * @param zibo\library\html\table\Cell $cell Cell of the value to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $field = $cell->getValue();
        if (!($field instanceof ModelField)) {
            return;
        }

        if (!$field->isLocalized()) {
            $cell->setValue('');
            return;
        }

        $cell->setValue($this->getLocalizedImageHtml());
    }

    /**
     * Gets the HTML of the localized image
     * @return string The HTML of the localized image
     */
    private function getLocalizedImageHtml() {
        if ($this->localizedImage) {
            return $this->localizedImage;
        }

        $translator = I18n::getInstance()->getTranslator();

        $image = new Image(self::IMAGE_LOCALIZED);
        $image->setAttribute('title', $translator->translate(self::TRANSLATION_LOCALIZED));

        $this->localizedImage = $image->getHtml();

        return $this->localizedImage;
    }

}