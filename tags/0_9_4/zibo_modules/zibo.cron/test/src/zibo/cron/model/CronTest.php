<?php

namespace zibo\cron\model;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class CronTest extends BaseTestCase {

	private $jobCalls;
	private $jobCalls2;

    public function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $configIO = new ConfigIOMock();

        $zibo = Zibo::getInstance($browser, $configIO);
        $zibo->registerEventListener(Zibo::EVENT_LOG, array($this, 'log'));
    }

	public function tearDown() {
	    Reflection::setProperty(Zibo::getInstance(), 'instance', null);
	}

	public function testRun() {
//	    $this->markTestSkipped();

		$this->jobCalls = 0;
		$this->jobCalls2 = 0;
		$loop = 8;

		$cron = new Cron();
		$cron->registerJob(array($this, 'jobCallback'));
		$cron->registerJob(array($this, 'jobCallbackWithSleep'), date('i') + 2);

		$cron->run($loop);

		$estimatedCalls = (int) floor($loop / 2);

		$this->assertEquals($estimatedCalls, $this->jobCalls);
		$this->assertEquals(1, $this->jobCalls2);
	}

    public function testRunKeepsRunningWhenJobThrowsException() {
//        $this->markTestSkipped();

        $this->jobCalls = 0;
		$loop = 8;

        $cron = new Cron();
        $cron->registerJob(array($this, 'jobCallbackWithException'));

        $cron->run($loop);

		$estimatedCalls = (int) floor($loop / 2);

		$this->assertEquals($estimatedCalls, $this->jobCalls);
    }

	public function jobCallback() {
		$this->jobCalls++;
	}

	public function jobCallbackWithSleep() {
		$this->jobCalls2++;
		sleep(5);
	}

	public function jobCallbackWithException() {
		$this->jobCalls++;
	    throw new \Exception('Faulty job');
	}

	public function log($title, $description, $level, $log) {
//		echo "\n" . date('Y-m-d H:i:s', time()) . ' - ' . $title . ($description ? ' - ' . $description : '');
	}

}