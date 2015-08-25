<?php

namespace zibo\cron\model;

use zibo\library\System;

use zibo\ZiboException;

/**
 * Object to start and stop the cron process on the server
 * @todo Windows support
 */
class CronProcess {

    /**
     * The id of the cron process
     * @var integer
     */
    private $pid;

    /**
     * The command to execute the cron process
     * @var string
     */
    private $command;

    /**
     * Constructs a new cron process object
     * @param string $rootPath The root path of the Zibo installation
     * @return null
     */
    public function __construct($rootPath) {
        $this->command = 'php ' . $rootPath . '/index.php cron';

        $result = System::execute('ps aux | grep "' . $this->command . '"');
        $result = explode("\n", $result);

        foreach ($result as $line) {
            if (strpos($line, 'grep') !== false) {
                continue;
            }

            $tokens = explode(' ', $line);

            $this->pid = $tokens[1];
        }
    }

    /**
     * Gets the command to start the cron process for execution in a shell
     * @return string
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * Starts the process of the cron server
     * @return null
     */
    public function start() {
        System::execute($this->command . ' > /dev/null 2> /dev/null & echo $!');
    }

    /**
     * Stop the process of the cron server
     * @return null
     */
    public function stop() {
        if (!$this->pid) {
            throw new ZiboException('The cron process is not running');
        }

        System::execute('kill ' . $this->pid);
    }

    /**
     * Gets whether the cron process is running
     * @return boolean
     */
    public function isRunning() {
        return $this->pid ? true : false;
    }

}