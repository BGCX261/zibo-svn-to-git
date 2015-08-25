<?php

namespace zibo\queue\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\environment\CliEnvironment;
use zibo\core\view\JsonView;
use zibo\core\Zibo;

use zibo\library\orm\ModelManager;

use zibo\queue\model\QueueModel;
use zibo\queue\model\QueueWorker;
use zibo\queue\table\QueueTable;
use zibo\queue\view\QueueDetailView;
use zibo\queue\view\QueueView;

use zibo\ZiboException;

/**
 * Controller of the queue
 */
class QueueController extends AbstractController {

    /**
     * The name of the detail action
     * @var string
     */
    const ACTION_DETAIL = 'detail';

    /**
     * The name of the worker action
     * @var string
     */
    const ACTION_WORKER = 'worker';

    /**
     * The name of the status action
     * @var string
     */
    const ACTION_STATUS = 'status';

    /**
     * Translation key for the title
     * @var string
     */
    const TRANSLATION_TITLE = 'queue.title';

    /**
     * Translation key for the delete action
     * @var string
     */
    const TRANSLATION_DELETE = 'button.delete';

    /**
     * Translation key for the delete confirmation message
     * @var string
     */
    const TRANSLATION_DELETE_CONFIRM = 'queue.label.delete.confirm';

    /**
     * Translation key for the information message when jobs have been deleted
     * @var string
     */
    const TRANSLATION_DELETED = 'queue.message.deleted';

    /**
     * Action to show an overview of the queues
     * @return null
     */
    public function indexAction() {
        if (func_num_args()) {
            $this->setError404();
            return;
        }

        $formAction = $this->request->getBasePath();
        $detailAction = $formAction . '/' . self::ACTION_DETAIL . '/';

        $translator = $this->getTranslator();

        $table = new QueueTable($formAction, $detailAction, $translator);
        $table->addAction(
            $translator->translate(self::TRANSLATION_DELETE),
            array($this, 'deleteAction'),
            $translator->translate(self::TRANSLATION_DELETE_CONFIRM)
        );

        $table->processForm();
        if ($this->response->willRedirect()) {
            return;
        }

        $view = new QueueView($table);
        $view->setPageTitle(self::TRANSLATION_TITLE, true);

        $this->response->setView($view);
    }

    /**
     * Action to show the detail of a queue job
     * @param integer $id The id of the job
     * @return null
     */
    public function detailAction($id = null) {
        $queueModel = ModelManager::getInstance()->getModel(QueueModel::NAME);

        $data = $queueModel->findById($id);
        if (!$data) {
            $this->setError404();
            return;
        }

        $basePath = $this->request->getBasePath();
        $statusAction = $basePath . '/' . self::ACTION_STATUS . '/' . $id;

        $view = new QueueDetailView($data, $basePath, $statusAction);
        $view->setPageTitle(self::TRANSLATION_TITLE, true);

        $this->response->setView($view);
    }

    /**
     * Action to delete a job or jobs from the queue
     * @param integer|array $id Id or an array of ids of the jobs to remove
     * @return null
     */
    public function deleteAction($id) {
        $queueModel = ModelManager::getInstance()->getModel(QueueModel::NAME);
        $queueModel->delete($id);

        $this->addInformation(self::TRANSLATION_DELETED);

        $this->response->setRedirect($this->request->getBasePath());
    }

	/**
	 * Action to run a queue worker. This will keep on running until killed.
	 * @param string $name The name of the queue
	 * @param integer $sleepTime Time in seconds to sleep between jobs
	 * @return null
	 * @throws zibo\ZiboException when this action is not run in a CLI environment
	 */
	public function workerAction($name = null, $sleepTime = 3) {
	    $zibo = Zibo::getInstance();

	    $environment = $zibo->getEnvironment();
        if (!($environment instanceof CliEnvironment)) {
            throw new ZiboException('A queue worker can only be started from the command line interface.');
        }

        $worker = new QueueWorker($name, $sleepTime);
        $worker->work();
	}

	/**
	 * Action to get the status of a job
	 * @param integer $id The id of the job
	 * @return null
	 */
	public function statusAction($id = null) {
	    $queueModel = ModelManager::getInstance()->getModel(QueueModel::NAME);

	    $status = $queueModel->getJobStatus($id);
	    if (!$status) {
	        $output = array('finished' => 1);
	    } else {
	        $output = array(
	           'queue' => $status->getQueue(),
	           'progress' => $status->isInProgress(),
	           'error' => $status->isError(),
	           'errorMessage' => $status->getErrorMessage(),
	           'slot' => $status->getSlot(),
	           'slots' => $status->getTotalSlots(),
	        );
	    }

	    $view = new JsonView($output);
	    $this->response->setView($view);
	}

}