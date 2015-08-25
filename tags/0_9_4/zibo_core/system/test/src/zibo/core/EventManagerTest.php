<?php

namespace zibo\core;

use zibo\library\Callback;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class EventManagerTest extends BaseTestCase {

    /**
     * @var EventManager
     */
    private $eventManager;

    private $executed;

    protected function setUp() {
        $this->eventManager = new EventManager();
    }

    public function testConstructWithMaxEvents() {
        $maxEventListeners = 10;
        $eventManager = new EventManager($maxEventListeners);
        $this->assertEquals($maxEventListeners, Reflection::getProperty($eventManager, 'maxEventListeners'));
        $this->assertEquals(EventManager::DEFAULT_MAX_EVENT_LISTENERS, Reflection::getProperty($this->eventManager, 'maxEventListeners'));
    }

    /**
     * @dataProvider providerConstructWithInvalidMaxEventsThrowsException
     * @expectedException zibo\ZiboException
     */
    public function testConstructWithInvalidMaxEventsThrowsException($maxEvents) {
        new EventManager($maxEvents);
    }

    public function providerConstructWithInvalidMaxEventsThrowsException() {
        return array(
            array(0),
            array(-15),
            array('test'),
            array($this),
        );
    }

    public function testRegisterEvent() {
        $event = 'event';
        $callback = array($this, 'testRegisterEvent');

        $this->eventManager->registerEventListener($event, $callback);

        $events = Reflection::getProperty($this->eventManager, 'events');

        $this->assertTrue(in_array(new Callback($callback), $events[$event]));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterEventWithEmptyNameThrowsException() {
        $this->eventManager->registerEventListener('', array('instance', 'method'));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterEventWithExistingWeightThrowsException() {
        $event = 'event';
        $callback = array('instance', 'method');

        $this->eventManager->registerEventListener($event, $callback, 20);
        $this->eventManager->registerEventListener($event, $callback, 20);
    }

    /**
     * @dataProvider providerRegisterEventInvalidWeightThrowsException
     * @expectedException zibo\ZiboException
     */
    public function testRegisterEventInvalidWeightThrowsException($weight) {
        $this->eventManager->registerEventListener('event', array('instance', 'method'), $weight);
    }

    public function providerRegisterEventInvalidWeightThrowsException() {
        return array(
            array('test'),
            array($this),
            array(70000),
        );
    }

    public function testPerformEventWithNoCallbacks() {
        $this->eventManager->runEvent('test');
    }

    public function testClearEventListeners() {
        $event = 'event';
        $callback = array($this, 'testClearEventListeners');

        $this->eventManager->registerEventListener($event, $callback);

        $events = Reflection::getProperty($this->eventManager, 'events');

        $this->assertTrue(in_array(new Callback($callback), $events[$event]));

        $this->eventManager->clearEventListeners($event);

        $events = Reflection::getProperty($this->eventManager, 'events');

        $this->assertFalse(array_key_exists($event, $events));
    }

    public function testRunEventWithEvents() {
        $event = 'event';
        $this->executed = false;
        $callback = array($this, 'eventCallbackMethod');

        $this->eventManager->registerEventListener($event, $callback);
        $this->eventManager->runEvent($event);

        $this->assertTrue($this->executed, 'TestEvent has not been called');
    }

    public function testRunEventWithEventsAndArgument() {
        $event = 'event';
        $this->executed = 0;
        $callback = array($this, 'eventCallbackMethodSum');

        $this->eventManager->registerEventListener($event, $callback);
        $this->eventManager->runEvent($event, 1);
        $this->eventManager->runEvent($event, 2);

        $this->assertEquals(3, $this->executed);
    }

    public function testRunEventWithWeights() {
        $event = 'event';
        $this->executed = 10;
        $callback = array($this, 'eventCallbackMethod');
        $callback2 = array($this, 'eventCallbackMethodSum');
        $callback3 = array($this, 'eventCallbackMethodMultiply');
        $callback4 = array($this, 'eventCallbackMethodSubstract');

        $this->eventManager->registerEventListener($event, $callback3);
        $this->eventManager->registerEventListener($event, $callback, 20);
        $this->eventManager->registerEventListener($event, $callback4, 99);
        $this->eventManager->registerEventListener($event, $callback2, 10);
        $this->eventManager->runEvent($event, 7);

        $this->assertEquals(42, $this->executed);
    }

    /**
     * @dataProvider providerRunEventWithInvalidEventThrowsException
     * @expectedException zibo\ZiboException
     */
    public function testRunEventWithInvalidEventThrowsException($event) {
        $this->eventManager->runEvent($event);
    }

    public function providerRunEventWithInvalidEventThrowsException() {
        return array(
            array(''),
            array($this),
        );
    }

    public function eventCallbackMethod($value = true) {
       $this->executed = $value;
    }

    public function eventCallbackMethodSum($value) {
       $this->executed += $value;
    }

    public function eventCallbackMethodSubstract($value) {
       $this->executed -= $value;
    }

    public function eventCallbackMethodMultiply($value) {
       $this->executed *= $value;
    }

}