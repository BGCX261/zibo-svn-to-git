<?php

namespace zibo\queue\model;

/**
 * Data container for the status of a job in it's queue
 */
class QueueJobStatus {

    /**
     * The name of the queue
     * @var string
     */
    private $queue;

    /**
     * The slot of the job in the queue
     * @var integer
     */
    private $slot;

    /**
     * The total number of slots in the queue
     * @var integer
     */
    private $slots;

    /**
     * Flag to see if the job is in progress
     * @var boolean
     */
    private $isInProgress;

    /**
     * Flag to see if the job generated an error
     * @var boolean
     */
    private $isError;

    /**
     * The error message
     * @var string
     */
    private $error;

    /**
     * Constructs a new queue job status
     * @param string $queue The name of the queue
     * @param integer $slot The slot of the job in the queue
     * @param integer $slots The total number of slots in the queue
     * @param boolean $isInProgress Flag to see if the job is in progress
     * @param boolean $isError Flag to see if the job generated an error
     * @param string $error The error message
     * @return null
     */
    public function __construct($queue, $slot, $slots, $isInProgress, $isError, $error = null) {
        $this->queue = $queue;
        $this->slot = $slot;
        $this->slots = $slots;
        $this->isInProgress = $isInProgress;
        $this->isError = $isError;
        $this->error = $error;
    }

    /**
     * Gets the name of the queue
     * @return string
     */
    public function getQueue() {
        return $this->queue;
    }

    /**
     * Gets the slot of the job in the queue
     * @return integer
     */
    public function getSlot() {
        return $this->slot;
    }

    /**
     * Gets the total number of slots in the queue
     * @return integer
     */
    public function getTotalSlots() {
        return $this->slots;
    }

    /**
     * Checks if the job is in progress
     * @return boolean
     */
    public function isInProgress() {
        return $this->isInProgress;
    }

    /**
     * Checks if the job generated an error
     * @return boolean
     */
    public function isError() {
        return $this->isError;
    }

    /**
     * Gets the error message for this job
     * @return string|null
     */
    public function getError() {
        return $this->error;
    }

}