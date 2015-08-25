<?php

namespace zibo\cron\table;

use zibo\cron\table\decorator\CallbackDecorator;
use zibo\cron\table\decorator\IntervalDecorator;
use zibo\cron\table\decorator\InvokeDecorator;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\table\decorator\StaticDecorator;
use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;
use zibo\library\i18n\I18n;

/**
 * Table for cron jobs
 */
class CronJobTable extends ExtendedTable {

    const NAME = 'tableCronJobs';

    /**
     * Name of the invoke button
     * @var string
     */
    const BUTTON_INVOKE = 'invoke';

    /**
     * Translation key for the interval label
     * @var string
     */
    const TRANSLATION_INTERVAL = 'cron.label.interval';

    /**
     * Translation key for the callback label
     * @var string
     */
    const TRANSLATION_CALLBACK = 'cron.label.callback';

    /**
     * Translation key for the invoke button
     * @var string
     */
    const TRANSLATION_INVOKE = 'cron.button.invoke';

    /**
     * Constructs a new cron job table
     * @param array $cronJobs Array with CronJob objects
     * @param string $formAction URL where the form of the table will point to
     * @return null
     */
    public function __construct(array $cronJobs, $formAction) {
        parent::__construct($cronJobs, $formAction, self::NAME);

        $translator = I18n::getInstance()->getTranslator();
        $translationInvoke = $translator->translate(self::TRANSLATION_INVOKE);

        $options = array();
        foreach ($cronJobs as $cronJob) {
            $options[$cronJob->getId()] = $translationInvoke;
        }

        $fieldFactory = FieldFactory::getInstance();

        $buttonInvoke = $fieldFactory->createField(FieldFactory::TYPE_SUBMIT, self::BUTTON_INVOKE);
        $buttonInvoke->setIsMultiple(true);
        $buttonInvoke->setOptions($options);

        $this->form->addField($buttonInvoke);

        $this->addDecorator(new ZebraDecorator(new IntervalDecorator()), new StaticDecorator(self::TRANSLATION_INTERVAL, true));
        $this->addDecorator(new CallbackDecorator(), new StaticDecorator(self::TRANSLATION_CALLBACK, true));
        $this->addDecorator(new InvokeDecorator($this->form));
    }

    /**
     * Gets the cron job for who the invoke button has been pressed
     * @return null|zibo\cron\model\CronJob
     */
    public function getInvokeCronJob() {
        $ids = $this->form->getValue(self::BUTTON_INVOKE);

        if (!$ids) {
            return null;
        }

        $ids = array_keys($ids);
        $id = array_pop($ids);

        foreach ($this->values as $cronJob) {
            if ($cronJob->getId() == $id) {
                return $cronJob;
            }
        }

        return null;
    }

}