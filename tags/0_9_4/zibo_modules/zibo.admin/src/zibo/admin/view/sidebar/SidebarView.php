<?php

namespace zibo\admin\view\sidebar;

use zibo\admin\controller\SidebarController;
use zibo\admin\Module;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\smarty\view\SmartyView;

/**
 * View for a sidebar
 */
class SidebarView extends SmartyView {

    /**
     * Path to the template of this view
     * @var unknown_type
     */
    const TEMPLATE = 'admin/sidebar';

    /**
     * Path to the JS script of this view
     * @var string
     */
    const SCRIPT_SIDEBAR =  'web/scripts/admin/sidebar.js';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_SIDEBAR =  'web/styles/admin/sidebar.css';

    /**
     * The sidebar to render
     * @var Sidebar
     */
    private $sidebar;

    /**
     * Constructs a new sidebar view
     * @param Sidebar $sidebar
     * @return null
     */
    public function __construct(Sidebar $sidebar) {
        parent::__construct(self::TEMPLATE);

        $this->sidebar = $sidebar;
    }

    /**
     * Renders the sidebar
     * @param boolean $return True to return the rendered string, false to output it directly
     * @return null|string
     */
    public function render($return = true) {
        $renderedPanels = array();

        $panels = $this->sidebar->getPanels();
        foreach ($panels as $panel) {
            $renderedPanels[] = $this->renderView($panel);
        }

        $this->set('actions', $this->sidebar->getActions());
        $this->set('panels', $renderedPanels);
        $this->set('information', $this->sidebar->getInformation());

        $request = Zibo::getInstance()->getRequest();

        $baseUrl = $request->getBaseUrl() . Request::QUERY_SEPARATOR;
        $sidebarAction = $baseUrl . Module::ROUTE_SIDEBAR . Request::QUERY_SEPARATOR;

        $isVisible = SidebarController::isSidebarVisible() ? 'true' : 'false';

        $this->addStyle(self::STYLE_SIDEBAR);
        $this->addJavascript(self::SCRIPT_SIDEBAR);
        $this->addInlineJavascript("ziboAdminInitializeSidebar(" . $isVisible . ", '" . $sidebarAction . "');");

        return parent::render($return);
    }

}