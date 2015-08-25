<?php

namespace joppa\view\frontend;

use joppa\model\Node;
use joppa\model\NodeSettingModel;

use joppa\Module;

use zibo\admin\view\BaseView;

use zibo\core\View;

use zibo\library\html\meta\Meta;
use zibo\library\security\SecurityManager;
use zibo\library\smarty\resource\ThemedResourceHandler;
use zibo\library\smarty\view\SmartyView;

/**
 * Frontend view for a node
 */
class NodeView extends BaseView {

    /**
     * The node of this view
     * @var joppa\model\Node
     */
    protected $node;

    /**
     * The dispatched views of the node
     * @var array
     */
    protected $dispatchedViews;

    /**
     * Construct this view
     * @param joppa\model\Node $node the node which is to be rendered
     * @param string $title title of the site (optional)
     * @return null
     */
    public function __construct(Node $node, $title = null) {
        $theme = $node->getTheme();

        $template = $theme->getTemplate();

        parent::__construct(null, $template);

        $resourceHandler = new ThemedResourceHandler($theme->getName());
        $this->setResourceHandler($resourceHandler);

        $style = $theme->getStyle();
        if ($style) {
            $this->addStyle($style);
        }

        if ($title) {
            $this->setTitle($title);
            $this->setPageTitle($node->name);
        } else {
            $this->setTitle($node->name);
        }

        $localeSuffix = '.' . $node->dataLocale;

        $metaDescription = $node->settings->get(NodeSettingModel::SETTING_META_DESCRIPTION . $localeSuffix);
        if ($metaDescription) {
        	$this->addMeta(new Meta(Meta::DESCRIPTION, $metaDescription));
        }
        $metaKeywords = $node->settings->get(NodeSettingModel::SETTING_META_KEYWORDS . $localeSuffix);
        if ($metaKeywords) {
        	$this->addMeta(new Meta(Meta::KEYWORDS, $metaKeywords));
        }

        $this->node = $node;
    }

    /**
     * Set the dispatched views of the node to this view
     * @param array $dispatchedViews Array with region name as key and a widget array as value. The widget array has the widget id as key and the widget View as value.
     * @return null
     */
    public function setDispatchedViews(array $dispatchedViews) {
        $this->dispatchedViews = $dispatchedViews;
    }

    /**
     * Render the page
     * @param boolean $return true to get the rendered view back, false to output the rendered view
     * @return string the rendered view if $return is set to true
     */
    public function render($return = true) {
        $regions = array();

        if ($this->dispatchedViews) {
            foreach ($this->dispatchedViews as $regionName => $widgetViews) {
                $regions[$regionName] = array();

                foreach ($widgetViews as $widgetId => $widgetView) {
                    if (!$widgetView) {
                        continue;
                    }

                    $this->resourceHandler->setTemplateId($widgetId);

                    $this->processSmartyView($widgetView, $widgetId, $regionName);

                    $regions[$regionName][$widgetId] = $this->renderView($widgetView);
                }
            }
        }

        $this->resourceHandler->setTemplateId($this->node->id);
        $this->getEngine()->compile_id = $this->node->id;

        $this->set('regions', $regions);
        $this->set('node', $this->node);

        return parent::render($return);
    }

    /**
     * Process a smarty view, set the resource handler of this page and set the region name
     * @param zibo\core\View $widgetView view of the widget
     * @param int $widgetId id of the widget
     * @param string $regionName name of the region
     * @return null
     */
    protected function processSmartyView(View $widgetView, $widgetId, $regionName) {
        if (!$widgetView instanceof SmartyView) {
            return;
        }

        $widgetView->setResourceHandler($this->resourceHandler);
        $widgetView->getEngine()->compile_id = $widgetId;

        $widgetView->set('_widgetId', $widgetId);
        $widgetView->set('_region', $regionName);
        $widgetView->set('_node', $this->node);

        $widgetSubviews = $widgetView->getSubviews();
        foreach ($widgetSubviews as $widgetSubview) {
            $this->processSmartyView($widgetSubview, $widgetId, $regionName);
        }
    }

    /**
     * Protect the taskbar with the joppa.taskbar permission
     * @return null
     */
    protected function addTaskbar() {
        if (SecurityManager::getInstance()->isPermissionAllowed(Module::PERMISSION_TASKBAR)) {
            parent::addTaskbar();
        }
    }

}