<?php

namespace zibo\user;

use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\security\SecurityManager;

use zibo\user\view\UserSwitchPanelView;

/**
 * Module to enable the user switch UI
 */
class UserSwitchModule {

    /**
     * The route to switch from user
     * @var string
     */
    const ROUTE_USER_SWITCH = 'authentication/switch';

    /**
     * Initialize this module
     * @return null
     */
    public function initialize() {
        Zibo::getInstance()->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'), 99);
    }

    /**
     * Prepares the taskbar by adding the user switch panel to it if allowed
     * @return null
     */
    public function prepareTaskbar($taskbar) {
        if (!SecurityManager::getInstance()->isRouteAllowed(self::ROUTE_USER_SWITCH)) {
            return;
        }

        $taskbar->addNotificationPanel(new UserSwitchPanelView());
    }

}