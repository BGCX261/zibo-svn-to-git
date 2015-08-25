<?php

namespace zibo\queue\model;

use zibo\library\orm\model\ExtendedModel;

/**
 * The model for the queue
 */
class QueueModel extends ExtendedModel {

    /**
     * The name of the model
     * @var string
     */
    const NAME = 'Queue';

    /**
     * Pushes a job to the queue
     * @param QueueJob $job The job to add
     * @param integer $dateScheduled Timestamp from which the invokation is possible (optional)
     * @return integer The id of the job in the queue to track it's progress
     * @see getJobStatus
     */
    public function pushJobToQueue(QueueJob $job, $dateScheduled = null) {
        $job = clone $job;

        $data = $this->createData();

        $data->queue = $job->getQueue();
        $data->isInProgress = false;
        $data->isError = false;
        $data->data = serialize($job);
        $data->dateScheduled = $dateScheduled;

        $this->save($data);

        return $data->id;
    }

    /**
     * Pops a job from the queue (FIFO) and marks it as in progress
     * @param string $queue The name of the queue
     * @return zibo\queue\model\data\QueueData|null The first job in the provided queue or null if the queue is empty
     */
    public function popJobFromQueue($queue) {
        $query = $this->createQuery();
        $query->addCondition('{queue} = %1% AND ({dateScheduled} IS NULL OR {dateScheduled} <= %2%) AND {isInProgress} = 0 AND {isError} = 0', $queue, time());
        $query->addOrderBy('{id} ASC');

        $data = $query->queryFirst();
        if (!$data) {
            return null;
        }

        $data->isInProgress = true;
        $this->save($data, 'isInProgress');

        $data->job = unserialize($data->data);

        return $data;
    }

    /**
     * Gets the status of a job in the queue
     * @param integer $id The id of the job in the queue
     * @return null|QueueJobStatus Null if the job is finished, a job status instance otherwise
     */
    public function getJobStatus($id) {
        $data = $this->findById($id);
        if (!$data) {
            return null;
        }

        $query = $this->createQuery();
        $query->addCondition('{queue} = %1% AND ({dateScheduled} IS NULL OR {dateScheduled} <= %2%) AND {isError} = 0', $data->queue, time());
        $slots = $query->count();

        $query->addCondition('{id} < %1% AND {isInProgress} = 0', $data->id);
        $slot = $query->count() + 1;

        return new QueueJobStatus($data->queue, $slot, $slots, $data->isInProgress, $data->isError, $data->error);
    }

}