<?php

namespace zibo\core;

use zibo\core\filesystem\GenericFileBrowser;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;


class AutoloaderTest extends BaseTestCase {

    public function testAutoload() {
        $browser = new GenericFileBrowser(new File(getcwd()));
        $autoloader = new Autoloader($browser);

        $this->assertTrue($autoloader->autoload('zibo\\core\\dispatcher\\Dispatcher'));
        $this->assertFalse($autoloader->autoload('zibo\\test'));
    }

}