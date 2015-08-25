<?php

namespace zibo\database\admin;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

/**
 * Database administration module initializer
 */
class Module {

    /**
     * Route to the administration of the database connections
     * @var string
     */
    const ROUTE_ADMIN = 'admin/database';

    /**
     * Translation key for the menu item
     * @var string
     */
    const TRANSLATION_ADMIN = 'database.title.connections';

    /**
     * Initializes the database administration module
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Adds the database administration menu item to the taskbar
     * @param zibo\admin\view\taskbar\Taskbar $taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $translator = I18n::getInstance()->getTranslator();
        $settingsMenu = $taskbar->getSettingsMenu();
        $systemMenu = $settingsMenu->getItem($translator->translate(BaseView::TRANSLATION_TASKBAR_SYSTEM));
        $systemMenu->addMenuItem(new MenuItem($translator->translate(self::TRANSLATION_ADMIN), self::ROUTE_ADMIN));
    }

}