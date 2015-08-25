<?php

namespace zibo\cron\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form to start or stop the cron process
 */
class CronProcessForm extends Form {

    /**
     * The name of this form
     * @var string
     */
    const NAME = 'formCronProcess';

    /**
     * The name of the submit button
     * @var string
     */
    const BUTTON_SUBMIT = 'submit';

    /**
     * The translation key for the start button
     * @var string
     */
    const TRANSLATION_START = 'cron.button.start';

    /**
     * The translation key for the stop button
     * @var string
     */
    const TRANSLATION_STOP = 'cron.button.stop';

    /**
     * Constructs a new cron process form
     * @param string $action URL where this form will point to
     * @param boolean $isRunning Flag to see if the cron process is currently running
     * @return null
     */
    public function __construct($action, $isRunning) {
        parent::__construct($action, self::NAME);

        $fieldFactory = FieldFactory::getInstance();

        if ($isRunning) {
            $translationKey = self::TRANSLATION_STOP;
        } else {
            $translationKey = self::TRANSLATION_START;
        }

        $submitButton = $fieldFactory->createSubmitField(self::BUTTON_SUBMIT, $translationKey);

        $this->addField($submitButton);
    }

}