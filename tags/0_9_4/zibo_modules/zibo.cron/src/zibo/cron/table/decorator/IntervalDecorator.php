<?php

namespace zibo\cron\table\decorator;

use zibo\cron\model\CronJob;

/**
 * Decorator for the interval of a cron job
 */
class IntervalDecorator extends AbstractCronJobDecorator {

    /**
     * Decorates the provided cron job
     * @param zibo\cron\model\CronJob $job Cron job to decorate
     * @return string
     */
    protected function decorateCronJob(CronJob $job) {
        return $job->getIntervalDefinition();
    }

}