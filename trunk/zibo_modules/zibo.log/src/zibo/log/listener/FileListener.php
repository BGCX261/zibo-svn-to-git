<?php

namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\library\filesystem\Formatter;
use zibo\library\Number;
use zibo\library\String;

use zibo\log\listener\AbstractFilteredLogListener;
use zibo\log\LogItem;

use zibo\ZiboException;

/**
 * Log listener to Write log items to file
 */
class FileListener extends AbstractFilteredLogListener {

    /**
     * Configuration key suffix for the date format
     * @var string
     */
	const CONFIG_DATE_FORMAT = 'date.format';

	/**
	 * Configuration key suffix for the file name
	 * @var string
	 */
	const CONFIG_FILE_NAME = 'file.name';

	/**
	 * Configuration key suffix for the maximum file size
	 * @var string
	 */
	const CONFIG_FILE_TRUNCATE_SIZE = 'file.max.size';

	/**
	 * Default date format
	 * @var string
	 */
	const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

	/**
	 * Default maximum file size
	 * @var integer
	 */
	const DEFAULT_TRUNCATE_SIZE = 1024;

	/**
	 * Separator between the fields
	 * @var string
	 */
	const FIELD_SEPARATOR = ' - ';

	/**
	 * Date format for the date
	 * @var string
	 */
	private $dateFormat;

	/**
	 * File name of the log
	 * @var string
	 */
    private $fileName;

    /**
     * Maximum file size
     * @var integer
     */
    private $fileTruncateSize;

    /**
     * Construct a new file log listener
     * @param string $fileName Path of the log file
     * @return null
     */
    public function __construct($fileName) {
    	if (String::isEmpty($fileName)) {
    		throw new ZiboException('Provided file name is empty');
    	}

        $this->fileName = $fileName;
        $this->fileTruncateSize = self::DEFAULT_TRUNCATE_SIZE;
        $this->dateFormat = self::DEFAULT_DATE_FORMAT;
    }

    /**
     * Set the date format used to write the timestamp of the log item
     */
    public function setDateFormat($dateFormat) {
    	if (String::isEmpty($dateFormat)) {
    		throw new ZiboException('Provided date format is empty');
    	}
    	$this->dateFormat = $dateFormat;
    }

    /**
     * Get the date format used to write the timestamp of the log item
     * @return string date format
     */
    public function getDateFormat() {
    	return $this->dateFormat;
    }

    /**
     * Set the limit in kb before the log file gets truncate
     * @param integer size limit in kilobytes
     */
    public function setFileTruncateSize($size) {
    	if (Number::isNegative($size)) {
    		throw new ZiboException($size . ' should be positive or zero');
    	}
    	$this->fileTruncateSize = $size;
    }

    /**
     * Get the limit in kb before the log file gets truncate
     * @oaram integer size limit in kilobytes
     */
    public function getFileTruncateSize() {
    	return $this->fileTruncateSize;
    }

    /**
     * Write a log item to the log file
     */
    protected function writeLogItem(LogItem $item) {
        $output = $this->getLogItemOutput($item);
        if ($this->writeFile($output)) {
            $this->truncateFile($output);
        }
    }

    /**
     * Get the output string of a log item
     * @param LogItem
     */
    protected function getLogItemOutput(LogItem $item) {
        $output = '';
        $output = date($this->getDateFormat(), $item->getDate());
        $output .= self::FIELD_SEPARATOR . substr($item->getMicroTime(), 0, 5);
        $output .= self::FIELD_SEPARATOR . $item->getIP();
        $output .= self::FIELD_SEPARATOR . str_pad($item->getName(), 9);
        $output .= self::FIELD_SEPARATOR . str_pad(Formatter::formatSize(memory_get_usage()), 9, ' ', STR_PAD_LEFT);
        $output .= self::FIELD_SEPARATOR . $item->getType();
        $output .= self::FIELD_SEPARATOR . $item->getTitle();

        $message = $item->getMessage();
        if (!empty($message)) {
            $output .= self::FIELD_SEPARATOR . $message;
        }
        $output .= "\n";

        return $output;
    }

    /**
     * Append the output to the log file
     * @param string output to append
     */
    private function writeFile($output) {
        if (!($f = @fopen($this->fileName, 'a'))) {
            return false;
        }

        fwrite($f, $output);
        fclose($f);

        return true;
    }

    /**
     * Truncate the log tile if the truncate size is set and the log file is bigger then the truncate size
     * @param string output string to write in the truncated file, empty by default
     */
    private function truncateFile($output = '') {
    	$truncateSize = $this->getFileTruncateSize();
    	if (!$truncateSize) {
    		return;
    	}

        $fileSize = filesize($this->fileName) / 1024; // we work with kb
        if ($fileSize < $truncateSize) {
        	return;
        }

        if ($f = @fopen($this->fileName, 'w')) {
            fwrite($f, $output);
            fclose($f);
        }
    }

    public static function createListenerFromConfig(Zibo $zibo, $name, $configBase) {
    	$fileName = self::getCreateListenerFileName($zibo, $name, $configBase);
        $listener = new self($fileName);
        self::setParametersToCreatedListener($listener, $zibo, $name, $configBase);
        self::addFiltersToCreatedListener($listener, $zibo, $name, $configBase);

        return $listener;
    }

    protected static function getCreateListenerFileName(Zibo $zibo, $name, $configBase) {
        $configFileName = $configBase . self::CONFIG_FILE_NAME;
        $fileName = $zibo->getConfigValue($configFileName);
        if (empty($fileName)) {
            throw new ZiboException('No file name set for log ' . $name . '. Try setting the configuration ' . $configFileName);
        }
    	return $fileName;
    }

    protected static function setParametersToCreatedListener(FileListener $listener, Zibo $zibo, $name, $configBase) {
        $configFileTruncateSize = $configBase . self::CONFIG_FILE_TRUNCATE_SIZE;
        $fileTruncateSize = $zibo->getConfigValue($configFileTruncateSize);
        if ($fileTruncateSize !== null) {
            $listener->setFileTruncateSize($fileTruncateSize);
        }

        $configDateFormat = $configBase . self::CONFIG_DATE_FORMAT;
        $dateFormat = $zibo->getConfigValue($configDateFormat);
        if ($dateFormat !== null) {
            $listener->setDateFormat($dateFormat);
        }
    }

}