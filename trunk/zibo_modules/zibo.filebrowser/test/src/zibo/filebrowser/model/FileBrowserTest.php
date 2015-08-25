<?php

namespace zibo\filebrowser\model;

use zibo\filebrowser\model\filter\DirectoryFilter;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

class FileBrowserTest extends BaseTestCase {

	private $browser;

	public function setUp() {
		$this->browser = new FileBrowser(new File(__DIR__));
	}

	public function testReadDirectory() {
        $directorySvn = new File('.svn');
        $directoryFilter = new File('filter');
        $fileFileBrowserTest = new File('FileBrowserTest.php');
        $expected = array (
            $fileFileBrowserTest->getPath() => $fileFileBrowserTest,
            $directorySvn->getPath() => $directorySvn,
            $directoryFilter->getPath() => $directoryFilter,
        );

        $files = $this->browser->readDirectory();
        $this->assertEquals($expected, $files);
	}

	/**
     * @dataProvider providerReadDirectoryThrowExceptionWhenInvalidFilePassed
     * @expectedException zibo\ZiboException
	 */
	public function testReadDirectoryThrowExceptionWhenInvalidFilePassed(File $directory) {
        $this->browser->readDirectory($directory);
	}

	public function providerReadDirectoryThrowExceptionWhenInvalidFilePassed() {
		return array(
            array(new File('src/zibo/filebrowser/FileBrowserTest.php')), // file
            array(new File('unexistant')), // unexistant
//            array(new File('/root')), // unreadable
		);
	}

    public function testReadDirectoryWithFilters() {
        $directoryFilter = new File('filter');
        $directorySvn = new File('.svn');
        $expected = array (
            $directoryFilter->getPath() => $directoryFilter,
            $directorySvn->getPath() => $directorySvn,
        );

        $filter = new DirectoryFilter();

        $files = $this->browser->readDirectory(null, array($filter));
        $this->assertEquals($expected, $files);
    }

}