<?php

namespace zibo\cron\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\environment\CliEnvironment;
use zibo\core\Zibo;

use zibo\cron\model\CronProcess;
use zibo\cron\model\Cron;
use zibo\cron\form\CronProcessForm;
use zibo\cron\table\CronJobTable;
use zibo\cron\view\CronView;

use zibo\ZiboException;

use \Exception;

/**
 * Controller of the cron server
 *
 * To attach jobs to the cron server, listen to EVENT_PRE_RUN event. This event will be
 * run before the server is used. The Cron object will be passed as argument. This gives
 * you the ability to add your jobs to the cron server.
 */
class CronController extends AbstractController {

    /**
     * Event which is run before the cron server starts to run
     * @var string
     */
	const EVENT_PRE_RUN = 'cron.run.pre';

	/**
	 * Translation key for the success message of the invoke action
	 * @var string
	 */
	const TRANSLATION_INVOKE_SUCCESS = 'cron.information.invoke';

	/**
	 * Translation key for the success message of the start process action
	 * @var string
	 */
	const TRANSLATION_CRON_STARTED = 'cron.information.started';

	/**
	 * Translation key for the success message of the stop process action
	 * @var string
	 */
	const TRANSLATION_CRON_STOPPED = 'cron.information.stopped';

	/**
	 * Action to run the cron server. This will keep on running until killed.
	 * @return null
	 * @throws zibo\ZiboException when this action is not run in a CLI environment
	 */
	public function indexAction() {
	    $zibo = Zibo::getInstance();

	    $environment = $zibo->getEnvironment();
        if (!($environment instanceof CliEnvironment)) {
            throw new ZiboException('Cron can only be run from the command line interface.');
        }

		$cron = new Cron();

		$zibo->runEvent(self::EVENT_PRE_RUN, $cron);

		$cron->run();
	}

	/**
     * Action to show an overview of the registered cron jobs
     * @return null
	 */
	public function overviewAction() {
	    $zibo = Zibo::getInstance();
        $cron = new Cron();

        $zibo->runEvent(self::EVENT_PRE_RUN, $cron);

        $basePath = $this->request->getBasePath();

        $cronJobTable = new CronJobTable($cron->getJobs(), $basePath);

        $formTable = $cronJobTable->getForm();
        if ($formTable->isSubmitted()) {
            $cronJob = $cronJobTable->getInvokeCronJob();

            if ($cronJob) {
                try {
                    ini_set('memory_limit', '512M');
                    ini_set('max_execution_time', '900');

                    $cronJob->run();
                    $this->addInformation(self::TRANSLATION_INVOKE_SUCCESS, array('callback' => $cronJob->getCallback()->__toString()));

                    $this->response->setRedirect($basePath);
                } catch (Exception $exception) {
                    $zibo->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
                    $this->addError(self::TRANSLATION_ERROR, array('error' => $exception->getMessage()));
                }
            }
        }

        $process = new CronProcess($zibo->getRootPath());
        $isRunning = $process->isRunning();

        $formProcess = new CronProcessForm($basePath, $isRunning);
        if ($formProcess->isSubmitted()) {
            if ($isRunning) {
                $process->stop();
                $this->addInformation(self::TRANSLATION_CRON_STOPPED);
            } else {
                $process->start();
                $this->addInformation(self::TRANSLATION_CRON_STARTED);
            }
            $this->response->setRedirect($basePath);
        }

        $view = new CronView($cronJobTable, $formProcess, $process->getCommand(), $isRunning);
        $this->response->setView($view);
	}

}