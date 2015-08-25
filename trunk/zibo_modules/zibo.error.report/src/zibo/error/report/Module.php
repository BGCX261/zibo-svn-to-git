<?php

namespace zibo\error\report;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\security\exception\UnauthorizedException;
use zibo\library\security\SecurityManager;
use zibo\library\Session;

use \Exception;

/**
 * Module to log and report an occured error
 */
class Module {

    /**
     * Configuration key for the recipient of the error mail
     * @var string
     */
    const CONFIG_MAIL_RECIPIENT = 'error.report.recipient';

    /**
     * Configuration key for the subject of the error mail
     * @var string
     */
    const CONFIG_MAIL_SUBJECT = 'error.report.subject';

    /**
     * Name of the log under which the errors will be logged
     * @var string
     */
    const LOG_NAME = 'error';

    /**
     * Session key for the error report
     * @var string
     */
    const SESSION_REPORT = 'error.report';

    /**
     * Initialize the error report module for a request
     * @return null
     */
    public function initialize() {
        $callback = array($this, 'handleException');
        Zibo::getInstance()->registerEventListener(Zibo::EVENT_ERROR, $callback, 75);
    }

    /**
     * Handle a exception, redirect to the error report form
     * @param Exception $exception
     * @return null
     */
    public function handleException(Exception $exception) {
        if ($exception instanceof UnauthorizedException) {
            return;
        }

        $zibo = Zibo::getInstance();

        $request = $zibo->getRequest();
        $title = $this->getTitle($exception);
        $report = $this->getReport($exception, $request);

        $zibo->runEvent(Zibo::EVENT_LOG, $title, $report, 1, self::LOG_NAME);

        if (!$zibo->getConfigValue(self::CONFIG_MAIL_RECIPIENT)) {
            return;
        }

        Session::getInstance()->set(self::SESSION_REPORT, $title . "\n" . $report);

        $response = Zibo::getInstance()->getResponse();
        $response->setView(null);
        $response->setRedirect($request->getBaseUrl() . '/report/error');
    }

    /**
     * Get the title of the exception
     * @param Exception $exception
     * @return string
     */
    private function getTitle(Exception $exception) {
        $message = $exception->getMessage();
        if (!$message) {
            $message = 'Unknown error occured';
        }
        $class = get_class($exception);

        return $message . ' (' . $class . ')';
    }

    /**
     * Get a error report of an exception
     * @param Exception $exception
     * @param zibo\core\Request $request
     * @return string
     */
    private function getReport(Exception $exception, Request $request = null) {
        $report = 'Date: ' . date('d/m/Y H:i:s', time()) . "\n";

        if ($request) {
            $url = $request->getBasePath() . '/';
            $url .= implode('/', $request->getParameters());
            $report .= 'Request: ' . $url . "\n";
        }

        $user = SecurityManager::getInstance()->getUser();
        if ($user) {
            $report .= 'User: ' . $user->getUsername();
        } else {
            $report .= 'User: anonymous';
        }

        $report .= "\n\nTrace:\n" . $this->getTrace($exception);

        return $report;
    }

    /**
     * Get the trace of a exception and it's causes
     * @param Exception $exception
     * @return string trace of the exception
     */
    private function getTrace(Exception $exception) {
        $trace = $exception->getTraceAsString();

        $previous = $exception->getPrevious();
        if ($previous) {
            $trace .= "\nCaused by:\n";
            $trace .= $this->getTitle($previous) . "\n";
            $trace .= $this->getTrace($previous);
        }

        return $trace;
    }

}