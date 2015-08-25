<?php

namespace zibo\library\html\form;

use zibo\library\html\form\field\FieldFactory;

/**
 * Form with a done button
 */
class DoneForm extends Form {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formDone';

    /**
     * Name of the done button
     * @var string
     */
    const BUTTON_DONE = 'done';

    /**
     * Translation key for the done button
     * @var string
     */
    const TRANSLATION_DONE = 'button.done';

    /**
     * Constructs a new done form
     * @param string $action URL where the form will point to
     * @param string $translationDone Translation key for the done button
     * @param string $formName Name of the form
     * @return null
     */
    public function __construct($action, $translationDone = null, $formName = null) {
        if (!$formName) {
            $formName = self::NAME;
        }

        parent::__construct($action, $formName);

        if (!$translationDone) {
            $translationDone = self::TRANSLATION_DONE;
        }

        $doneField = FieldFactory::getInstance()->createSubmitField(self::BUTTON_DONE, $translationDone);

        $this->addField($doneField);
    }

}