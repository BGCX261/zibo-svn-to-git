<?php

namespace zibo\install\view;

use zibo\core\View;

use zibo\library\validation\exception\ValidationException;

use \Exception;

/**
 * View to display an exception
 */
class ExceptionView implements View {

    /**
     * The exception to show
     * @var Exception
     */
    private $exception;

    /**
     * Construct this exception view
     * @param Exception $exception The exception to display as an error
     * @return null
     */
    public function __construct(Exception $exception) {
        $this->exception = $exception;
    }

    /**
     * Render the view
     * @param boolean $return true to return the rendered view, false to send it to the client
     * @return mixed null when provided $return is set to true; the rendered output when the provided $return is set to false
     */
    public function render($return = true) {
        $output = $this->getExceptionOutput($this->exception);

        if ($return) {
            return $output;
        }

        echo $output;
    }

    /**
     * Parse the exception in a structured array for easy display
     * @param Exception $exception
     * @return array Array containing the values needed to display the exception
     */
    private function getExceptionOutput(Exception $exception) {
        $message = $exception->getMessage();

        $output = get_class($exception) . (!empty($message) ? ': ' . $message : '');

        if ($exception instanceof ValidationException) {
            $output .= $exception->getErrorsAsString();
        }

        $output .= "\n" . $exception->getTraceAsString();

        $cause = $exception->getPrevious();
        if (!empty($cause)) {
            $output .= "\n\nCaused by:\n" . $this->getExceptionOutput($cause);
        }

        $output = "<pre>" . $output . '</pre>';

        return $output;
    }

}