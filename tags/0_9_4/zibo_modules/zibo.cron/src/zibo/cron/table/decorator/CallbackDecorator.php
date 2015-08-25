<?php

namespace zibo\cron\table\decorator;

use zibo\cron\model\CronJob;

/**
 * Decorator for the callback of a cron job
 */
class CallbackDecorator extends AbstractCronJobDecorator {

    /**
     * Decorates the provided cron job
     * @param zibo\cron\model\CronJob $job Cron job to decorate
     * @return string
     */
    protected function decorateCronJob(CronJob $job) {
        return $job->getCallback()->__toString();
    }

}