<?php

namespace zibo\library\html\form\captcha;

use zibo\core\Zibo;

use zibo\library\html\form\captcha\exception\CaptchaException;
use zibo\library\html\form\Form;
use zibo\library\String;
use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * Manager of the captchas.
 */
class CaptchaManager {

    /**
     * Class name of the captcha interface
     * @var string
     */
    const INTERFACE_CAPTCHA = 'zibo\\library\\html\\form\\captcha\\Captcha';

    /**
     * Configuration key for the available captchas
     * @var string
     */
    const CONFIG_CAPTCHA = 'captcha';

    /**
     * Name of the default captcha
     * @var string
     */
    const NAME_DEFAULT = 'default';

    /**
     * Instance of the manager, singleton pattern
     * @var CaptchaManager
     */
    private static $instance;

    /**
     * Array with all the registered captchas; the name as key and the captcha instance as value
     * @var array
     */
    private $captchas;

    /**
     * Name of the default captcha
     * @var string
     */
    private $defaultCaptchaName;

    /**
     * Constructs a new captcha manager: loads the captchas from the configuration
     * @return null
     */
    private function __construct() {
        $this->captchas = array();
        $this->defaultCaptchaName = null;

        $this->loadCaptchasFromConfig();
    }

    /**
     * Gets the instance of the captcha manager
     * @return CaptchaManager Instance of the captcha manager
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Adds the necessairy fields for the captcha to the form
     * @param zibo\library\html\form\Form $form Form which needs a captcha
     * @param string $captcha Name of the captcha
     * @return null
     */
    public function addCaptchaToForm(Form $form, $captcha = null) {
        $captcha = $this->getCaptcha($captcha);

        $captcha->addCaptchaToForm($form);
    }

    /**
     * Validated the captcha in the provided form
     * @param zibo\library\html\form\Form $form Submitted form with the captcha added
     * @param string $captcha Name of the captcha
     * @return null
     * @throws zibo\library\html\form\captcha\exception\CaptchaException when the captcha failed
     */
    public function validateCaptcha(Form $form, $captcha = null) {
        $captcha = $this->getCaptcha($captcha);

        $captcha->validateCaptcha($form);
    }

    /**
     * Gets a view for the captcha part
     * @param zibo\library\html\form\Form $form Form with the captcha added
     * @param string $captcha Name of the captcha
     * @return zibo\core\view\HtmlView
     */
    public function getCaptchaView(Form $form, $captcha = null) {
        $captcha = $this->getCaptcha($captcha);

        return $captcha->getCaptchaView($form);
    }

    /**
     * Gets the implementation of the provided captcha
     * @param string $name Name of the captcha
     * @return Captcha The captcha implementation
     * @throws zibo\ZiboException when the name is empty or invalid
     * @throws zibo\ZiboException when the captcha is not registered
     */
    public function getCaptcha($name = null) {
        if (!$name) {
            $name = $this->defaultCaptchaName;
        }

        if (!$this->captchas) {
            throw new ZiboException('No captchas registered. Please install a captcha');
        } elseif (!$this->hasCaptcha($name)) {
            throw new ZiboException('Could not get the captcha: captcha ' . $name . ' is not registered');
        }

        return $this->captchas[$name];
    }

    /**
     * Checks if a captcha has been registered
     * @param string $name Name of the captcha
     * @return boolean True if the captcha exists, false otherwise
     * @throws zibo\ZiboException when the name is empty or invalid
     */
    public function hasCaptcha($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Could not get the captcha: provided name is empty');
        }

        return array_key_exists($name, $this->captchas);
    }

    /**
     * Sets the default captcha
     * @param string $defaultCaptchaName Name of the new default captcha
     * @return null
     * @throws zibo\ZiboException when the captcha name is invalid or when the captcha does not exist
     */
    public function setDefaultCaptchaName($defaultCaptchaName) {
        if (String::isEmpty($defaultCaptchaName)) {
            throw new ZiboException('Provided captcha name is empty');
        }

        if (!$this->hasCaptcha($defaultCaptchaName)) {
            throw new ZiboException('Captcha ' . $defaultCaptchaName . ' does not exist');
        }

        $this->defaultCaptchaName = $defaultCaptchaName;
    }

    /**
     * Gets the name of the default captcha
     * @return string Name of the default captcha
     */
    public function getDefaultCaptchaName() {
        return $this->defaultCaptchaName;
    }

    /**
     * Registers a captcha in the manager
     * @param string $name Internal name of the captcha
     * @param string $className Class name of the captcha
     * @return null
     * @throws zibo\ZiboException when the internal name or class name is empty or invalid
     * @throws zibo\ZiboException when the captcha does not exist or is not a valid captcha class
     */
    public function registerCaptcha($name, $className) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        if (String::isEmpty($className)) {
            throw new ZiboException('Provided captcha class name is empty');
        }

        $objectFactory = new ObjectFactory();
        $this->captchas[$name] = $objectFactory->create($className, self::INTERFACE_CAPTCHA);

        if (!$this->defaultCaptchaName) {
            $this->defaultCaptchaName = $name;
        }
    }

    /**
     * Loads the captchas from the Zibo configuration
     * @return null
     */
    private function loadCaptchasFromConfig() {
        $captchas = Zibo::getInstance()->getConfigValue(self::CONFIG_CAPTCHA, array());

        $defaultCaptchaName = null;
        foreach ($captchas as $name => $className) {
            if ($name == self::NAME_DEFAULT) {
                $defaultCaptchaName = $name;
            }

            try {
                $this->registerCaptcha($name, $className);
            } catch (ZiboException $exception) {
                if ($name == self::NAME_DEFAULT) {
                    $defaultCaptchaName = $className;
                } else {
                    throw $exception;
                }
            }
        }

        if ($defaultCaptchaName != null) {
            $this->setDefaultCaptchaName($defaultCaptchaName);
        }
    }

}