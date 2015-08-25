<?php

namespace zibo\library\xml\exception;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Exception for the errors of PHP's internal XML library
 */
class LibXmlException extends ZiboException {

    /**
     * Construct this exception
     * @param string $message
     * @param string $code
     * @param array $errors an array with LibXMLError objects
     * @param string $source source of the document or file name to the source
     * @return null
     */
    public function __construct($message = null, $code = null, array $errors = array(), $source = null) {

        if (count($errors) > 0) {
            $message .= str_repeat(PHP_EOL, 3) . 'Errors reported by libxml:' . str_repeat(PHP_EOL, 2);
            foreach ($errors as $error) {
                $message .= PHP_EOL;
                $message .= str_pad($this->getErrorLevelName($error->level), 10, ' ', STR_PAD_RIGHT);
                $message .= str_pad($error->code,     8, ' ', STR_PAD_RIGHT);
                $message .= str_pad($error->file,    60, ' ', STR_PAD_RIGHT);
                $message .= str_pad('line ' . $error->line,    20, ' ', STR_PAD_RIGHT);
                $message .= str_pad('column ' . $error->column,  20, ' ', STR_PAD_RIGHT);
                $message .= $error->message;
            }
        }

        $message = '<br /><br /><pre>' . $message . '</pre><br /><br />';

        if ($source) {
            $message .= '<br /><br /><pre>Source: ' . PHP_EOL . String::addLineNumbers($source) . '</pre><br /><br />';
        }

        parent::__construct($message, $code);
    }

    /**
     * Get the name of the error level
     * @param int $level level of a LibXMLError
     * @return string name of the error level
     */
    private function getErrorLevelName($level) {
        switch ($level) {
            case LIBXML_ERR_WARNING:
                return 'warning';
            case LIBXML_ERR_ERROR:
                return 'error';
            case LIBXML_ERR_FATAL:
                return 'fatal error';
        }
    }

}