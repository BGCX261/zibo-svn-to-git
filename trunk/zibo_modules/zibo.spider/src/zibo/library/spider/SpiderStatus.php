<?php

namespace zibo\library\spider;

use zibo\library\filesystem\File;

use \Exception;

/**
 *
 */
class SpiderStatus {

    private $current;

    private $visited;

    private $gathered;

    private $start;

    private $stop;

    public function __construct($current = null, $visited = 0, $gathered = 0, $start = 0, $stop = 0) {
        $this->current = $current;
        $this->visited = $visited;
        $this->gathered = $gathered;
        $this->start = $start;
        $this->stop = $stop;
    }

    public function getCurrent() {
        return $this->current;
    }

    public function getVisited() {
        return $this->visited;
    }

    public function getGathered() {
        return $this->gathered;
    }

    public function getStart() {
        return $this->start;
    }

    public function getStop() {
        return $this->stop;
    }

    public function getElapsedTime() {
        if (!$this->start) {
            return '00:00:00';
        }

        if ($this->stop) {
            $stop = $this->stop;
        } else {
            $stop = time();
        }

        $seconds = $stop - $this->start;

        $hours = floor($seconds / 3600);
        $seconds = $seconds % 3600;

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        $time = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $time .= ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $time .= ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);

        return $time;
    }

    public function isFinished() {
        return $this->stop ? true : false;
    }

    public function read(File $file) {
        if (!$file->exists()) {
            return false;
        }

        $ini = $file->read();
        $ini = parse_ini_string($ini);

        if (array_key_exists('current', $ini)) {
            $this->current = $ini['current'];
        }

        $this->visited = $ini['visited'];
        $this->gathered = $ini['gathered'];
        $this->start = $ini['start'];
        $this->stop = $ini['stop'];
    }

    public function write(File $file) {
        $output = 'current = "' . $this->current. "\"\n";
        $output .= 'visited = ' . $this->visited . "\n";
        $output .= 'gathered = ' . $this->gathered . "\n";
        $output .= 'start = ' . $this->start . "\n";
        $output .= 'stop = ' . $this->stop . "\n";

        $parent = $file->getParent();
        $parent->create();

        $file->write($output);
    }

}