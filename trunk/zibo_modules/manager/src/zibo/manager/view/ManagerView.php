<?php

namespace zibo\manager\view;

use zibo\admin\view\BaseView;

use zibo\core\View;

/**
 * Base view for a manager
 */
class ManagerView extends BaseView {

    /**
     * Template file of this view
     * @var string
     */
    const TEMPLATE = 'manager/index';

    /**
     * Construct a new base view
     * @param zibo\core\View $managerView
     * @param string $managerName
     * @return null
     */
    public function  __construct(View $managerView = null, $managerName = null) {
        parent::__construct(self::TEMPLATE);

        $sidebar = $this->getSidebar();
        $sidebar->addPanel(new SidebarView($managerName));

        if ($managerView) {
            $this->setSubview('manager', $managerView);
        }
    }

}