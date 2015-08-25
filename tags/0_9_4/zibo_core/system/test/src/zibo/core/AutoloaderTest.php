<?php

namespace zibo\core;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;


class AutoloaderTest extends BaseTestCase {

    public function testAutoload() {
        $browser = new GenericBrowser(new File(getcwd()));
        $autoloader = new Autoloader($browser);

        $this->assertTrue($autoloader->autoload('zibo\\core\\Dispatcher'));
        $this->assertFalse($autoloader->autoload('zibo\\test'));
    }

}