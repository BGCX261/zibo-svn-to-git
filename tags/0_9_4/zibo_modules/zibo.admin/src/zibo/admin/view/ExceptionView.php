<?php

namespace zibo\admin\view;

use zibo\library\validation\exception\ValidationException;

use \Exception;

/**
 * View to display an exception
 */
class ExceptionView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/exception/index';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_EXCEPTION = 'web/styles/admin/exception.css';

    /**
     * Construct this exception view
     * @param Exception $e the exception to display as an error
     * @return null
     */
    public function __construct(Exception $e) {
        parent::__construct(self::TEMPLATE);

        $this->set('exception', $this->getExceptionArray($e));

        $this->addStyle(self::STYLE_EXCEPTION);
    }

    /**
     * Parse the exception in a structured array for easy display
     * @param Exception $exception
     * @return array Array containing the values needed to display the exception
     */
    private function getExceptionArray(Exception $exception) {
        $message = $exception->getMessage();

        $array = array();
        $array['message'] = get_class($exception) . (!empty($message) ? ': ' . $message : '');
        $array['trace'] = $exception->getTraceAsString();
        $array['cause'] = null;

        if ($exception instanceof ValidationException) {
            $array['message'] .= $exception->getErrorsAsString();
        }

        $cause = $exception->getPrevious();
        if (!empty($cause)) {
            $array['cause'] = $this->getExceptionArray($cause);
        }

        return $array;
    }

}