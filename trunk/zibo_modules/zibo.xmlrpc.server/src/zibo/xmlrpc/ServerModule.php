<?php

/**
 * @package zibo-xmlrpc
 */
namespace zibo\xmlrpc;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

/**
 * XMLRPC server module initializer
 */
class ServerModule {

    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'addSetting'));
    }

    public function addSetting($taskbar) {
        $translator = I18n::getInstance()->getTranslator();
        $menuSettings = $taskbar->getSettingsMenu();
        $menuSettings->addMenuItem(new MenuItem($translator->translate('xmlrpc.title.server'), 'admin/xmlrpc'));
    }

}