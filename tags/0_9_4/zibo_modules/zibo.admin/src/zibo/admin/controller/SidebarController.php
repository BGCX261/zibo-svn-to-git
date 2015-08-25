<?php

namespace zibo\admin\controller;

use zibo\library\Session;

use zibo\ZiboException;

/**
 * Controller to get and set the locale of the localized content
 */
class SidebarController extends AbstractController {

    /**
     * Name of the session key for the locale of the localized content
     * @var string
     */
    const SESSION_SIDEBAR_HIDDEN = 'sidebar.hidden';

    /**
     * Action to change the locale of the localized content
     * @param string $locale Code of the locale, if not specified, the LocalizePanelForm should be submitted
     * @return null
     */
    public function indexAction($isHidden = false) {
        self::setIsSidebarVisible(!$isHidden);
    }

    /**
     * Sets the locale of the localized content
     * @param string $locale Code of the locale
     * @return null
     */
    public static function setIsSidebarVisible($flag) {
        Session::getInstance()->set(self::SESSION_SIDEBAR_HIDDEN, !$flag);
    }

    /**
     * Gets the locale of the localized content
     * @return string Code of the locale
     */
    public static function isSidebarVisible() {
        return !Session::getInstance()->get(self::SESSION_SIDEBAR_HIDDEN, false);
    }

}