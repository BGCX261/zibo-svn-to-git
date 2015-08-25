<?php

namespace zibo\orm\security;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

/**
 * Initializer of the ORM security module
 */
class Module {

    /**
     * Route to the permissions administration
     * @var string
     */
    const ROUTE_PERMISSIONS = 'admin/permissions';

    /**
     * Route to the roles administration
     * @var string
     */
    const ROUTE_ROLES = 'admin/roles';

    /**
     * Route to the users administration
     * @var string
     */
    const ROUTE_USERS = 'admin/users';

    /**
     * Translation key for the title of the permissions administration
     * @var string
     */
    const TRANSLATION_PERMISSIONS = 'orm.security.title.permissions';

    /**
     * Translation key for the title of the roles administration
     * @var string
     */
    const TRANSLATION_ROLES = 'orm.security.title.roles';

    /**
     * Translation key for the title of the users administration
     * @var string
     */
    const TRANSLATION_USERS = 'orm.security.title.users';

    /**
     * Translation key for the back button
     * @var string
     */
    const TRANSLATION_BACK = 'orm.security.button.back';

    /**
     * Initializes the ORM security module
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance()->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Adds the users administration menu item to the taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $translator = I18n::getInstance()->getTranslator();
        $menuSettings = $taskbar->getSettingsMenu();
        $menuSettings->addMenuItem(new MenuItem($translator->translate(self::TRANSLATION_USERS), self::ROUTE_USERS));
    }

}