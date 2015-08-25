<?php

namespace zibo\cron\model;

use zibo\library\Callback;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class SuperCronJobTest extends BaseTestCase {

	public function testConstruct() {
		$callback = array($this, 'testConstruct');
		$second = 1;
		$minute = 2;
		$hour = 3;
		$day = 4;
		$month = 5;
		$dayOfWeek = 6;

		$job = new SuperCronJob($callback, $second, $minute, $hour, $day, $month, $dayOfWeek);
		$this->assertEquals(new Callback($callback), Reflection::getProperty($job, 'callback'));
		$this->assertEquals(array($second => $second), Reflection::getProperty($job, 'second'));
		$this->assertEquals(array($minute => $minute), Reflection::getProperty($job, 'minute'));
		$this->assertEquals(array($hour => $hour), Reflection::getProperty($job, 'hour'));
		$this->assertEquals(array($day => $day), Reflection::getProperty($job, 'day'));
		$this->assertEquals(array($month => $month), Reflection::getProperty($job, 'month'));
		$this->assertEquals(array($dayOfWeek => $dayOfWeek), Reflection::getProperty($job, 'dayOfWeek'));
	}

	public function testConstructThrowsExceptionWhenEmptyCallbackProvided() {
		try {
			new SuperCronJob(array());
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	/**
     * @dataProvider providerConstructWithListAndRanges
	 */
	public function testConstructWithListAndRanges($expected, $second) {
        $job = new SuperCronJob(array($this, 'testConstruct'), $second);
        $result = Reflection::getProperty($job, 'second');
        $this->assertEquals($expected, $result);
	}

	public function providerConstructWithListAndRanges() {
		return array(
            array(SuperCronJob::ASTERIX, null),
            array(SuperCronJob::ASTERIX, SuperCronJob::ASTERIX),
            array(array(2 => 2, 3 => 3, 4 => 4), '2-4'),
            array(array(2 => 2, 4 => 4), '2,4'),
            array(array(2 => 2, 4 => 4, 5 => 5, 6 => 6, 7 => 7), '2,4-7'),
		);
	}

	/**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidSecondProvided
	 */
	public function testConstructThrowsExceptionWhenInvalidSecondProvided($second) {
		try {
			new SuperCronJob(array($this, 'testConstruct'), $second);
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	public function providerConstructThrowsExceptionWhenInvalidSecondProvided() {
		return array(
            array(-1),
            array(1230),
            array('test'),
            array('5-2'),
            array(new SuperCronJob('strpos')),
		);
	}

	public function testGetId() {
		$callback = 'strpos';
		$second = 5;
		$minute = 5;
		$hour = '*';
		$day = '10-15';
		$month = 3;
		$dayOfWeek = null;

		$job = new SuperCronJob($callback, $second, $minute, $hour, $day, $month, $dayOfWeek);

		$id = serialize($callback);
		$id .= serialize(array($second => $second));
		$id .= serialize(array($minute => $minute));
		$id .= serialize(SuperCronJob::ASTERIX);
		$id .= serialize(array(10 => 10, 11 => 11, 12 => 12, 13 => 13, 14 => 14, 15 => 15));
		$id .= serialize(array($month => $month));
		$id .= serialize(SuperCronJob::ASTERIX);
		$id = md5($id);

		$this->assertEquals($id, $job->getId());
	}

	/**
     * @dataProvider providerGetNextRunTime
	 */
	public function testGetNextRunTime($expected, $time, $second, $minute, $hour, $day, $month, $dayOfWeek) {
		$job = new SuperCronJob('strpos', $second, $minute, $hour, $day, $month, $dayOfWeek);
		$result = $job->getNextRunTime($time);
		try {
		  $this->assertEquals($expected, $result);
		} catch (\Exception $e) {
		    echo "\n" . $job->__toString();
		    echo "\n" . 'now: ' . date('Y-m-d H:i:s', $time) . "\n";
		    echo 'expected = ' . date('Y-m-d H:i:s', $expected) . "\n";
		    echo 'got = ' . date('Y-m-d H:i:s', $result) . "\n";
		    throw  $e;
		}
	}

	public function providerGetNextRunTime() {
		return array(
            array(mktime(1, 0, 1, 1, 1, 1970), mktime(1, 0, 0, 1, 1, 1970), '*', '*', '*', '*', '*', '*'), // now:  1970-01-01 01:00:00 next: 1970-01-01 01:00:01
            array(mktime(1, 1, 1, 1, 1, 1970), mktime(1, 0, 1, 1, 1, 1970), '1', '*', '*', '*', '*', '*'), // now:  1970-01-01 01:00:01 next: 1970-01-01 01:01:01
            array(mktime(1, 1, 1, 1, 1, 1970), mktime(1, 0, 15, 1, 1, 1970), '1', '*', '*', '*', '*', '*'), // now:  1970-01-01 01:00:15 next: 1970-01-01 01:01:01
            array(mktime(1, 1, 30, 1, 1, 1970), mktime(1, 0, 20, 1, 1, 1970), '30', '1', '*', '*', '*', '*'), // now:  1970-01-01 01:00:20 next: 1970-01-01 01:01:30
            array(mktime(2, 30, 0, 1, 1, 1970), mktime(1, 45, 0, 1, 1, 1970), '*', '30', '*', '*', '*', '*'), // now:  1970-01-01 01:45:00 next: 1970-01-01 02:30:00
            array(mktime(2, 30, 20, 1, 1, 1970), mktime(1, 30, 30, 1, 1, 1970), '20', '30', '*', '*', '*', '*'), // now:  1970-01-01 01:30:30 next: 1970-01-01 02:30:20
            array(mktime(1, 5, 1, 1, 1, 1970), mktime(1, 5, 0, 1, 1, 1970), '*', '*', '1', '*', '*', '*'), // now:  1970-01-01 01:05:00 next: 1970-01-01 01:05:01
            array(mktime(2, 0, 0, 1, 1, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '*', '2', '*', '*', '*'), // now:  1970-01-01 01:00 next: 1970-01-01 02:00
            array(mktime(2, 0, 0, 1, 1, 1970), mktime(1, 5, 0, 1, 1, 1970), '0', '*', '2', '*', '*', '*'), // now:  1970-01-01 01:05 next: 1970-01-01 02:00
            array(mktime(2, 0, 0, 1, 2, 1970), mktime(2, 59, 0, 1, 1, 1970), '0', '*', '2', '*', '*', '*'), // now:  1970-01-01 01:59 next: 1970-01-01 02:00
            array(mktime(2, 30, 0, 1, 1, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '30', '2', '*', '*', '*'), // now:  1970-01-01 01:00 next: 1970-01-01 02:30
            array(mktime(2, 30, 0, 1, 2, 1970), mktime(3, 0, 0, 1, 1, 1970), '0', '30', '2', '*', '*', '*'), // now:  1970-01-01 03:00 next: 1970-01-02 02:30
            array(mktime(1, 1, 0, 1, 1, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '*', '*', '1', '*', '*'), // now:  1970-01-01 01:00 next: 1970-02-01 00:00
            array(mktime(1, 0, 0, 2, 1, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '0', '1', '1', '*', '*'), // now:  1970-01-01 01:00 next: 1970-02-01 00:00
            array(mktime(0, 30, 0, 1, 3, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '30', '*', '3', '*', '*'), // now:  1970-01-01 01:00 next: 1970-01-03 00:30
            array(mktime(3, 30, 0, 1, 3, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '30', '3', '3', '*', '*'), // now:  1970-01-01 01:00 next: 1970-01-03 03:30
            array(mktime(3, 0, 0, 1, 3, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '0,30', '3', '3', '*', '*'), // now:  1970-01-01 01:00 next: 1970-01-03 03:00
            array(mktime(0, 0, 0, 3, 1, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '*', '*', '*', '3', '*'), // now:  1970-01-01 01:00 next: 1970-03-01 00:00
            array(mktime(18, 15, 0, 3, 15, 1970), mktime(7, 0, 0, 3, 15, 1970), '0', '15,45', '6,18', '1,15', '3,6,9,12', '*'), // now:  1970-03-15 07:00 next: 1970-03-15 18:15
            array(mktime(18, 45, 0, 3, 15, 1970), mktime(18, 15, 0, 3, 15, 1970), '0', '15,45', '6,18', '1,15', '3,6,9,12', '*'), // now:  1970-03-15 18:15 next: 1970-03-15 18:45
            array(mktime(6, 15, 0, 6, 1, 1970), mktime(18, 45, 0, 3, 15, 1970), '0', '15,45', '6,18', '1,15', '3,6,9,12', '*'), // now:  1970-03-15 18:45 next: 1970-06-01 06:15
            array(mktime(1, 0, 0, 1, 5, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '*', '1', '*', '*', '1'), // now:  1970-01-01 01:00 next: 1970-01-05 01:00
            array(mktime(1, 0, 0, 1, 2, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '*', '1', '*', '*', '1,3,5'), // now:  1970-01-01 01:00 next: 1970-01-02 01:00
            array(mktime(0, 0, 0, 1, 3, 1970), mktime(1, 0, 0, 1, 1, 1970), '0', '*', '0', '*', '*', '1,6'), // now:  1970-01-01 01:00 next: 1970-01-03 00:00
            array(mktime(0, 0, 0, 3, 1, 1970), mktime(1, 0, 0, 2, 28, 1970), '0', '*', '0', '*', '*', '*'), // now:  1970-02-28 01:00 next: 1970-03-01 00:00
            array(mktime(0, 0, 0, 1, 1, 1971), mktime(23, 0, 0, 12, 31, 1970), '0', '*', '0', '*', '*', '*'), // now:  1970-02-28 01:00 next: 1970-03-01 00:00
            array(mktime(13, 55, 0, 10, 12, 2009), mktime(12, 55, 5, 10, 12, 2009), '0', '55', '*', '*', '*', '*'), // now:  2009-10-12 12:55:05 next: 2009-10-12 13:55:05
            array(mktime(3, 30, 0, 10, 19, 2009), mktime(3, 32, 55, 10, 12, 2009), '0', '30', '3', '*', '*', '1'), // now:  2009-10-12 3:32:55 next: 2009-10-19 3:30:00
            array(mktime(3, 35, 0, 10, 12, 2009), mktime(3, 32, 55, 10, 12, 2009), '0', '*/5', '3', '*', '*', '*'), // now:  2009-10-12 3:32:55 next: 2009-10-12 3:35:00
            array(mktime(3, 35, 0, 10, 12, 2009), mktime(3, 32, 55, 10, 12, 2009), '0', '/5', '3', '*', '*', '*'), // now:  2009-10-12 3:32:55 next: 2009-10-12 3:35:00
            array(mktime(3, 40, 0, 10, 12, 2009), mktime(3, 32, 55, 10, 12, 2009), '0', '40/5', '3', '*', '*', '*'), // now:  2009-10-12 3:32:55 next: 2009-10-12 3:35:00
            array(mktime(3, 50, 0, 10, 12, 2009), mktime(3, 32, 55, 10, 12, 2009), '0', '10/20', '3', '*', '*', '*'), // now:  2009-10-12 3:32:55 next: 2009-10-12 3:35:00
            array(mktime(3, 30, 30, 10, 12, 2009), mktime(3, 30, 15, 10, 12, 2009), '30', '10/20', '3', '*', '*', '*'), // now:  2009-10-12 3:32:55 next: 2009-10-12 3:35:00
		);
	}

}