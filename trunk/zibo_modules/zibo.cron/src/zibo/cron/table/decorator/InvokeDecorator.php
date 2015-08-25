<?php

namespace zibo\cron\table\decorator;

use zibo\cron\model\CronJob;
use zibo\cron\table\CronJobTable;

use zibo\library\html\form\Form;

/**
 * Decorator for the callback of a cron job
 */
class InvokeDecorator extends AbstractCronJobDecorator {

    /**
     * The form holding the invoke button
     * @var zibo\library\html\form\Form
     */
    private $form;

    /**
     * Constructs a new cron job invoke decorator
     * @param zibo\library\html\form\Form $form Form holding the invoke button
     * @return null
     */
    public function __construct(Form $form) {
        $this->form = $form;
    }

    /**
     * Decorates the provided cron job
     * @param zibo\cron\model\CronJob $job Cron job to decorate
     * @return string
     */
    protected function decorateCronJob(CronJob $job) {
        $button = $this->form->getField(CronJobTable::BUTTON_INVOKE);

        return $button->getOptionHtml($job->getId());
    }

}