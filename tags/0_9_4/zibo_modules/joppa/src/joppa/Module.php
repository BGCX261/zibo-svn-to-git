<?php

namespace joppa;

use joppa\router\JoppaRouter;

use zibo\admin\view\taskbar\MenuItem;
use zibo\admin\view\taskbar\Taskbar;
use zibo\admin\view\BaseView;

use zibo\core\Zibo;

use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\SimpleCache;
use zibo\library\filesystem\File;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;

/**
 * Joppa module object
 */
class Module {

    /**
     * Path for the Joppa cache
     * @var string
     */
    const CACHE_PATH = 'application/data/cache/joppa';

    /**
     * Type for the cache to store the node dispatchers to
     * @var string
     */
    const CACHE_TYPE_NODE_DISPATCHER = 'NodeDispatcher';

    /**
     * Type for the cache to store the node settings to
     * @var string
     */
    const CACHE_TYPE_NODE_SETTINGS = 'NodeSettings';

    /**
     * Type for the cache to store the node trees to
     * @var string
     */
    const CACHE_TYPE_NODE_TREE = 'NodeTree';

    /**
     * Type for the cache to store the node widgets to
     * @var string
     */
    const CACHE_TYPE_NODE_WIDGETS = 'NodeWidgets';

    /**
     * Type for the cache to store the view of a node widget to
     * @var string
     */
    const CACHE_TYPE_NODE_WIDGET_VIEW = 'NodeWidgetView';

    /**
     * Type for the cache to store the list of sites indexed on their base URL
     * @var string
     */
    const CACHE_TYPE_SITE_URLS = 'siteUrls';

    /**
     * Type for the cache to store the widget nodes to
     * @var string
     */
    const CACHE_TYPE_WIDGET_NODES = 'WidgetNodes';

    /**
     * Name of the event to clear the joppa cache
     * @var string
     */
    const EVENT_CLEAR_JOPPA_CACHE = 'joppa.cache.clear';

    /**
     * Name of the permission to create and display the taskbar in the frontend
     * @var string
     */
    const PERMISSION_TASKBAR = 'joppa.taskbar';

    /**
     * Route of the Joppa settings
     * @var string
     */
    const ROUTE_ADMIN = 'admin/joppa';

    /**
     * Route of the ajax tree controller
     * @var string
     */
    const ROUTE_AJAX_TREE = 'ajax/joppa/tree';

    /**
     * Route of the Joppa backend
     * @var string
     */
    const ROUTE_JOPPA = 'pages';

    /**
     * Route of a node
     * @var string
     */
    const ROUTE_NODE = 'node';

    /**
     * Translation key for the menu item of the Joppa application
     * @var string
     */
    const TRANSLATION_APPLICATION = 'joppa.title.pages';

    /**
     * Translation key for the menu item of the Joppa settings
     * @var string
     */
    const TRANSLATION_SETTINGS = 'joppa.title.settings';

    /**
     * Prefix of the translation key of a node type
     * @var string
     */
    const TRANSLATION_NODE_TYPE_PREFIX = 'joppa.node.type.';

    /**
     * The Joppa cache
     * @var zibo\library\cache\Cache
     */
    private static $cache;

    /**
     * Initialize the Joppa module
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(Zibo::EVENT_PRE_ROUTE, array($this, 'setJoppaRouter'));
        $zibo->registerEventListener(Zibo::EVENT_CLEAR_CACHE, array($this, 'clearCache'));
        $zibo->registerEventListener(self::EVENT_CLEAR_JOPPA_CACHE, array($this, 'clearCache'));
        $zibo->registerEventListener(BaseView::EVENT_TASKBAR, array($this, 'prepareTaskbar'));
    }

    /**
     * Set the Joppa router over the already assigned router
     * @return null
     */
    public function setJoppaRouter() {
    	$zibo = Zibo::getInstance();

    	$router = $zibo->getRouter();
    	$router = new JoppaRouter($router);

    	$zibo->setRouter($router);
    }

    /**
     * Add the menu item for the Joppa application to the taskbar
     * @param zibo\admin\view\taskbar\Taskbar $taskbar
     * @return null
     */
    public function prepareTaskbar(Taskbar $taskbar) {
        $translator = I18n::getInstance()->getTranslator();

        $menu = $taskbar->getApplicationsMenu();
        $menu->addMenuItem(new MenuItem($translator->translate(self::TRANSLATION_APPLICATION), self::ROUTE_JOPPA));

        $menu = $taskbar->getSettingsMenu();
        $menu->addMenuItem(new MenuItem($translator->translate(self::TRANSLATION_SETTINGS), self::ROUTE_ADMIN));
    }

    /**
     * Clear the Joppa cache
     * @return null
     */
    public function clearCache() {
        $cache = self::getCache();
        $cache->clear();
    }

    /**
     * Get the Joppa cache
     * @return zibo\library\cache\Cache
     */
    public static function getCache() {
        if (self::$cache) {
            return self::$cache;
        }

        $io = new FileCacheIO(new File(self::CACHE_PATH));
        self::$cache = new SimpleCache($io);

        return self::$cache;
    }

}