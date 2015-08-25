<?php

namespace zibo\xmlrpc;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

/**
 * XML-RPC client module initializer
 */
class ClientModule {

    /**
     * Registers the addApplication method to the taskbar event
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'addApplication'));
    }

    /**
     * Adds a menu item for the XML-RPC client to the taskbar
     * @param zibo\admin\view\taskbar\Taskbar $taskbar The taskbar
     * @return null
     */
    public function addApplication($taskbar) {
        $translator = I18n::getInstance()->getTranslator();
        $applicationsMenu = $taskbar->getApplicationsMenu();
        $applicationsMenu->addMenuItem(new MenuItem($translator->translate('xmlrpc.title.client'), 'xmlrpc/client'));
    }

}