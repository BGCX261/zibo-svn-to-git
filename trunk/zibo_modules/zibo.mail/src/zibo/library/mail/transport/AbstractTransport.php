<?php

namespace zibo\library\mail\transport;

use zibo\core\Zibo;

/**
 * Abstract mail transport with common methods
 */
abstract class AbstractTransport implements Transport {

    /**
     * Configuration key for the line break between the headers
     * @var string
     */
    const CONFIG_HEADER_LINE_BREAK = 'mail.header.line.break';

    /**
     * Default value for the line break between the headers
     * @var string
     */
    const DEFAULT_HEADER_LINE_BREAK = "\n";

    /**
     * Name for the mail logs
     * @var string
     */
    const LOG_NAME = 'mail';

    /**
     * Character for line breaks in the headers
     * @var string
     */
    protected $lineBreak;

    /**
     * Constructs a new message transport
     * @return null
     */
    public function __construct() {
        $this->lineBreak = Zibo::getInstance()->getConfigValue(self::CONFIG_HEADER_LINE_BREAK, self::DEFAULT_HEADER_LINE_BREAK);
    }

    /**
     * Implode an array of headers with the line break character from the configuration
     * @param array $headers Array with header strings
     * @return string String with the headers
     */
    protected function implodeHeaders(array $headers) {
        return implode($this->lineBreak, $headers);
    }

    /**
     * Log the sending of a message
     * @param string $subject Subject of the message
     * @param string $headers String with the headers
     * @param boolean $result Flag to see if the message is accepted for sending
     * @return null
     */
    protected function logMail($subject, $headers, $result) {
        $title = 'Sending mail with subject \'' . $subject . '\'';
        $description = "Headers:\n" . $headers;
        $code = !$result ? 1 : 0;

        $this->log($title, $description, $code);
    }

    /**
     * Log an event in the mail log
     * @param string $title Title of the log message
     * @param string $description Description of the log message
     * @param int $code Code of the event
     * @return null
     */
    protected function log($title, $description = '', $code = 0) {
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $title, $description, $code, self::LOG_NAME);
    }

}