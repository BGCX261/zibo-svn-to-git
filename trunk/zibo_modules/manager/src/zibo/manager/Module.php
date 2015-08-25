<?php

namespace zibo\manager;

use zibo\admin\view\taskbar\Menu;
use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;

use zibo\manager\model\ManagerModel;

/**
 * Manager module functions
 */
class Module {

    /**
     * Route to the manager
     * @var string
     */
    const ROUTE_MANAGER = 'manager';

    /**
     * Translation key for the manage button
     * @var string
     */
    const TRANSLATION_MANAGE = 'manager.button.manage';

    /**
     * Initialize the manager module for a request
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Adds the manager menu to the application menu on the provided taskbar
     * @param zibo\admin\view\taskbar\Taskbar $taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $managerModel = ManagerModel::getInstance();

        $basePath = self::getManagerBasePath();
        $managerActions = $managerModel->getManagerMenuActions($basePath);
        if (!$managerActions) {
            return;
        }

        $translator = I18n::getInstance()->getTranslator();
        $managerLabel = $translator->translate(self::TRANSLATION_MANAGE);
        $managerMenu = $this->createMenuFromActions($managerLabel, $managerActions);

        $applicationsMenu = $taskbar->getApplicationsMenu();
        $applicationsMenu->addMenu($managerMenu);
    }

    /**
     * Creates a menu from an array of actions
     * @param string $label Label for the menu
     * @param array $actions Array with the action label as key and the action path or another action array as value
     * @return zibo\admin\view\taskbar\Menu;
     */
    private function createMenuFromActions($label, array $actions) {
        $menu = new Menu($label);

        foreach ($actions as $label => $action) {
            if (is_array($action)) {
                $actionMenu = $this->createMenuFromActions($label, $action);
                $menu->addMenu($actionMenu);
            } else {
                $menu->addMenuItem(new MenuItem($label, $action));
            }
        }

        return $menu;
    }

    /**
     * Gets the base url of the manager module
     * @return string
     */
    public static function getManagerBasePath() {
        $request = Zibo::getInstance()->getRequest();
        return $request->getBaseUrl() . '/' . self::ROUTE_MANAGER . '/';
    }

}