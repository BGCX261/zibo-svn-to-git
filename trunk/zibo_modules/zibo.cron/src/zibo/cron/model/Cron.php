<?php

namespace zibo\cron\model;

use zibo\core\Zibo;

use zibo\ZiboException;

use \Exception;

/**
 * Object to run automated tasks at defined times
 */
class Cron {

    /**
     * Event to handle on a job error
     * @var string
     */
    const EVENT_ERROR = 'cron.error';

    /**
     * Name of the log
     * @var string
     */
	const LOG_NAME = 'cron';

	/**
	 * Array with the registered jobs
	 * @var array
	 */
	private $jobs;

	/**
	 * Constructs a new cron server
	 * @return null
	 */
	public function __construct() {
		$this->jobs = array();
	}

    /**
     * Gets all the registered jobs
     * @return array Array with CronJob objects
     */
    public function getJobs() {
        return $this->jobs;
    }

	/**
     * Registers a new job with the job parameters
     * @param string|array|zibo\library\Callback $callback
     * @param string $minute
     * @param string $hour
     * @param string $day
     * @param string $month
     * @param string $dayOfWeek
     * @return
	 */
	public function registerJob($callback, $minute = null, $hour = null, $day = null, $month = null, $dayOfWeek = null) {
		$job = new GenericCronJob($callback, $minute, $hour, $day, $month, $dayOfWeek);

		return $this->registerCronJob($job);
	}

	/**
     * Registers a new job with a job object
     * @param CronJob $job
     * @return integer The id of the job
     * @throws zibo\ZiboException when the provided job id is already in use
	 */
	public function registerCronJob(CronJob $job) {
		$id = $job->getId();

		if (array_key_exists($id, $this->jobs)) {
		    throw new ZiboException('Could not register the job: the id of the job is already in use');
		}

		$this->jobs[$id] = $job;

		return $id;
	}

	/**
	 * Removes a job
	 * @param string $id The id of the job
	 * @return null
	 */
	public function removeJob($id) {
	    if (array_key_exists($id, $this->jobs)) {
	        unset($this->jobs[$id]);
	    }
	}

	/**
     * Runs the server.
     *
     * If no loop provided, this will keep on running until killed unless there are no jobs.
     * @param int $loop number of loops to make (optional)
     * @return null
	 */
	public function run($loop = 0) {
		$zibo = Zibo::getInstance();
        $zibo->runEvent(Zibo::EVENT_LOG, 'Initializing cron', '', 0, self::LOG_NAME);
		if (empty($this->jobs)) {
            $zibo->runEvent(Zibo::EVENT_LOG, 'No jobs registered, returning', '', 0, self::LOG_NAME);
			return;
		}

        $runOrder = $this->getRunOrder(time());
        $zibo->runEvent(Zibo::EVENT_LOG, 'Registered jobs:', '', 0, self::LOG_NAME);
        foreach ($runOrder as $jobId => $nextRunTime) {
            $zibo->runEvent(Zibo::EVENT_LOG, $this->jobs[$jobId]->__toString(), 'next runtime: ' . date('Y-m-d H:i:s', $nextRunTime), 0, self::LOG_NAME);
        }

        $index = 1;
        do {
        	if ($loop) {
                $zibo->runEvent(Zibo::EVENT_LOG, 'Loop ' . $index, '', 0, self::LOG_NAME);
        	}

            $sleepTime = 0;
            $executed = false;
        	$time = time();

            foreach ($runOrder as $jobId => $nextRunTime) {
            	if ($nextRunTime > $time) {
            		if (!$executed) {
                        $sleepTime = $nextRunTime - $time;
            		}
            		break;
            	}

            	$executed = true;
                $job = $this->jobs[$jobId];
            	$jobString = $job->__toString();

            	$lastRunTime = $job->getLastRunTime();
            	$zibo->runEvent(Zibo::EVENT_LOG, 'Executing ' . $jobString, 'last runtime: ' . ($lastRunTime ? date('Y-m-d H:i:s', $lastRunTime) : '---'), 0, self::LOG_NAME);

            	try {
                    $job->run();
            	} catch (Exception $exception) {
            	    $message = $exception->getMessage();
            	    if (!$message) {
            	        $message = get_class($exception);
            	    }

                	$zibo->runEvent(Zibo::EVENT_LOG, 'Error when executing ' . $jobString . ': ' . $message, $exception->getTraceAsString(), 1, self::LOG_NAME);
                    $zibo->runEvent(self::EVENT_ERROR, $job, $exception);
            	}

            	$runOrder[$jobId] = $job->getNextRunTime();
            	$zibo->runEvent(Zibo::EVENT_LOG, 'Done with ' . $jobString, 'next runtime: ' . date('Y-m-d H:i:s', $runOrder[$jobId]), 0, self::LOG_NAME);
            }

            if ($sleepTime) {
            	$zibo->runEvent(Zibo::EVENT_LOG, 'Sleeping ' . $sleepTime . ' seconds', 'Wake up at ' . date('Y-m-d H:i:s', $time + $sleepTime), 0, self::LOG_NAME);
            	sleep($sleepTime);
            } else {
            	asort($runOrder);
            }

            $index++;
		} while ($loop == 0 || $index <= $loop);
	}

	/**
     * Gets an array with the run order of the jobs
     * @param int $time the current time in seconds
     * @return array Array with the job id as key and the next run time as value
	 */
	private function getRunOrder($time) {
		$runOrder = array();

		foreach ($this->jobs as $jobId => $job) {
			$runOrder[$jobId] = $job->getNextRunTime($time);
		}
		asort($runOrder);

		return $runOrder;
	}

}