<?php

namespace zibo\admin\view;

use zibo\admin\controller\LocalesController;
use zibo\admin\controller\ModulesController;
use zibo\admin\controller\SecurityController;
use zibo\admin\controller\SystemController;
use zibo\admin\view\sidebar\Sidebar;
use zibo\admin\view\sidebar\SidebarView;
use zibo\admin\view\taskbar\Menu;
use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\taskbar\TaskbarView;
use zibo\admin\view\security\UserPanelView;
use zibo\admin\Module;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;
use zibo\library\security\SecurityManager;

use zibo\jquery\Module as JQueryModule;

use zibo\library\optimizer\CssOptimizer;
use zibo\library\optimizer\JsOptimizer;
use zibo\library\smarty\view\SmartyView;
use zibo\library\Boolean;

/**
 * Base admin view
 */
class BaseView extends SmartyView {

    /**
     * Configuration key for the title in the taskbar
     * @var string
     */
    const CONFIG_TITLE = 'admin.view.title';

    /**
     * Configuration key to enable/disable the CSS optimizer
     * @var string
     */
    const CONFIG_OPTIMIZE_CSS = 'admin.view.optimize.css';

    /**
     * Configuration key to enable/disable the JS optimizer
     * @var string
     */
    const CONFIG_OPTIMIZE_JS = 'admin.view.optimize.js';

    /**
     * Default template for the base
     * @var string
     */
    const DEFAULT_TEMPLATE = 'admin/index';

    /**
     * Default title in the taskbar
     * @var string
     */
    const DEFAULT_TITLE = 'Zibo';

    /**
     * Default value to enable/disable the CSS optimizer
     * @var boolean
     */
    const DEFAULT_OPTIMIZE_CSS = true;

    /**
     * Default value to enable/disable the JS optimizer
     * @var boolean
     */
    const DEFAULT_OPTIMIZE_JS = true;

    /**
     * Name of the event to process the taskbar
     * @var string
     */
    const EVENT_TASKBAR = 'admin.taskbar';

    /**
     * Path to the JS for a table
     * @var string
     */
    const SCRIPT_TABLE = 'web/scripts/admin/table.js';

    /**
     * Path to the CSS for this view
     * @var string
     */
    const STYLE_BASE = 'web/styles/admin/style.css';

    /**
     * Path to the general IE style
     * @var string
     */
    const STYLE_IE = 'web/styles/ie.css';

    /**
     * Path to the style of IE 6
     * @var string
     */
    const STYLE_IE6 = 'web/styles/ie6.css';

    /**
     * Path to the style of IE 7
     * @var string
     */
    const STYLE_IE7 = 'web/styles/ie7.css';

    /**
     * Condition for the IE style
     * @var string
     */
    const STYLE_CONDITION_IE = 'IE';

    /**
     * Condition for the IE 6 style
     * @var string
     */
    const STYLE_CONDITION_IE6 = 'lte IE 6';

    /**
     * Condition for the IE7 style
     * @var string
     */
    const STYLE_CONDITION_IE7 = 'lte IE 7';

    /**
     * Translation key for the system menu in the taskbar
     * @var string
     */
    const TRANSLATION_TASKBAR_SYSTEM = 'taskbar.system';

    /**
     * Title of the site
     * @var string
     */
    private $title;

    /**
     * Title of the page
     * @var string
     */
    private $pageTitle;

    /**
     * The taskbar of the view
     * @var zibo\admin\view\taskbar\Taskbar
     */
    protected $taskbar;

    /**
     * The sidebar of the view
     * @var zibo\admin\view\sidebar\Sidebar
     */
    protected $sidebar;

    /**
     * Constructs a new base view
     * @param string $contentTemplate Name of the content template
     * @param string $baseTemplate Name of the base template
     * @return null
     */
    public function __construct($contentTemplate = null, $baseTemplate = null) {
        if (!$baseTemplate) {
            $baseTemplate = self::DEFAULT_TEMPLATE;
        }

        parent::__construct($baseTemplate);

        $this->title = Zibo::getInstance()->getConfigValue(self::CONFIG_TITLE, self::DEFAULT_TITLE);
        $this->sidebar = new Sidebar();
        $this->taskbar = new Taskbar();
        $this->taskbar->setTitle($this->title);

        if ($contentTemplate) {
            $this->set('contentTemplate', $contentTemplate);
        }

        $this->set('_locale', I18n::getInstance()->getLocale()->getCode());

        $this->addStyle(self::STYLE_BASE);

        $this->addJavascript(JQueryModule::SCRIPT_JQUERY);
    }

    public function getTaskbar() {
        return $this->taskbar;
    }

    /**
     * Gets the sidebar of this view
     * @return zibo\admin\view\sidebar\Sidebar
     */
    public function getSidebar() {
        return $this->sidebar;
    }

    /**
     * Sets the title
     * @param string $title
     * @param boolean $translate Set to true to translate the title string
     * @return null
     */
    public function setTitle($title, $translate = false) {
        if ($translate) {
            $title = $this->getTranslator()->translate($title);
        }

        $this->title = $title;
    }

    /**
     * Gets the title
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title of the page
     * @param string $title
     * @param boolean $translate Set to true to translate the title string
     * @return null
     */
    public function setPageTitle($title, $translate = false, array $variables = null) {
        if ($translate) {
            $title = $this->getTranslator()->translate($title, $variables);
        }

        $this->pageTitle = $title;
    }

    /**
     * Gets the title of the page
     * @return string
     */
    public function getPageTitle() {
        return $this->pageTitle;
    }

    /**
     * Sets the title of the taskbar
     * @param string $title
     * @param boolean $translate Set to true to translate the title string
     * @return null
     */
    public function setTaskbarTitle($title, $translate = false, array $variables = null) {
        if ($translate) {
            $title = $this->getTranslator()->translate($title, $variables);
        }

        $this->taskbar->setTitle($title);
    }

    /**
     * Gets the title of the taskbar
     * @return string
     */
    public function getTaskbarTitle() {
        return $this->taskbar->getTitle();
    }

    /**
     * Renders this view
     * @param boolean $return True to return the rendered view, false to write it to the output
     * @return null|string
     */
    public function render($return = true) {
        $this->addTaskbar();
        $this->addSidebar();

        $this->set('_stylesIE', $this->getStylesForIE());
        $this->set('_title', $this->title);
        $this->set('_pageTitle', $this->pageTitle);

        return parent::render($return);
    }

    /**
     * Prepares the taskbar and adds it to the view
     * @return null
     */
    protected function addTaskbar() {
        $securityModel = SecurityManager::getInstance()->getSecurityModel();
        $translator = $this->getTranslator();

        $systemMenu = new Menu($translator->translate(self::TRANSLATION_TASKBAR_SYSTEM));
        $systemMenu->addMenuItem(new MenuItem($translator->translate(LocalesController::TRANSLATION_TITLE), Module::ROUTE_LOCALES));
        $systemMenu->addMenuItem(new MenuItem($translator->translate(ModulesController::TRANSLATION_TITLE), Module::ROUTE_MODULES));
        if ($securityModel) {
            $systemMenu->addMenuItem(new MenuItem($translator->translate(SecurityController::TRANSLATION_TITLE), Module::ROUTE_SECURITY));
        }
        $systemMenu->addMenuItem(new MenuItem($translator->translate(SystemController::TRANSLATION_TITLE), Module::ROUTE_SYSTEM));

        $settingsMenu = $this->taskbar->getSettingsMenu();
        $settingsMenu->addMenu($systemMenu);

        Zibo::getInstance()->runEvent(self::EVENT_TASKBAR, $this->taskbar);

        if ($securityModel) {
            $this->taskbar->addNotificationPanel(new UserPanelView());
        }

        $view = new TaskbarView($this->taskbar);
        $this->setSubview('taskbar', $view);
    }

    /**
     * Adds the sidebar to the view if the sidebar is populated
     * @return null
     */
    private function addSidebar() {
        if (!$this->sidebar || (!$this->sidebar->hasActions() && !$this->sidebar->hasPanels() && !$this->sidebar->hasInformation())) {
            return;
        }

        $view = new SidebarView($this->sidebar);
        $this->setSubview('sidebar', $view);
    }

    /**
     * Runs the CSS and JS optimizers if enabled through the Zibo configuration
     * @return null
     */
    protected function preRender() {
        $zibo = Zibo::getInstance();

        $optimizeCss = $zibo->getConfigValue(self::CONFIG_OPTIMIZE_CSS, self::DEFAULT_OPTIMIZE_CSS);
        if (Boolean::getBoolean($optimizeCss) && !empty($this->styles)) {
            $optimizer = new CssOptimizer();
            $style = $optimizer->optimize($this->styles);
            $this->styles = array($style => $style);
        }

        $optimizeJs = $zibo->getConfigValue(self::CONFIG_OPTIMIZE_JS, self::DEFAULT_OPTIMIZE_JS);
        if (Boolean::getBoolean($optimizeJs) && !empty($this->scripts)) {
            $optimizer = new JsOptimizer();
            $script = $optimizer->optimize($this->scripts);
            $this->scripts = array($script => $script);
        }
    }

    /**
     * Gets the style sheets for IE
     * @return array Array with the condition as key and the path to the style sheet as value
     */
    private function getStylesForIE() {
        $zibo = Zibo::getInstance();
        $optimizer = new CssOptimizer();

        $styles = array();
        $styles[self::STYLE_CONDITION_IE] = $this->getStyleForIE($zibo, $optimizer, self::STYLE_IE);
        $styles[self::STYLE_CONDITION_IE6] = $this->getStyleForIE($zibo, $optimizer, self::STYLE_IE6);
        $styles[self::STYLE_CONDITION_IE7] = $this->getStyleForIE($zibo, $optimizer, self::STYLE_IE7);

        return $styles;
    }

    /**
     * Gets the style for
     * @param zibo\core\Zibo $zibo
     * @param zibo\library\optimizer\CssOptimizer $optimizer
     * @param string $path Path of the CSS file in the Zibo filesystem structure
     * @return string Path to the style
     */
    private function getStyleForIE(Zibo $zibo, CssOptimizer $optimizer, $path) {
        $styles = $zibo->getFiles($path);
        if (!$styles) {
            return null;
        }

        return $optimizer->optimize($styles);
    }

    /**
     * Easy access to the translator
     * @return zibo\library\i18n\translation\Translator
     */
    protected function getTranslator() {
        return I18n::getInstance()->getTranslator();
    }

}