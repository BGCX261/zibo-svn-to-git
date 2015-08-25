<?php

namespace zibo\notify;

use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\notify\view\NotifyView;

class Module {

    const EVENT_NOTIFY = 'notify';

    public function initialize() {
        Zibo::getInstance()->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    public function prepareTaskbar($taskbar) {
        $notifyView = new NotifyView();

        Zibo::getInstance()->runEvent(self::EVENT_NOTIFY, $notifyView);

        $taskbar->addNotificationPanel($notifyView);
    }

}