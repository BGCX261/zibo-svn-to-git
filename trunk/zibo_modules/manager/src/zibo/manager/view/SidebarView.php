<?php

namespace zibo\manager\view;

use zibo\manager\model\ManagerModel;
use zibo\manager\Module;

use zibo\library\security\SecurityManager;
use zibo\library\smarty\view\SmartyView;

/**
 * Sidebar view of the manager module
 */
class SidebarView extends SmartyView {

    /**
     * Template of this view
     * @var string
     */
    const TEMPLATE = 'manager/sidebar';

    /**
     * Constructs a new sidebar view
     * @param string $managerName name of the current manager
     * @return null
     */
    public function  __construct($managerName = null) {
        parent::__construct(self::TEMPLATE);

        $managers = ManagerModel::getInstance()->getManagers();
        $managers = $this->getViewManagers($managers, $managerName);

        $this->set('managers', $managers);

        $this->addStyle('web/styles/manager.css');
    }

    /**
     * Gets a display array of the provided managers
     * @param array $managers the managers
     * @param string $currentManager name of the current manager
     * @return array Array with the name of the manager as key and a manager array as value.
     *
     * A manager array is an array with the following keys:
     * <ul>
     *      <li>action: string</li>
     *      <li>name: string</li>
     *      <li>current: boolean</li>
     *      <li>icon: string</li>
     * </ul>
     */
    private function getViewManagers(array $managers, $currentManager = null) {
        $basePath = Module::getManagerBasePath();
        $viewManagers = array();

        $securityManager = SecurityManager::getInstance();

        foreach ($managers as $name => $manager) {
            $viewManager = array();

            $actions = $manager->getActions();
            if ($actions) {
                $viewManager['actions'] = array();
                foreach ($actions as $route => $label) {
                    $route = $basePath . $name . '/' . $route;
                    if ($securityManager->isRouteAllowed($route)) {
                        $viewManager['actions'][$route] = $label;
                    }
                }
                if (empty($viewManager['actions'])) {
                    continue;
                }
            }

            $route = $basePath . $name;
            if (!$securityManager->isRouteAllowed($route) && empty($viewManager['actions'])) {
                continue;
            }
            $viewManager['action'] = $route;

            $viewManager['name'] = $manager->getName();
            $viewManager['current'] = $currentManager == $name;
            $viewManager['icon'] = $manager->getIcon();
            if (!$viewManager['icon']) {
                $viewManager['icon'] = 'web/images/manager.png';
            }

            $viewManagers[$viewManager['name']] = $viewManager;
        }

        ksort($viewManagers);

        return $viewManagers;
    }

}