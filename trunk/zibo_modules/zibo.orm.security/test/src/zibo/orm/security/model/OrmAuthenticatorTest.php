<?php

namespace zibo\security\orm\model;

use zibo\core\Zibo;

use zibo\library\config\IniIO;

use zibo\library\orm\util\ModelLoader;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class OrmAuthenticatorTest extends BaseTestCase {

    public function setUp() {
        Zibo::getInstance(new IniIO());
        ModelLoader::clearCache();
    }

    public function tearDown() {
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetCookieTimeoutThrowsZiboExceptionOnNonNumericArgument() {
        $auth = new OrmAuthenticator();
        $auth->setCookieTimeout('string');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetCookieTimeoutThrowsZiboExceptionOnNumericArgumentLowerThanNull() {
        $auth = new OrmAuthenticator();
        $auth->setCookieTimeout(-1);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetCookieTimeoutThrowsZiboExceptionOnIntegerZeroArgument() {
        $auth = new OrmAuthenticator();
        $auth->setCookieTimeout(0);
    }

    public function testGetCookieTimeoutReturnsValueOfSetCookieTimeoutArgument() {
        $auth = new OrmAuthenticator();
        $auth->setCookieTimeout(50);
        $this->assertEquals(50, $auth->getCookieTimeout());
    }
}