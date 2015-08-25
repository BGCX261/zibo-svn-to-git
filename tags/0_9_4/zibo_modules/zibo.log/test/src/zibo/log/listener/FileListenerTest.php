<?php

namespace zibo\log\listener;

use zibo\core\Zibo;

use zibo\library\config\Config;

use zibo\library\filesystem\File;

use zibo\log\LogItem;

use zibo\log\Module;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class FileListenerTest extends BaseTestCase {

    public function testConstruct() {
    	$fileName = 'application/log/log.txt';

    	$listener = new FileListener($fileName);

    	$this->assertEquals($fileName, Reflection::getProperty($listener, 'fileName'));
    	$this->assertEquals(FileListener::DEFAULT_DATE_FORMAT, Reflection::getProperty($listener, 'dateFormat'));
    	$this->assertEquals(FileListener::DEFAULT_TRUNCATE_SIZE, Reflection::getProperty($listener, 'fileTruncateSize'));
    }

    public function testConstructThrowsExceptionWhenEmptyFileNameProvided() {
    	try {
    		new FileListener('');
    	} catch (ZiboException $e) {
    		return;
    	}
    	$this->fail();
    }

    /**
     * @dataProvider providerSetFileTruncateSizeThrowsExceptionWhenInvalidValueProvided
     */
    public function testSetFileTruncateSizeThrowsExceptionWhenInvalidValueProvided($value) {
    	$listener = new FileListener('test');
    	try {
    		$listener->setFileTruncateSize($value);
    	} catch (ZiboException $e) {
    		return;
    	}
    	$this->fail();
    }

    public function providerSetFileTruncateSizeThrowsExceptionWhenInvalidValueProvided() {
    	return array(
            array('test'),
            array('-50'),
    	);
    }

    public function testSetDateFormatThrowsExceptionWhenEmptyValueProvided() {
    	$listener = new FileListener('test');
    	try {
    		$listener->setDateFormat('');
    	} catch (ZiboException $e) {
    		return;
    	}
    	$this->fail();
    }

    public function testAddLogItem() {
    	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    	$file = '/tmp/filelog.test';
    	$listener = new FileListener($file);

        $item = $this->getLogItem();
    	$listener->addLogItem($item);
    	$listener->addLogItem($item);

    	$output = $this->cleanUpLogFile($file);

    	$this->assertEquals(138, strlen($output));
    }

    public function testAddLogItemTruncateFile() {
    	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    	$file = '/tmp/filelogtruncates.test';
    	$listener = new FileListener($file);
    	$listener->setFileTruncateSize(0.0069);

    	$item = $this->getLogItem();
    	$listener->addLogItem($item);
    	$listener->addLogItem($item);

    	$output = $this->cleanUpLogFile($file);

    	$this->assertEquals(69, strlen($output));
    }

    private function getLogItem() {
        $title = 'title';
        $message = 'message';
        $type = LogItem::INFORMATION;
        $name = 'name';
        $item = new LogItem($title, $message, $type, $name);
        $item->setMicrotime('0.001');

    	return $item;
    }

    private function cleanUpLogFile($file) {
        $output = file_get_contents($file);
        @unlink($file);

        return $output;
    }

    /**
     * @dataProvider providerCreateListenerFromConfig
     */
    public function testCreateListenerFromConfig($name, $succeeds, $fileName = null, $fileTruncateSize = null, $dateFormat = null) {
        $zibo = $this->getZibo();

        $configBase = Module::CONFIG_LOG . Config::TOKEN_SEPARATOR . $name . Config::TOKEN_SEPARATOR;
        try {
            $listener = FileListener::createListenerFromConfig($zibo, $name, $configBase);
            $this->assertNotNull($listener);
        } catch (ZiboException $e) {
        	if ($succeeds) {
        		$this->cleanUpZibo();
        		$this->fail();
        		return;
        	}
        }

        if ($fileName) {
        	$this->assertEquals($fileName, Reflection::getProperty($listener, 'fileName'));
        }
        if ($fileTruncateSize) {
        	$this->assertEquals($fileTruncateSize, Reflection::getProperty($listener, 'fileTruncateSize'));
        }
        if ($dateFormat) {
        	$this->assertEquals($dateFormat, Reflection::getProperty($listener, 'dateFormat'));
        }

        $this->cleanUpZibo();
    }

    public function providerCreateListenerFromConfig() {
    	return array(
            array('valid', true, 'test.log'),
            array('noFile', false),
            array('withSettings', true, 'test.log', 1024, 'Y-m-d'),
    	);
    }

    private function getZibo($name1 = 'valid', $name2 = 'noFile', $name3 = 'withSettings') {
        $browser = $this->getMock('zibo\\library\\filesystem\\browser\\GenericBrowser', null, array(new File(getcwd())));
        $autoloader = $this->getMock('zibo\\core\\Autoloader', $browser);
        $configIOMock = new ConfigIOMock();
        $configIOMock->setValues(Module::CONFIG_LOG, array(
                Module::CONFIG_LISTENER => array(
                    'file' => 'zibo\\log\\listener\\FileListener',
                ),
                $name1 => array(
                    'file' => array(
                        'name' => 'test.log',
                    ),
                ),
                $name2 => array(
                ),
                $name3 => array(
                    'file' => array(
                        'name' => 'test.log',
                        'max' => array(
                            'size' => 1024,
                        ),
                    ),
                    'date' => array(
                        'format' => 'Y-m-d',
                    )
                )
            )
        );

        return Zibo::getInstance($autoloader, $configIOMock);
    }

    private function cleanUpZibo() {
    	Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

}