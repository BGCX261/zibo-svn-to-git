<?php

namespace zibo\library\message;

/**
 * Container of Message objects
 */
class MessageList implements \Iterator, \Countable {

    /**
     * Array containing Message objects
     * @var array
     */
    protected $list;

    /**
     * Pointer to the current position in the $messages array
     * @var int
     */
    protected $position = 0;

    /**
     * Constructs a new MessageList
     * @return null
     */
    public function __construct() {
        $this->list = array();
    }

    /**
     * Adds a message to the message list
     * @param zibo\library\message\Message $message the message to add
     * @return null
     */
    public function add(Message $message) {
        $this->list[] = $message;
    }

    /**
     * Merge another list into this list
     * @param zibo\library\message\MessageList $list the message list to merge
     * @return null
     */
    public function merge(MessageList $list) {
        foreach ($list as $message) {
            $this->add($message);
        }
    }

    /**
     * Checks whether this list contains messages
     * @return boolean true if this list has messages, false otherwise
     */
    public function hasMessages() {
        return !empty($this->list);
    }

    /**
     * Checks whether this list contains messages of a certain type
     * @param mixed $type the type identifier
     * @return boolean true if this list has messages of the provided type, false otherwise
     */
    public function hasType($type) {
        foreach ($this as $message) {
            if ($message->getType() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieves a list of messages of a certain type
     * @param mixed $type the type identifier
     * @return array an array of Message objects
     */
    public function getByType($type) {
        $list = array();

        foreach ($this as $message) {
            if ($message->getType() === $type) {
                $list[] = $message;
            }
        }

        return $list;
    }

    /**
     * Retrieves a list of all the messages
     * @return array an array of Message objects
     */
    public function getAll() {
        return $this->list;
    }

    /**
     * Implementation of the rewind() method of the {@link Iterator Iterator interface}
     * @return null
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Implementation of the current() method of the {@link Iterator Iterator interface}
     * @return Message a message
     */
    public function current() {
        return $this->list[$this->position];
    }

    /**
     * Implementation of the key() method of the {@link Iterator Iterator interface}
     * @return int the pointer of the current message
     */
    public function key() {
        return $this->position;
    }

    /**
     * Implementation of the next() method of the {@link Iterator Iterator interface}
     * @return null
     */
    public function next() {
        ++$this->position;
    }

    /**
     * Implementation of the valid() method of the {@link Iterator Iterator interface}
     * @return true if the current pointer is valid, false otherwise
     */
    public function valid() {
        return isset($this->list[$this->position]);
    }

    /**
     * Implementation of the count() method of the {@link Countable Countable interface}
     * @return int number of messages in this container
     */
    public function count() {
        return count($this->list);
    }

}