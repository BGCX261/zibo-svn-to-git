<?php

namespace zibo\recaptcha;

use zibo\core\Zibo;

use zibo\library\html\form\captcha\exception\CaptchaException;
use zibo\library\html\form\captcha\Captcha;
use zibo\library\html\form\Form;
use zibo\library\String;

use zibo\recaptcha\view\RecaptchaView;

use zibo\ZiboException;

require_once __DIR__ . '/../../../vendor/recaptcha/recaptchalib.php';

/**
 * Recaptcha implementation
 */
class Recaptcha implements Captcha {

    /**
     * Configuration key to the public key
     * @var string
     */
    const CONFIG_PUBLIC_KEY = 'recaptcha.key.public';

    /**
     * Configuration key to the private key
     * @var string
     */
    const CONFIG_PRIVATE_KEY = 'recaptcha.key.private';

    /**
     * Name of the recaptcha challenge field
     * @var string
     */
    const FIELD_CHALLENGE = 'recaptcha_challenge_field';

    /**
     * Name of the recaptcha response field
     * @var string
     */
    const FIELD_RESPONSE = 'recaptcha_response_field';

    /**
     * The public key
     * @var string
     */
    private $publicKey;

    /**
     * The private key
     * @var string
     */
    private $privateKey;

    /**
     * The error provided by the recaptcha server, kept in a array with the form id
     * @var array
     */
    private $errors;

    /**
     * Constructs a new recaptcha
     * @return null
     */
    public function __construct() {
        $zibo = Zibo::getInstance();

        $publicKey = $zibo->getConfigValue(self::CONFIG_PUBLIC_KEY);
        $privateKey = $zibo->getConfigValue(self::CONFIG_PRIVATE_KEY);

        $this->setPublicKey($publicKey);
        $this->setPrivateKey($privateKey);

        $this->errors = array();
    }

    /**
     * Sets the public key for the recaptcha
     * @param string $publicKey
     * @return null
     * @throws zibo\ZiboException when the public key is empty or invalid
     */
    public function setPublicKey($publicKey) {
        if (String::isEmpty($publicKey)) {
            throw new ZiboException('Could not set the public key: provided public key is empty');
        }

        $this->publicKey = $publicKey;
    }

    /**
     * Sets the private key for the recaptcha
     * @param string $privateKey
     * @return null
     * @throws zibo\ZiboException when the private key is empty or invalid
     */
    private function setPrivateKey($privateKey) {
        if (String::isEmpty($privateKey)) {
            throw new ZiboException('Could not set the private key: provided private key is empty');
        }

        $this->privateKey = $privateKey;
    }

    /**
     * Adds the necessairy fields for the captcha to the form
     * @param zibo\library\html\form\Form $form Form which needs a captcha
     * @return null
     */
    public function addCaptchaToForm(Form $form) {

    }

    /**
     * Validated the captcha in the provided form
     * @param zibo\library\html\form\Form $form Submitted form with the captcha added
     * @return null
     * @throws zibo\library\html\form\captcha\exception\CaptchaException when the captcha failed
     */
    public function validateCaptcha(Form $form) {
        if (!array_key_exists(self::FIELD_RESPONSE, $_POST) || !array_key_exists(self::FIELD_CHALLENGE, $_POST)) {
            return;
        }

        $resp = recaptcha_check_answer($this->privateKey, $_SERVER["REMOTE_ADDR"], $_POST[self::FIELD_CHALLENGE], $_POST[self::FIELD_RESPONSE]);

        if ($resp->is_valid) {
            return;
        }

        $this->errors[$form->getId()] = $resp->error;

        throw new CaptchaException($resp->error);
    }

    /**
     * Gets a view for the captcha part
     * @param zibo\library\html\form\Form $form Form with the captcha added
     * @return zibo\core\view\HtmlView
     */
    public function getCaptchaView(Form $form) {
        $formId = $form->getId();

        $error = null;
        if (array_key_exists($formId, $this->errors)) {
            $error = $this->errors[$formId];
        }

        return new RecaptchaView($this->publicKey, $error);
    }

}