<?php

namespace zibo\dashboard;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

/**
 * Dashboard module initializer
 */
class Module {

    /**
     * The javascript for the dashboard application
     * @var string
     */
    const SCRIPT_DASHBOARD = 'web/scripts/dashboard/dashboard.js';

    /**
     * The style for the dashboard application
     * @var unknown_type
     */
    const STYLE_DASHBOARD = 'web/styles/dashboard/dashboard.css';

    /**
     * Initialize the dashboard application
     * @return nulll
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Prepare the taskbar by adding the dashboard application to it
     * @param zibo\admin\view\taskbar\Taskbar $taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $translator = I18n::getInstance()->getTranslator();
        $applicationsMenu = $taskbar->getApplicationsMenu();
        $applicationsMenu->addMenuItem(new MenuItem($translator->translate('dashboard.title'), 'dashboard'));
    }

}