<?php

namespace zibo\admin\controller;

use zibo\admin\Module;
use zibo\admin\form\LocalizePanelForm;

use zibo\library\i18n\I18n;

use zibo\library\Session;

use zibo\ZiboException;

/**
 * Controller to get and set the locale of the localized content
 */
class LocalizeController extends AbstractController {

    /**
     * Name of the session key for the locale of the localized content
     * @var string
     */
    const SESSION_LOCALE = 'locale.content';

    /**
     * Action to change the locale of the localized content
     * @param string $locale Code of the locale, if not specified, the LocalizePanelForm should be submitted
     * @return null
     * @throws zibo\ZiboException when no locale is provided through an argument or the LocalizePanelForm
     */
    public function indexAction($locale = null) {
        $session = Session::getInstance();

        $referer = $session->get(Module::SESSION_REFERER);
        if (!$referer) {
            $referer = $this->request->getBaseUrl();
        }

        $locale = $this->getSubmittedLocale($locale);
        self::setLocale($locale);

        $this->response->setRedirect($referer);
    }

    /**
     * Gets the submitted locale. if the provided locale is not null, this will be used, else the
     * LocalizePanelForm will be checked.
     * @param string $locale
     * @return string Code of the submitted locale
     * @throws zibo\ZiboException when no locale is provided through an argument or the LocalizePanelForm
     */
    private function getSubmittedLocale($locale) {
        if ($locale !== null) {
            return $locale;
        }

        $form = new LocalizePanelForm();
        if ($form->isSubmitted()) {
            return $form->getLocaleCode();
        }

        throw new ZiboException('No locale provided and the LocalizePanelForm is not submitted');
    }

    /**
     * Sets the locale of the localized content
     * @param string $locale Code of the locale
     * @return null
     */
    public static function setLocale($locale) {
        I18n::getInstance()->getLocale($locale);
        Session::getInstance()->set(self::SESSION_LOCALE, $locale);
    }

    /**
     * Gets the locale of the localized content
     * @return string Code of the locale
     */
    public static function getLocale() {
        $locale = I18n::getInstance()->getLocale();
        return Session::getInstance()->get(self::SESSION_LOCALE, $locale->getCode());
    }

}