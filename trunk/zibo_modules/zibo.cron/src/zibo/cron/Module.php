<?php

namespace zibo\cron;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

/**
 * API module initializer
 */
class Module {

    /**
     * Route to the cron overview
     * @var string
     */
    const ROUTE_CRON = 'admin/cron';

    /**
     * Translation key of the title of the cron module
     * @var string
     */
    const TRANSLATION_CRON = 'cron.title';

    /**
     * Initialize the API module for a request
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Prepare the taskbar for the API module
     * @param zibo\library\admin\view\taskbar\Taskbar $taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $translator = I18n::getInstance()->getTranslator();
        $settingsMenu = $taskbar->getSettingsMenu();
        $settingsMenu->addMenuItem(new MenuItem($translator->translate(self::TRANSLATION_CRON), self::ROUTE_CRON));
    }

}