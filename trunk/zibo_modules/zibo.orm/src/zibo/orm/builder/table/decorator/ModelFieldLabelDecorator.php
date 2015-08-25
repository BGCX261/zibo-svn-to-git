<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\field\ModelField;

/**
 * Decorator for the label of a model field
 */
class ModelFieldLabelDecorator implements Decorator {

    /**
     * Translator for the labels
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new model field label decorator
     * @param zibo\library\i18n\translation\Translator $translator
     * @return null
     */
    public function __construct(Translator $translator = null) {
        if (!$translator) {
            $translator = I18n::getInstance()->getTranslator();
        }

        $this->translator = $translator;
    }

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

        $label = $field->getLabel();
        if (!$label) {
            $cell->setValue('');
            return;
        }

        $value = $this->translator->translate($label);
        $value .= '<div class="info">' . $label . '</div>';

        $cell->setValue($value);
    }

}