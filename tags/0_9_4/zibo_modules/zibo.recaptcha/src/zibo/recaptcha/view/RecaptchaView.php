<?php

namespace zibo\recaptcha\view;

use zibo\core\view\HtmlView;
use zibo\core\Zibo;

require_once __DIR__ . '/../../../../vendor/recaptcha/recaptchalib.php';

/**
 * View for the recaptcha field
 */
class RecaptchaView extends HtmlView {

    /**
     * Configuration key for the default theme
     * @var string
     */
    const CONFIG_THEME = 'recaptcha.theme';

    /**
     * The public key of the recaptcha
     * @var string
     */
    private $publicKey;

    /**
     * Error of the previous submit
     * @var string
     */
    private $error;

    /**
     * The theme of the recaptcha
     * @var string
     */
    private $theme;

    /**
     * Constructs a new recaptcha view
     * @param string $publicKey The public key of the recaptcha
     * @param string $error Error of the previous submit
     * @return null
     */
    public function __construct($publicKey, $error = null, $theme = null) {
        if (!$theme) {
            $theme = Zibo::getInstance()->getConfigValue(self::CONFIG_THEME);
        }

        $this->publicKey = $publicKey;
        $this->error = $error;
        $this->theme = $theme;
    }

    /**
     * Render the view
     * @param boolean $return true to return the rendered view, false to send it to the client
     * @return mixed null when provided $return is set to true; the rendered output when the provided $return is set to false
     */
    public function render($return = true) {
        $config = '';
        if ($this->theme) {
            $config = '<script type="text/javascript">var RecaptchaOptions = { theme: \'' . $this->theme . '\'}; </script>';
        }

        $html = $config . recaptcha_get_html($this->publicKey, $this->error);

        if ($return) {
            return $html;
        }

        echo $html;
    }

}