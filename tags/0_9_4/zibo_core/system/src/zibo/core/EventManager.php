<?php

namespace zibo\core;

use zibo\library\Callback;
use zibo\library\Number;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Manager of dynamic events
 */
class EventManager {

    /**
     * Default maximum number of event listeners for each event
     * @var int
     */
    const DEFAULT_MAX_EVENT_LISTENERS = 100;

    /**
     * Array with the event name as key and the callbacks to the event listeners as value (array)
     * @var array
     */
    private $events;

    /**
     * Maximum number of event listeners for each event
     * @var int
     */
    private $maxEventListeners;

    /**
     * Default weight for a new event
     * @var int
     */
    private $defaultWeight;

    /**
     * Constructs a new event manager
     * @param int $maxEventListeners Maximum number of event listeners for each event
     */
    function __construct($maxEventListeners = self::DEFAULT_MAX_EVENT_LISTENERS) {
        $this->setMaxEventListeners($maxEventListeners);
        $this->events = array();
    }

    /**
     * Sets the maximum number of event listeners for each event
     * @param int $maxEventListeners
     * @return null
     * @throw zibo\ZiboException when the provided maxEventListeners is not a positive number
     */
    private function setMaxEventListeners($maxEventListeners) {
        if (Number::isNegative($maxEventListeners) || $maxEventListeners === 0) {
            throw new ZiboException('Provided maximum of events is zero or negative');
        }

        $this->maxEventListeners = $maxEventListeners;
        $this->defaultWeight = (int) floor($maxEventListeners / 2);
    }

    /**
     * Registers a new event listener
     * @param string $event Name of the event
     * @param string|array|zibo\library\Callback $callback Callback for the event listener
     * @param int $weight Weight for the new listener in the event listener list.
     *                    This will influence the order of the event listener calls.
     *                    An event with weight 1 will be called before an event with weight 10.
     * @return null
     * @throws zibo\ZiboException when the name of the event is empty or invalid
     * @throws zibo\ZiboException when the weight of the event listener is invalid or already set
     */
    public function registerEventListener($event, $callback, $weight = null) {
        $this->checkEventName($event);

        if (!array_key_exists($event, $this->events)) {
            $this->events[$event] = array();
        }

        if ($weight === null) {
            $weight = $this->getNewWeight($event);
        } elseif (Number::isNegative($weight) || $weight >= $this->maxEventListeners) {
            throw new ZiboException('Provided weight ' . $weight . ' is invalid. Try a value between 0 and ' . $this->maxEventListeners);
        }
        if (array_key_exists($weight, $this->events[$event])) {
            throw new ZiboException('Weight ' . $weight . ' for event ' . $event . ' is already set with callback ' . $this->events[$event][$weight]);
        }

        $this->events[$event][$weight] = new Callback($callback);
        ksort($this->events[$event]);
    }

    /**
     * Clears the event listeners for the provided event
     * @param string $event Name of the event
     * @return null
     * @throws zibo\ZiboException when the name of the event is empty or invalid
     */
    public function clearEventListeners($event) {
        $this->checkEventName($event);

        if (array_key_exists($event, $this->events)) {
            unset($this->events[$event]);
        }
    }

    /**
     * Gets the new weight for the provided event
     * @param string $event Name of the event
     * @return int The weight for a new event listener
     * @throws zibo\ZiboException when no weight could be found for the provided event
     */
    private function getNewWeight($event) {
        $weight = $this->defaultWeight;

        do {
            if (!array_key_exists($weight, $this->events[$event])) {
                return $weight;
            }

            $weight++;
        } while ($weight < $this->maxEventListeners);

        throw new ZiboException('No new weight found for event ' . $event . '. Tried from ' . $this->defaultWeight . ' to ' . ($this->maxEventListeners - 1));
    }

    /**
     * Runs the listeners of the provided event. All arguments passed after the event name are passed through to the event listener.
     * @param string $event Name of the event
     * @return null
     * @throws zibo\ZiboException when the provided event is empty or invalid
     */
    public function runEvent($event) {
        if (!$this->checkEvent($event)) {
            return;
        }

        $arguments = func_get_args();
        unset($arguments[0]);

        $this->invokeEvent($event, $arguments);
    }

    /**
     * Runs the listeners of the provided event with the provided arguments.
     * @param string $event Name of the event
     * @param array $arguments Array with the arguments for the event listener callbacks
     * @return null
     * @throws zibo\ZiboException when the provided event is empty or invalid
     */
    public function runEventWithArrayArguments($event, array $arguments) {
        if (!$this->checkEvent($event)) {
            return;
        }

        $this->invokeEvent($event, $arguments);
    }

    /**
     * Invokes the event listeners for the provided event
     * @param string $event Name of the event
     * @param array $arguments Array with the arguments for the event listeners
     * @return null
     */
    private function invokeEvent($event, array $arguments) {
        foreach ($this->events[$event] as $callback) {
            $callback->invokeWithArrayArguments($arguments);
        }
    }

    /**
     * Checks if the provided event is invokable.
     * @param string $event Name of the event
     * @return boolean True if the event has listeners registered, false otherwise
     * @throws zibo\ZiboException when the provided event is empty or invalid
     */
    private function checkEvent($event) {
        $this->checkEventName($event);

        if (empty($this->events[$event])) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the provided event name is valid
     * @param string $name Name of a event
     * @return null
     * @throws zibo\ZiboException when the provided event is empty or invalid
     */
    private function checkEventName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name of the event is empty');
        }
    }

}