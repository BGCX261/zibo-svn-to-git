<?php

namespace zibo\filebrowser\table\decorator;

use zibo\library\filesystem\File;
use zibo\library\html\table\decorator\AnchorDecorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\i18n\I18n;

/**
 * Base decorator for a File action
 */
class FileActionDecorator extends AnchorDecorator {

    /**
     * Style class for a action cell
     * @var string
     */
    const CLASS_ACTION = 'action';

    /**
     * The label for the button
     * @var string
     */
    private $label;

    /**
     * Constructs a new file action decorator
     * @param string $href The URL to the action
     * @param string $translationKey Translation key for the label of the button
     * @return null
     */
    public function __construct($href, $translationKey) {
        parent::__construct($href);

        $translator = I18n::getInstance()->getTranslator();
        $this->label = $translator->translate($translationKey);
    }

    /**
     * Decorates a table cell by setting an anchor to the cell based on the cell's value
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $cell->appendToClass(self::CLASS_ACTION);

        $file = $cell->getValue();
        if (!($file instanceof File)) {
            $cell->setValue('');
            return;
        }

        parent::decorate($cell, $row, $rowNumber, $remainingValues);
    }

    /**
     * Gets the label for the anchor
     * @param mixed $value Value of the cell
     * @return string Label for the anchor
     */
    protected function getLabelFromValue($value) {
        return $this->label;
    }

    /**
     * Gets the href attribute for the anchor
     * @param mixed $value Value of the cell
     * @return string Href attribute for the anchor
     */
    protected function getHrefFromValue($value) {
        return $this->href . $value->getPath();
    }

}