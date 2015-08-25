<?php

namespace zibo\library\i18n\translation\io;

use zibo\core\filesystem\GenericFileBrowser;
use zibo\core\Zibo;

use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class IniTranslationIOTest extends BaseTestCase {

    public function setUp() {
        $dir = __DIR__ . '/../../../../../..';

        // current working directory is the test folder
        // we want to use the regular zibo browser but in our test folder, so the system and application folders in our test folder
        // are in the browser include paths
        $browser = new GenericFileBrowser(new File($dir));

        $this->configIOMock = new ConfigIOMock();
        $zibo = new Zibo($browser, $this->configIOMock);

        $this->io = new IniTranslationIO($zibo);

        $this->setUpApplication($dir);
    }

    public function tearDown() {
        $this->tearDownApplication();
    }

    public function testGetTranslationReadsTranslationFromIniFilesInL10nFoldersOfZiboIncludePaths() {
        $this->assertEquals('Vertaling 1', $this->io->getTranslation('nl', 'translation1'));
        $this->assertEquals('Vertaling 2 uit application', $this->io->getTranslation('nl', 'translation2'));
    }

    public function testGetTranslationsReturnsAllTranslationsAsArray() {
        $translations = $this->io->getTranslations('nl');

        $this->assertEquals(4, count($translations));

        foreach ($translations as $key => $translation) {
            switch($key) {
                case 'translation1': $this->assertEquals('Vertaling 1', $translation); break;
                case 'translation2': $this->assertEquals('Vertaling 2 uit application', $translation); break;
                case 'translation3': $this->assertEquals('Vertaling 3', $translation); break;
                case 'translation4': $this->assertEquals('Vertaling 4', $translation); break;
                default: $this->fail('unexpected translation key: ' . $key);
            }
        }
    }

    /**
     * @dataProvider providerSetTranslation
     */
    public function testSetTranslation($translation) {
        $localeCode = 'write';
        $key = 'test.write';

        $this->io->setTranslation($localeCode, $key, $translation);

        $fileName = Zibo::DIRECTORY_APPLICATION . File::DIRECTORY_SEPARATOR . Zibo::DIRECTORY_L10N . File::DIRECTORY_SEPARATOR . $localeCode . IniTranslationIO::EXTENSION;
        $translationFile = new File($fileName);

        $this->assertTrue($translationFile->exists(), 'No file is being written');

        $iniContent = $translationFile->read();
        $ini = parse_ini_string($iniContent, false);

        $this->assertEquals(array($key => $translation), $ini);
    }

    public function providerSetTranslation() {
        return array(
            array('test translation'),
            array('test translation with %var%'),
            array('test translation with a " ...'),
        );
    }

    public function testSetTranslationWhenTranslationFileExists() {
        $localeCode = 'locale';
        $key = 'translation5';
        $translation = 'Translation 5';

        $this->io->setTranslation($localeCode, $key, $translation);

        $fileName = Zibo::DIRECTORY_APPLICATION . File::DIRECTORY_SEPARATOR . Zibo::DIRECTORY_L10N . File::DIRECTORY_SEPARATOR . $localeCode . IniTranslationIO::EXTENSION;
        $translationFile = new File($fileName);

        $this->assertTrue($translationFile->exists(), 'No file is being written');

        $iniContent = $translationFile->read();
        $translations = parse_ini_string($iniContent, false);

        $this->assertEquals(5, count($translations));
        $this->assertTrue(array_key_exists($key, $translations));
        $this->assertEquals($translation, $translations[$key]);
    }


}