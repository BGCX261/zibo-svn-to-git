<?php

namespace zibo\library\archive;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \Exception;

class ArchiveFactoryTest extends BaseTestCase {

    public function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $configIO = new ConfigIOMock();
        Zibo::getInstance($browser, $configIO);
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testRegister() {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('Zip is unsupported on this system.');
        }

        $typeName = 'zip';
        $typeClass = 'zibo\\library\\archive\\Zip';

        $factory = ArchiveFactory::getInstance();
        $factory->register($typeName, $typeClass);

        $types = Reflection::getProperty($factory, 'types');

        $this->assertArrayHasKey($typeName, $types);
        $this->assertContains($typeClass, $types);
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testRegisterWithEmptyNameThrowsException() {
        $factory = ArchiveFactory::getInstance();
        $factory->register('', 'someClass');
    }

    /**
     * @dataProvider providerRegisterWithInvalidClassThrowsException
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testRegisterWithInvalidClassThrowsException($class) {
        $factory = ArchiveFactory::getInstance();
        $factory->register('extension', $class);
    }

    public function providerRegisterWithInvalidClassThrowsException() {
        return array(
            array(''),
            array('zibo\\library\\archive\\Invalid'),
            array('zibo\\library\\String'),
        );
    }

    public function testGetArchive() {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('Zip is unsupported on this system.');
        }
        $typeName = 'zip';
        $typeClass = 'zibo\\library\\archive\\Zip';

        $factory = ArchiveFactory::getInstance();
        $factory->register($typeName, $typeClass);

        $file = new File('test.zip');
        $archive = $factory->getArchive($file);

        $archiveFile = Reflection::getProperty($archive, 'file');

        $this->assertEquals($file, $archiveFile);
    }

    /**
     * @expectedException zibo\library\archive\exception\ArchiveException
     */
    public function testGetArchiveWithInvalidArchiveThrowsException() {
        $file = new File('test.txt');

        $factory = ArchiveFactory::getInstance();
        $factory->getArchive($file);
    }

}