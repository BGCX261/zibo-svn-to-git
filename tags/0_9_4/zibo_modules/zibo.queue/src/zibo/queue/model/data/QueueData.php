<?php

namespace zibo\queue\model\data;

use zibo\library\orm\model\data\Data;

use \Exception;

/**
 * Data container for the data in the queue
 */
class QueueData extends Data {

    /**
     * Style class for a waiting job
     * @var string
     */
    const STYLE_WAITING = 'job-waiting';

    /**
     * Style class for a job in progress
     * @var string
     */
    const STYLE_PROGRESS = 'job-progress';

    /**
     * Style class for a job which ran into an error
     * @var string
     */
    const STYLE_ERROR = 'job-error';

    /**
     * The name of the queue
     * @var string
     */
    public $queue;

    /**
     * Serialized queue job
     * @var string
     */
    public $data;

    /**
     * Flag to see if the job is in progress
     * @var boolean
     */
    public $isInProgress;

    /**
     * Flag to see if the job generated an error
     * @var boolean
     */
    public $isError;

    /**
     * The error message of the job
     * @var string
     */
    public $error;

    /**
     * Timestamp for rescheduling
     * @var integer
     */
    public $dateScheduled;

    /**
     * Unserialized queue job
     * @var zibo\queue\model\QueueJob
     */
    public $job;

    /**
     * Gets the class name of the job
     * @return string
     */
    public function getJobClassName() {
        if (!$this->job) {
            $this->job = unserialize($this->data);
        }

        if (is_object($this->job)) {
            return get_class($this->job);
        }

        return '---';
    }

    /**
     * Gets the style class for the job's status
     * @return string
     */
    public function getStatusClass() {
        $class = self::STYLE_WAITING;
        if ($this->isError) {
            $class = self::STYLE_ERROR;
        } elseif ($this->isInProgress) {
            $class = self::STYLE_PROGRESS;
        }

        return $class;
    }

}