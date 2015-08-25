<?php

namespace zibo\cron\view;

use zibo\admin\view\BaseView;

use zibo\cron\form\CronProcessForm;
use zibo\cron\table\CronJobTable;
use zibo\cron\Module;

/**
 * View for the cron job overview
 */
class CronView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'cron/index';

    /**
     * Constructs a new cron view
     * @param zibo\cron\table\CronJobTable $table Table with an overview of the cron jobs
     * @param zibo\cron\form\CronProcessForm $form Form to start or stop the cron process
     * @param string $command Command to start the cron process manually
     * @param boolean $isRunning Flag to see if the cron is currently running
     * @return null
     */
    public function __construct(CronJobTable $table, CronProcessForm $form, $command, $isRunning) {
        parent::__construct(self::TEMPLATE);

        $this->set('table', $table);
        $this->set('form', $form);
        $this->set('command', $command);
        $this->set('isRunning', $isRunning);

        $this->setPageTitle(Module::TRANSLATION_CRON, true);

        $this->addJavascript(self::SCRIPT_TABLE);
    }

}