<?php

namespace zibo\cron\model;

use zibo\library\DateTime;

/**
 * CronJob extended to have a second precision instead of a minute precision
 */
class SuperCronJob extends GenericCronJob {

    /**
     * Array with the second values or ASTERIX for all values
     * @var string|array
     */
    protected $second;

    /**
     * Constructs a new job
     * @param string|array|zibo\library\Callback $callback
     * @param string $second
     * @param string $minute
     * @param string $hour
     * @param string $day
     * @param string $month
     * @param string $dayOfWeek
     * @return null
     */
    public function __construct($callback, $second = null, $minute = null, $hour = null, $day = null, $month = null, $dayOfWeek = null) {
        $this->setCallback($callback);
        $this->setRunInterval($second, $minute, $hour, $day, $month, $dayOfWeek);
    }

    /**
     * Gets a unique id for this job
     * @return string
     */
    public function getId() {
        if ($this->id) {
            return $this->id;
        }

        $id = serialize($this->callback->__toString());
        $id .= serialize($this->second);
        $id .= serialize($this->minute);
        $id .= serialize($this->hour);
        $id .= serialize($this->day);
        $id .= serialize($this->month);
        $id .= serialize($this->dayOfWeek);
        $this->id = md5($id);

        return $this->id;
    }

    /**
     * Gets the time when this job should run next
     * @param int $time if not provided, the last run time will be used or now if this job hasn't run yet
     * @return int
     */
    public function getNextRunTime($time = null) {
        if ($time === null) {
            if ($this->lastRunTime) {
                $time = $this->lastRunTime;
            } else {
                $time = time();
            }
        }

        $second = date('s', $time);
        $minute = date('i', $time);
        $hour = date('G', $time);
        $day = date('j', $time);
        $month = date('n', $time);
        $year = date('Y', $time);
        $dayOfWeek = date('w', $time);

        if ($this->second == self::ASTERIX && $this->minute == self::ASTERIX && $this->hour == self::ASTERIX && $this->day == self::ASTERIX && $this->month == self::ASTERIX && $this->dayOfWeek == self::ASTERIX) {
            $this->addSecond($second, $minute, $hour, $day, $month, $year);
            return mktime($hour, $minute, $second, $month, $day, $year);
        }

        $newMinute = $minute;
        $newHour = $hour;
        $newDay = $day;
        $newMonth = $month;
        $newYear = $year;
        $changed = false;

        $newSecond = $this->getNextRunIntervalValue($this->second, $second, null, false);
        if ($newSecond === null) {
            $newSecond = $second;
            $this->addSecond($newSecond, $newMinute, $newHour, $newDay, $newMonth, $newYear);
        }

        if ($newSecond != $second) {
            $changed = true;
        }

        $tmpMinute = $newMinute;
        if ($second < $newSecond) {
            $newMinute = $this->getNextRunIntervalValue($this->minute, $newMinute, $newMinute, true);
        } else {
            $newMinute = $this->getNextRunIntervalValue($this->minute, $newMinute, null, false);
        }
        if ($newMinute === null) {
            $newMinute = $tmpMinute;
            if ($newMinute == $minute) {
                $this->addMinute($newMinute, $newHour, $newDay, $newMonth, $newYear);
            }
        }

        if ($newMinute != $minute) {
            $changed = true;
            $newSecond = $this->getFirstRunIntervalValue($this->second, 0);
        }

        $tmpHour = $newHour;
        if ($newMinute < $minute || ($newMinute == $minute && $newSecond <= $second)) {
           $newHour = $this->getNextRunIntervalValue($this->hour, $newHour, null, false);
        } else {
           $newHour = $this->getNextRunIntervalValue($this->hour, $newHour, $newHour, true);
        }
        if ($newHour === null) {
            $newHour = $tmpHour;
            if ($newHour == $hour) {
                $this->addHour($newHour, $newDay, $newMonth, $newYear);
            }
        }

        if ($newHour != $hour) {
            $changed = true;
            $newSecond = $this->getFirstRunIntervalValue($this->second, 0);
            $newMinute = $this->getFirstRunIntervalValue($this->minute, 0);
        }

        $tmpDay = $newDay;
        if ($newHour < $hour || ($newHour == $hour && ($newMinute < $minute || ($newMinute == $minute && $newSecond <= $second)))) {
            $newDay = $this->getNextRunIntervalValue($this->day, $newDay, null, false);
        } else {
            $newDay = $this->getNextRunIntervalValue($this->day, $newDay, $newDay, true);
        }
        if ($newDay === null) {
            $newDay = $tmpDay;
            if ($newDay == $day) {
                $this->addDay($newDay, $newMonth, $newYear);
            }
        }

        if ($newDay != $day) {
            $changed = true;
            $newSecond = $this->getFirstRunIntervalValue($this->second, 0);
            $newMinute = $this->getFirstRunIntervalValue($this->minute, 0);
            $newHour = $this->getFirstRunIntervalValue($this->hour, 0);
        }

        $tmpMonth = $newMonth;
        if ($newDay < $day || ($newDay == $day && ($newHour < $hour || ($newHour == $hour && ($newMinute < $minute || ($newMinute == $minute && $newSecond <= $second)))))) {
            $newMonth = $this->getNextRunIntervalValue($this->month, $newMonth, null, false);
        } else {
            $newMonth = $this->getNextRunIntervalValue($this->month, $newMonth, $newMonth, true);
        }
        if ($newMonth == null) {
            $newMonth = $tmpMonth;
            if ($newMonth == $month) {
                $this->addMonth($newMonth, $newYear);
            }
        }

        if ($newMonth != $month) {
            $newSecond = $this->getFirstRunIntervalValue($this->second, 0);
            $newMinute = $this->getFirstRunIntervalValue($this->minute, 0);
            $newHour = $this->getFirstRunIntervalValue($this->hour, 0);
            $newDay = $this->getFirstRunIntervalValue($this->day, 1);
        }

        $nextRunTime = mktime($newHour, $newMinute, $newSecond, $newMonth, $newDay, $newYear);

        if ($this->dayOfWeek != self::ASTERIX && !isset($this->dayOfWeek[date('w', $nextRunTime)])) {
            $nextRunTime = DateTime::roundTimeToDay($nextRunTime) + DateTime::DAY - 1;
            return $this->getNextRunTime($nextRunTime);
        }

        return $nextRunTime;
    }

    /**
     * Adds a second
     * @param string $second
     * @param string $minute
     * @param string $hour
     * @param string $day
     * @param string $month
     * @param string $year
     * @return null
     */
    protected function addSecond(&$second, &$minute, &$hour, &$day, &$month, &$year) {
        $second++;
        if ($second == 60) {
            $this->addMinute($minute, $hour, $day, $month, $year);
            $second = 0;
        }
    }

    /**
     * Sets the run interval for this job
     * @param string $minute
     * @param string $hour
     * @param string $day
     * @param string $month
     * @param string $dayOfWeek
     * @return null
     */
    protected function setRunInterval($second = null, $minute = null, $hour = null, $day = null, $month = null, $dayOfWeek = null) {
        $this->setRunIntervalSecond($second);
        $this->setRunIntervalMinute($minute);
        $this->setRunIntervalHour($hour);
        $this->setRunIntervalDay($day);
        $this->setRunIntervalMonth($month);
        $this->setRunIntervalDayOfWeek($dayOfWeek);

        if ($second=== null) {
            $second = self::ASTERIX;
        }
        if ($minute === null) {
            $minute = self::ASTERIX;
        }
        if ($hour === null) {
            $hour = self::ASTERIX;
        }
        if ($day === null) {
            $day = self::ASTERIX;
        }
        if ($month === null) {
            $month = self::ASTERIX;
        }
        if ($dayOfWeek === null) {
            $dayOfWeek = self::ASTERIX;
        }

        $this->intervalDefinition = $second . ' ' . $minute . ' ' . $hour . ' ' . $day . ' ' . $month . ' ' . $dayOfWeek;
    }

    /**
     * Sets the run interval for second
     * @param string $second
     * @return null
     */
    protected function setRunIntervalSecond($second = null) {
        if ($second === null || $second == self::ASTERIX) {
            $this->second = self::ASTERIX;
            return;
        }

        $this->second = $this->parseRunIntervalValue($second, 0, 59);
    }

}