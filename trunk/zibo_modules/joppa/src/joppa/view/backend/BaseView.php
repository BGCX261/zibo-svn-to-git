<?php

namespace joppa\view\backend;

use joppa\model\Node;
use joppa\model\Site;

use joppa\form\backend\SiteSelectForm;

use joppa\Module;

use zibo\admin\view\BaseView as AdminBaseView;
use zibo\admin\view\i18n\LocalizePanelView;

use zibo\jquery\Module as JQuery;
use zibo\jquery\contextmenu\Module as JQueryContextMenu;

/**
 * Base view for any Joppa backend view
 */
class BaseView extends AdminBaseView {

    /**
     * Relative path to the stylesheet for this view
     * @var string
     */
    const STYLE = 'web/styles/joppa/joppa.css';

    /**
     * Relative path to the javascript of Joppa
     * @var string
     */
    const SCRIPT_JOPPA = 'web/scripts/joppa.js';

    /**
     * Construct this view
     * @param joppa\form\backend\SiteSelectForm $siteSelectForm
     * @param joppa\model\Site $site the current site (optional)
     * @param joppa\model\Node $node the current node (optional)
     * @param string $template template file to include as content (optional)
     */
    public function __construct(SiteSelectForm $siteSelectForm, Site $site = null, Node $node = null, $template = null) {
        parent::__construct('joppa/backend/index');

        $this->set('_templateContent', $template);

        if ($site) {
            $this->setTitle($site->node->name);
        }

        $sidebar = $this->getSidebar();
        $sidebar->addPanel(new SidebarView($siteSelectForm, $site, $node));

        $this->addJavascript(JQuery::SCRIPT_JQUERY_UI);
        $this->addJavascript(JQueryContextMenu::SCRIPT_JQUERY_CONTEXT_MENU);
        $this->addJavascript(self::SCRIPT_JOPPA);
        $this->addInlineJavascript('joppaInitializeActionMenus();');

        $this->addStyle(JQuery::STYLE_JQUERY_UI);
        $this->addStyle(JQueryContextMenu::STYLE_JQUERY_CONTEXT_MENU);
        $this->addStyle(self::STYLE);
    }

    protected function addTaskbar() {
        $this->taskbar->addNotificationPanel(new LocalizePanelView(true));
        parent::addTaskbar();
    }

}