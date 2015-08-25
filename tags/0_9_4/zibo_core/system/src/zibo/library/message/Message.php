<?php

namespace zibo\library\message;

/**
 * Message data container
 */
class Message {

    /**
     * The message
     * @var string
     */
    private $message;

    /**
     * The type of the message
     * @var string
     */
    private $type;

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
     * Get the message
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set the message
     * @param string $message
     * @return null
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * Get the type of this message
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set the type of this message
     * @param string $type
     * @return null
     */
    public function setType($type) {
        $this->type = $type;
    }

}