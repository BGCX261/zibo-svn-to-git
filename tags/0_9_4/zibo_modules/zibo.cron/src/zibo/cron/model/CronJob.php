<?php

namespace zibo\cron\model;

/**
 * interface for an automated task
 */
interface CronJob {

	/**
	 * Gets a unique id for this job
	 * @return string
	 */
	public function getId();

    /**
     * Gets the callback of this job
     * @return zibo\library\Callback
     */
    public function getCallback();

    /**
     * Gets the interval definition
     * @return string
     */
    public function getIntervalDefinition();

    /**
     * Runs this job
     * @return null
     */
    public function run();

    /**
     * Gets the time when this job run for the last time
     * @return int
     */
    public function getLastRunTime();

    /**
     * Gets the time when this job should run next
     * @param int $time if not provided, the last run time will be used or now if this job hasn't run yet
     * @return int
     */
	public function getNextRunTime($time = null);

}