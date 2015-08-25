<?php

namespace zibo\queue\model;

use zibo\core\Zibo;

use zibo\library\orm\ModelManager;
use zibo\library\Number;
use zibo\library\String;

use zibo\queue\model\data\QueueData;

use zibo\ZiboException;

use \Exception;

/**
 * A worker for a queue.
 */
class QueueWorker {

    /**
     * Name for the log messages
     * @var string
     */
    const LOG_NAME = 'queue';

    /**
     * The name of this queue
     * @var string
     */
    private $name;

    /**
     * The sleep time in seconds before fetching the next job
     * @var integer
     */
    private $sleepTime;

    /**
     * The model of the queue
     * @var QueueModel
     */
    private $model;

    /**
     * Constructs a new queue worker
     * @param string $name The name of the queue
     * @param integer $sleepTime The time in seconds before fetching the next job
     * @return null
     */
    public function __construct($name, $sleepTime = 3) {
        $this->setName($name);
        $this->setSleepTime($sleepTime);
    }

    /**
     * Sets the name of the queue
     * @param string $name The name of the queue
     * @return null
     */
    protected function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Could not set the name of the queue worker: provided name is empty');
        }

        $this->name = $name;
    }

    /**
     * Gets the name of the queue
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the time in seconds before fetching and invoking the job
     * @param integer $sleepTime Time in seconds
     * @return null
     */
    public function setSleepTime($sleepTime) {
        if (Number::isNegative($sleepTime)) {
            throw new ZiboException('Could not set the sleep time of the queue worker: invalid sleep time provided');
        }

        $this->sleepTime = $sleepTime;
    }

    /**
     * Gets the time in seconds before fetching and invoking the next job
     * @return integer
     */
    public function getSleepTime() {
        return $this->sleepTime;
    }

    /**
     * Continiously checks the queue and invokes the scheduled jobs
     * @return null
     */
    public function work() {
        $this->model = ModelManager::getInstance()->getModel(QueueModel::NAME);

        do {
            $data = $this->model->popJobFromQueue($this->name);
            if ($data) {
                echo $this->name . ': Invoking job #' . $data->id . ' ... ';
                $dateReschedule = $this->invokeJob($data);

                if ($dateReschedule == -1) {
                    echo "error\n";
                } else {
                    echo "done\n";

                    if (is_numeric($dateReschedule) && $dateReschedule > time()) {
                        echo $this->name . ': Rescheduling job #' . $data->id . ' from ' . date('Y-m-d H:i:s', $dateReschedule) . "\n";
                        $queueModel->pushJobToQueue($data->job, $dateReschedule);
                    }
                }
            } elseif (!$this->sleepTime) {
                echo $this->name . ": Nothing to be done and no sleep time. Exiting ...\n";
                break;
            }

            if ($this->sleepTime) {
                echo $this->name . ': Sleeping ' . $this->sleepTime . " second(s)...\n";
                sleep($this->sleepTime);
            }
        } while (true);
    }

    /**
     * Invokes a queue job
     * @param zibo\queue\model\data\QueueData $data The data of the job to invoke
     * @return null|integer Null when the job is finished, a timestamp to reschedule the job
     */
    protected function invokeJob(QueueData $data) {
        try {
            $result = $data->job->run();

            $this->model->delete($data);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $message = get_class($exception) . (!empty($message) ? ': ' . $message : '');
            $trace = $exception->getTraceAsString();

            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $message, $trace, 1, self::LOG_NAME);

            $data->isInProgress = false;
            $data->isError = true;
            $data->error = $message . "\n\nTrace:\n" . $trace;

            $this->model->save($data);

            $result = null;
        }

        return $result;
    }

}