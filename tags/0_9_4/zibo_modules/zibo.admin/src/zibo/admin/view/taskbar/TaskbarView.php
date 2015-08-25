<?php

namespace zibo\admin\view\taskbar;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the taskbar
 */
class TaskbarView extends SmartyView {

    /**
     * Path of the template file
     * @var string
     */
    const TEMPLATE = 'admin/taskbar';

    /**
     * Path to the JS script of the menu
     */
    const SCRIPT_CLICKMENU = 'web/scripts/admin/jquery.clickmenu.js';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_TASKBAR = 'web/styles/admin/taskbar.css';

    /**
     * Taskbar to render
     * @var Taskbar
     */
    private $taskbar;

    /**
     * Construct this taskbar view
     * @param Taskbar $taskbar
     * @return null
     */
    public function __construct(Taskbar $taskbar) {
        parent::__construct(self::TEMPLATE);

        $this->taskbar = $taskbar;
    }

    /**
     * Render this taskbar view
     * @param boolean $return true to return the rendered view, false to send it to the client
     * @return mixed null when provided $return is set to true; the rendered output when the provided $return is set to false
     */
    public function render($return = true) {
        $request = Zibo::getInstance()->getRequest();
        if (!$request) {
            return;
        }

        $baseUrl = $request->getBaseUrl() . Request::QUERY_SEPARATOR;
        $renderedPanels = array();

        $notificationPanels = $this->taskbar->getNotificationPanels();
        foreach ($notificationPanels as $panel) {
            $renderedPanels[] = $this->renderView($panel);
        }

        $applicationsMenu = $this->taskbar->getApplicationsMenu();
        $applicationsMenu->setBaseUrl($baseUrl);

        $settingsMenu = $this->taskbar->getSettingsMenu();
        $settingsMenu->setBaseUrl($baseUrl);
        $settingsMenu->orderItems();

        $this->set('applicationsMenu', $applicationsMenu);
        $this->set('settingsMenu', $settingsMenu);
        $this->set('notificationPanels', array_reverse($renderedPanels));
        $this->set('title', $this->taskbar->getTitle());

        $this->addStyle(self::STYLE_TASKBAR);
        $this->addJavascript(self::SCRIPT_CLICKMENU);
        $this->addInlineJavascript(
            "$('#taskbarApplications').clickMenu({start: 'left'}); \n \t\t\t\t" .
            "$('#taskbarSettings').clickMenu({start: 'right'});"
        );

        return parent::render($return);
    }

}