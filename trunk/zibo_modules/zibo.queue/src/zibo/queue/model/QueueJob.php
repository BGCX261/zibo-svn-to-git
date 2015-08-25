<?php

namespace zibo\queue\model;

/**
 * Interface for a job
 */
interface QueueJob {

    /**
     * Gets the name of the queue
     * @return string
     */
    public function getQueue();

    /**
     * Invokes the implementation of the job
     * @return integer|null A timestamp from which time this job should be invoked again or null when the job is done
     */
    public function run();

}