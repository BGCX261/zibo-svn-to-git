<?php

namespace zibo\terminal\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form for the terminal
 */
class TerminalForm extends Form {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formTerminal';

    /**
     * Name of the command field
     * @var string
     */
    const FIELD_COMMAND = 'command';

    /**
     * Constructs a new terminal form
     * @param string $action URL where this form will point to
     * @param string $command The initial command
     * @return null
     */
    public function __construct($action, $command = null) {
        parent::__construct($action, self::NAME);

        $fieldFactory = FieldFactory::getInstance();

        $commandField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_COMMAND, $command);

        $this->addField($commandField);
    }

    /**
     * Gets the submitted command
     * @return string
     */
    public function getCommand() {
        return $this->getValue(self::FIELD_COMMAND);
    }

}