<?php

namespace zibo\library\html\form;

use zibo\library\html\form\field\FieldFactory;

/**
 * Empty form with a submit and cancel button
 */
class SubmitCancelForm extends Form {

    /**
     * Field name for the submit button
     * @var string
     */
    const BUTTON_SUBMIT = 'submit';

    /**
     * Field name for the cancel button
     * @var string
     */
    const BUTTON_CANCEL = 'cancel';

    /**
     * Default translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'button.submit';

    /**
     * Default translation key for the cancel button
     * @var string
     */
    const TRANSLATION_CANCEL = 'button.cancel';

    /**
     * Construct a new form
     * @param string $action URL where the form will point to
     * @param string $name Name of the form
     * @param string $translationSubmit Translation key for the submit button
     * @param string $translationCancelTranslation key for the cancel button
     * @return null
     */
    public function __construct($action, $name, $translationSubmit = null, $translationCancel = null) {
        parent::__construct($action, $name);

        $fieldFactory = FieldFactory::getInstance();

        if (!$translationSubmit) {
            $translationSubmit = self::TRANSLATION_SUBMIT;
        }
        $submitButton = $fieldFactory->createSubmitField(self::BUTTON_SUBMIT, $translationSubmit);

        if (!$translationCancel) {
            $translationCancel = self::TRANSLATION_CANCEL;
        }
        $cancelButton = $fieldFactory->createSubmitField(self::BUTTON_CANCEL, $translationCancel);

        $this->addField($submitButton);
        $this->addField($cancelButton);
    }

    /**
     * Checks whether this form was canceled
     * @return boolean True if the cancel button was pressed, false otherwise
     */
    public function isCancelled() {
        return $this->getValue(self::BUTTON_CANCEL) ? true : false;
    }

}