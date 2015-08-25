<?php

namespace zibo\cron\table\decorator;

use zibo\cron\model\CronJob;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

/**
 * Abstract cron job table decorator
 */
abstract class AbstractCronJobDecorator implements Decorator {

    /**
     * Decorates the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null|boolean
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $cronJob = $cell->getValue();
        if (!($cronJob instanceof CronJob)) {
            return;
        }

        $value = $this->decorateCronJob($cronJob);

        $cell->setValue($value);
    }

    /**
     * Decorates the provided cron job
     * @param zibo\cron\model\CronJob $job Cron job to decorate
     * @return string
     */
    abstract protected function decorateCronJob(CronJob $job);

}