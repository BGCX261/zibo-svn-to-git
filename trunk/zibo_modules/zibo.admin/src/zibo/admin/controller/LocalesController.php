<?php

namespace zibo\admin\controller;

use zibo\core\Zibo;

use zibo\admin\view\i18n\LocalesView;

use zibo\library\i18n\I18n;
use zibo\library\i18n\locale\Manager;

/**
 * Controller to manage the locales
 */
class LocalesController extends AbstractController {

    /**
     * Translation key of the title
     * @var string
     */
    const TRANSLATION_TITLE = 'locales.title';

    /**
     * Action to show an overview of the current locales
     * @return null
     */
    public function indexAction() {
        $locales = I18n::getInstance()->getAllLocales();

        $translator = $this->getTranslator();

        $view = new LocalesView($this->request->getBasePath() . '/order', $locales);
        $view->setPageTitle($translator->translate(self::TRANSLATION_TITLE));

        $this->response->setView($view);
    }

    /**
     * Action to reorder the preference of the locales
     * @return null
     */
    public function orderAction() {
        if (!isset($_POST['locale'])) {
            return;
        }

        $order = $_POST['locale'];
        $i18n = I18n::getInstance();

        foreach ($order as $localeCode) {
            // exception is thrown by getLocale() if locale with the specified code is not found
            $locale = $i18n->getLocale($localeCode);
        }

        $localeOrder = implode(',', $order);

        Zibo::getInstance()->setConfigValue(Manager::CONFIG_LOCALE_ORDER, $localeOrder);
    }

}