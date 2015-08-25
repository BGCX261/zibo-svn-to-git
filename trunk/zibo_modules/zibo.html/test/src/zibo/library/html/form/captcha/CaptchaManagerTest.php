<?php

namespace zibo\library\html\form\captcha;

use zibo\core\Zibo;

use zibo\library\filesystem\browser\GenericBrowser;
use zibo\library\filesystem\File;

use zibo\test\mock\ConfigIOMock;
use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use zibo\ZiboException;

class CaptchaManagerTest extends BaseTestCase {

    const CAPTCHA_MOCK = 'zibo\\library\\html\\form\\captcha\\CaptchaMock';

    private $configIOMock;

    protected function setUp() {
        $browser = new GenericBrowser(new File(getcwd()));
        $this->configIOMock = new ConfigIOMock();

        $zibo = Zibo::getInstance($browser, $this->configIOMock);
    }

    protected function tearDown() {
        Reflection::setProperty(CaptchaManager::getInstance(), 'instance', null);
        Reflection::setProperty(Zibo::getInstance(), 'instance', null);
    }

    public function testGetInstanceLoadsCaptchas() {
        $config = array(
            'mock' => self::CAPTCHA_MOCK,
        );
        $this->configIOMock->setValues('captcha', $config);

        $manager = CaptchaManager::getInstance();

        $captchas = Reflection::getProperty($manager, 'captchas');

        $this->assertTrue(is_array($captchas));
        $this->assertTrue(array_key_exists('mock', $captchas));
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterCaptchaThrowsExceptionWithEmptyName() {
        $manager = CaptchaManager::getInstance();
        $manager->registerCaptcha('', self::CAPTCHA_MOCK);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterCaptchaThrowsExceptionWithEmptyClassName() {
        $manager = CaptchaManager::getInstance();
        $manager->registerCaptcha('mock', '');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testRegisterCaptchaThrowsExceptionWithInvalidClass() {
        $captcha = get_class($this);
        $manager = CaptchaManager::getInstance();
        $manager->registerCaptcha('invalid', $captcha);
    }

    public function testGetCaptchaWithoutName() {
        $manager = CaptchaManager::getInstance();

        $manager->registerCaptcha('mock', self::CAPTCHA_MOCK);

        $captcha = $manager->getCaptcha();
        $this->assertTrue($captcha instanceof CaptchaMock);
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetDefaultCaptchaThrowsExceptionWhenNameIsEmpty() {
        $manager = CaptchaManager::getInstance();
        $manager->setDefaultCaptchaName('');
    }

    /**
     * @expectedException zibo\ZiboException
     */
    public function testSetDefaultCaptchaThrowsExceptionWhenNameDoesNotExist() {
        $manager = CaptchaManager::getInstance();
        $manager->setDefaultCaptchaName('unexistant');
    }

}