<?php

namespace zibo\filebrowser;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

/**
 * File browser module initializer
 */
class Module  {

    /**
     * Route to the file browser
     * @var string
     */
    const ROUTE_FILE_BROWSER = 'filebrowser';

    /**
     * Translation key for the title of the file browser
     * @var string
     */
    const TRANSLATION_FILE_BROWSER = 'filebrowser.title';

    /**
     * Initializes the file browser
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Adds the file browser to the application menu
     * @param zibo\admin\view\taskbar\Taskbar $taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $translator = I18n::getInstance()->getTranslator();
        $menuApplications = $taskbar->getApplicationsMenu();
        $menuApplications->addMenuItem(new MenuItem($translator->translate(self::TRANSLATION_FILE_BROWSER), self::ROUTE_FILE_BROWSER));
    }

}