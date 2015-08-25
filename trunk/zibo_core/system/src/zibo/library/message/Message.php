<?php

namespace zibo\library\message;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Message data container
 */
class Message {

    /**
     * The message
     * @var string
     */
    protected $message;

    /**
     * The type of the message
     * @var string
     */
    protected $type;

    /**
     * Construct a new message
     * @param string $message the message
     * @param string $type type of the message
     * @return null
     */
    public function __construct($message, $type = null) {
        $this->setMessage($message);
        $this->setType($type);
    }

    /**
     * Sets the message
     * @param string $message
     * @return null
     * @throws zibo\ZiboException when the provided message is empty or invalid
     */
    public function setMessage($message) {
        if (!String::isString($message, String::NOT_EMPTY)) {
            throw new ZiboException('The provided message is invalid or empty');
        }

        $this->message = $message;
    }

    /**
     * Gets the message
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set the type of this message
     * @param string $type
     * @return null
     */
    public function setType($type = null) {
        if ($type !== null && !String::isString($type, String::NOT_EMPTY)) {
            throw new ZiboException('The provided type is invalid or empty');
        }

        $this->type = $type;
    }

    /**
     * Get the type of this message
     * @return string
     */
    public function getType() {
        return $this->type;
    }

}