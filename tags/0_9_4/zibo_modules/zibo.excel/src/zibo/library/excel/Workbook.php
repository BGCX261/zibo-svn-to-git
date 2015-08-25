<?php

namespace zibo\library\excel;

use zibo\library\excel\format\Format;
use zibo\library\filesystem\File;
use zibo\library\DateTime;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_IOFactory;
use \PHPExcel_Settings;
use \PHPExcel;

/**
 * Excel workbook: container for worksheets and properties
 */
class Workbook {

    /**
     * Path for temporary files
     * @var string
     */
    const DIRECTORY_TEMP = 'application/data';

    /**
     * The difference in days between a Excel date and a UNIX timestamp
     * @var integer
     */
    const DIFFERENCE_TIMESTAMP = 25569;

    /**
     * Workbook object of the vendor library
     * @var PHPExcel
     */
    private $workbook;

    /**
     * Flag to see if there are worksheets added to this workbook
     * @var boolean
     */
    private $worksheetAdded;

    /**
     * Construct a new workbook
     * @param zibo\library\filesystem\File $file
     * @param integer $cacheSize Size of the temporary cache in MB
     * @return null
     */
    public function __construct(File $file = null, $cacheSize = null) {
        if ($cacheSize) {
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize' => $cacheSize . 'MB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        }

        if ($file) {
            $this->read($file);
            $this->worksheetAdded = true;
        } else {
            $this->workbook = new PHPExcel();
            $this->worksheetAdded = false;
        }
    }

    /**
     * Read an existing workbook into this instance, writing will become unavailable
     * @param File file to read
     * @return null
     */
    public function read(File $file) {
        $this->workbook = PHPExcel_IOFactory::load($file->getPath());
    }

    /**
     * Write the generated workbook to an xls file on disk
     * @param File file file to save the workbook to
     * @return null
     */
    public function write(File $file, $type = 'Excel5') {
        $writer = PHPExcel_IOFactory::createWriter($this->workbook, $type);
        $writer->save($file->getPath());
    }

    /**
     * Send the generated workbook to the browser
     * @param string filename filename proposed to the user
     * @return null
     */
    public function send($filename, $type = 'Excel5') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createWriter($this->workbook, $type);
        if (method_exists($writer, 'setTempDir')) {
            $tempDir = new File(self::DIRECTORY_TEMP);
            $writer->setTempDir($tempDir->getAbsolutePath());
        }
        $writer->save('php://output');
    }

    /**
     * Create a new worksheet
     * @param string name name of the worksheet
     * @return Worksheet created worksheet
     */
    public function addWorksheet($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Name is empty');
        }

        if (!$this->worksheetAdded) {
            $this->worksheetAdded = true;
            $worksheet = $this->workbook->getActiveSheet();
        } else {
            try {
                $sheet = $this->getWorksheet($name);
                throw new ZiboException('Worksheet ' . $name . ' already exists');
            } catch (Exception $e) {

            }

            $worksheet = $this->workbook->createSheet();
        }

        $worksheet->setTitle($name);

        return new Worksheet($worksheet);
    }

    /**
     * Get an existing worksheet
     * @param string name name of the worksheet
     * @return Worksheet worksheet of the given name
     */
    public function getWorksheet($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        $worksheet = $this->workbook->getSheetByName($name);
        if (!$worksheet) {
            throw new ZiboException('Could not find worksheet ' . $name);
        }

        return new Worksheet($worksheet);
    }

    /**
     * Get the names of all the worksheets
     * @return array
     */
    public function getWorksheetNames() {
        return $this->workbook->getSheetNames();
    }

    /**
     * Converts a serial date or serial date time, the format of Excel dates, into a UNIX timestamp
     * @param float $serialDateTime Date or date time in the Excel format
     * @return integer UNIX timestamp
     */
    public static function convertSerialDateToTimestamp($serialDateTime) {
        $days = floor($serialDateTime / 1);
        $time = $serialDateTime - $days;

        $timestamp = 0;
        if ($days > self::DIFFERENCE_TIMESTAMP) {
            $timestamp = ($days - self::DIFFERENCE_TIMESTAMP) * DateTime::DAY;
        }

        $timestamp += $time * DateTime::DAY;

        return $timestamp;
    }

}