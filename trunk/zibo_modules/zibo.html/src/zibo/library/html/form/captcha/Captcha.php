<?php

namespace zibo\library\html\form\captcha;

use zibo\library\html\form\Form;

/**
 * Interface for a form captcha. A captcha protects a form from automated submits (bots)
 */
interface Captcha {

    /**
     * Adds the necessairy fields for the captcha to the form
     * @param zibo\library\html\form\Form $form Form which needs a captcha
     * @return null
     */
    public function addCaptchaToForm(Form $form);

    /**
     * Validated the captcha in the provided form
     * @param zibo\library\html\form\Form $form Submitted form with the captcha added
     * @return null
     * @throws zibo\library\html\form\captcha\exception\CaptchaException when the captcha failed
     */
    public function validateCaptcha(Form $form);

    /**
     * Gets a view for the captcha part
     * @param zibo\library\html\form\Form $form Form with the captcha added
     * @return zibo\core\view\HtmlView
     */
    public function getCaptchaView(Form $form);

}