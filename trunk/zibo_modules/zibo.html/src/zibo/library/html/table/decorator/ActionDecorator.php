<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\i18n\I18n;

/**
 * Abstract decorator to create an action
 */
abstract class ActionDecorator extends ConfirmAnchorDecorator {

    /**
     * Style class for action cells
     * @var string
     */
    const STYLE_ACTION = 'action';

    /**
     * Translation key for the label of the action
     * @var string
     */
    protected $label;

    /**
     * Translator instance
     * @var zibo\library\i18n\translation\Translator
     */
    protected $translator;

    /**
     * Flag to hide the action
     * @var boolean
     */
    private $willDisplay;

    /**
     * Flag to disable the action
     * @var boolean
     */
    private $isDisabled;

    /**
     * Constructs a new action decorator
     * @param string $href Base href attribute for the action
     * @param string $label Translation key for the label
     * @param string $message Translation key for the message
     * @return null
     */
    public function __construct($href, $label, $message = null) {
        parent::__construct($href, $message);

        $this->label = $label;

        $this->translator = I18n::getInstance()->getTranslator();
    }

    /**
     * Decorates the cell with the action for the value of the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell
     * @param integer $rowNumber Current row number
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $this->willDisplay = true;
        $this->isDisabled = false;

        $cell->appendToClass(self::STYLE_ACTION);

        $value = $cell->getValue();

        parent::decorate($cell, $row, $rowNumber, $remainingValues);

        if (!$this->willDisplay) {
            $cell->setValue('');
            return;
        }

        if (!$this->isDisabled) {
            return;
        }

        $label = $this->getLabelFromValue($value);

        $cell->setValue($label);
    }

    /**
     * Gets the label for the anchor
     * @param mixed $value Value of the cell
     * @return string Label for the anchor
     */
    protected function getLabelFromValue($value) {
        $label = $this->processLabel($value);

        if ($label === null) {
            $label = $this->translator->translate($this->label);
        }

        return $label;
    }

    /**
     * Hook to process the label of the action with the value of the cell
     * @param mixed $value Value of the cell
     * @return string|null A string to use as label or null to the plain translation of the label
     */
    protected function processLabel($value) {
        return null;
    }

    /**
     * Gets the translation of the message
     * @param mixed $value Value of the cell
     * @return string|null The translation of the message to use for the confirmation, null if no message was set while constructing
     */
    protected function processMessage($value) {
        if (!$this->message) {
            return null;
        }

        return $this->translator->translate($this->message);
    }

    /**
     * Sets whether the decorator will display the action of the current row
     * @param boolean $flag
     * @return null
     */
    protected function setWillDisplay($flag) {
        $this->willDisplay = $flag;
    }

    /**
     * Sets whether will disable the action by only providing the label of the action, not the anchor
     * @param boolean $flag True to only display the label, false to display the full action
     * @return null
     */
    protected function setIsDisabled($flag) {
        $this->isDisabled = $flag;
    }

}