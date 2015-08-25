<?php

namespace joppa\view\backend;

use joppa\controller\backend\action\NodeActionManager;
use joppa\controller\backend\AjaxTreeController;

use joppa\form\backend\SiteSelectForm;

use joppa\model\Node;
use joppa\model\NodeModel;
use joppa\model\NodeTypeFacade;
use joppa\model\Site;
use joppa\model\SiteModel;

use joppa\Module;

use zibo\admin\controller\LocalizeController;

use zibo\core\view\HtmlView;
use zibo\core\Zibo;

use zibo\library\i18n\I18n;
use zibo\library\html\Anchor;
use zibo\library\html\Image;
use zibo\library\orm\ModelManager;
use zibo\library\String;

/**
 * View for the node tree of a site
 */
class TreeView extends HtmlView {

	/**
	 * The site to generate a tree of
	 * @var joppa\model\Site
	 */
	private $site;

	/**
	 * The selected node of the site
	 * @var joppa\model\Node
	 */
	private $node;

    /**
     * Base url for generating the tree
     * @var string
     */
    private $baseUrl;

    /**
     * Locale code of the current locale
     * @var string
     */
    private $locale;

    /**
     * Translator of the current locale
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * The facade to the node types
     * @var joppa\model\NodeTypeFacade
     */
    private $nodeTypeFacade;

    /**
     * Construct this view
     * @param joppa\model\Site $site the current site (optional)
     * @param joppa\model\Node $node the current node (optional)
     * @return null
     */
    public function __construct(Site $site, Node $node = null, $orderUrl = null) {
    	$this->site = $site;
    	$this->node = $node;
    }

    /**
     * Render this view
     * @param boolean $return Set to false to output the rendered view
     * @return null|string The rendered view if $return is set to true, null otherwise
     */
    public function render($return = true) {
        $this->baseUrl = Zibo::getInstance()->getRequest()->getBaseUrl() . '/';
        $this->nodeTypeFacade = NodeTypeFacade::getInstance();
        $this->locale = LocalizeController::getLocale();
        $this->translator = I18n::getInstance()->getTranslator();

        $defaultNodeId = null;
        if ($this->site->defaultNode) {
            $defaultNodeId = $this->site->defaultNode->id;
        }

        $siteModel = ModelManager::getInstance()->getModel(SiteModel::NAME);
        $this->site->node->children = $siteModel->getNodeTreeForSite($this->site, null, null, $this->locale, true);

        $nodeTree = array($this->site->node->id => $this->site->node);
        $output = $this->getNodeTreeHtml($nodeTree, $defaultNodeId, $this->site->localizationMethod == SiteModel::LOCALIZATION_METHOD_COPY, $this->node);

        if ($return) {
        	return $output;
        }

        echo $output;
    }

    /**
     * Get the html of the node tree
     * @param array $nodeTree array with Node objects
     * @param int $defaultNodeId id of the node of the default page
     * @param joppa\model\Node $selectedNode the current node in the ui
     * @param boolean $addUnlocalizedClass Set to true to add the unlocalized class to nodes which are not localized in the current locale
     * @return string html representation of the nodes
     */
    private function getNodeTreeHtml(array $nodeTree, $defaultNodeId, $addUnlocalizedClass, Node $selectedNode = null) {
        $this->actions = NodeActionManager::getInstance()->getActions();

        $html = '<ul id="nodeTree">';
        foreach ($nodeTree as $node) {
            $html .= $this->getNodeHtml($node, $defaultNodeId, $addUnlocalizedClass, $selectedNode);
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Get the HTML of a node
     * @param joppa\model\Node $node the node to render
     * @param int $defaultNodeId id of the node of the default page
     * @param joppa\model\Node $selectedNode the current node in the ui
     * @param boolean $addUnlocalizedClass Set to true to add the unlocalized class to nodes which are not localized in the current locale
     * @param int $truncateSize number of characters to truncate the name to
     * @return string HTML representation of the node
     */
    private function getNodeHtml(Node $node, $defaultNodeId, $addUnlocalizedClass, Node $selectedNode = null, $truncateSize = 20) {
        $isNodeSelected = false;
        if ($selectedNode && $selectedNode->id == $node->id) {
            $isNodeSelected = true;
        }

        $nodeClass = 'node';
        if ($isNodeSelected) {
        	$nodeClass .= ' selected';
        }
        if ($addUnlocalizedClass) {
        	if ($node->dataLocale != $this->locale) {
        	   $nodeClass .= ' unlocalized';
        	} else {
        		$nodeClass .= ' localized';
        	}
        }

        if (AjaxTreeController::isNodeCollapsed($node->id)) {
        	$nodeClass .= ' closed';
        }

        $html = '<li class="' . $nodeClass . '" id="node_' . $node->id . '">';

        if ($this->nodeTypeFacade->isAvailableInFrontend($node->type)) {
            if ($node->isSecured()) {
                $nodeClass = 'secured' . ucfirst($node->type);
            } else {
                $nodeClass = $node->type;
            }

            if ($node->id == $defaultNodeId) {
                $nodeClass .= 'Default';
            }
            if (!$node->isPublished()) {
                $nodeClass .= 'Hidden';
            }
        } else {
            $nodeClass = $node->type;
        }

        if ($node->children) {
            $html .= '<a href="#" class="toggle"></a>';
        } else {
        	$html .= '<span class="toggle"></span>';
        }

        $icon = new Image($this->getIcon($nodeClass));

        $html .= '<div class="handle ' . $nodeClass . '">';
        $html .= $icon->getHtml();
        $html .= '</div>';

        $html .= '<div class="menu">';

        $html .= $this->getAnchorHtml('/node/' . $node->id, String::truncate($node->name, $truncateSize, '...', true, true), false, 'name', null, $node->name);
        $html .= $this->getAnchorHtml('#', ' ', false, 'actionMenuNode', 'nodeActions_' . $node->id);
//        $html .= $this->getAnchorHtml(' <a href="#" class="actionMenuNode" id="nodeActions_' . $node->id . '" title="' . $node->name . '"></a>';
//        $html .= ' <a href="#" class="actionMenuNode" id="nodeActions_' . $node->id . '" title="' . $node->name . '"></a>';

        $html .= '<ul class="actions" id="nodeActions_' . $node->id . 'Menu">';

        $addedActions = false;
        foreach ($this->actions as $action) {
            if (!$action->isAvailableForNode($node)) {
                continue;
            }

            $addedActions = true;

            $html .= '<li>';
            $html .= $this->getAnchorHtml('/node/' . $action->getRoute() . '/' . $node->id, $action->getLabel($this->translator), false, $action->getRoute());
            $html .= '</li>';
        }

        $html .= '<li' . ($addedActions ? ' class="separator"' : '') . '>' . $this->getAnchorHtml('/node/edit/' . $node->id, 'button.edit', true, 'edit') . '</li>';
        $html .= '<li>' . $this->getAnchorHtml('/node/copy/' . $node->id, 'joppa.button.copy', true, 'copy') . '</li>';
        $html .= '<li>' . $this->getAnchorHtml('/node/delete/' . $node->id, 'button.delete', true, 'delete confirm') . '</li>';
        $html .= '</ul>';
        $html .= '</div>';

        if ($node->children) {
            $html .= '<ul class="children">';
            foreach ($node->children as $child) {
                $html .= $this->getNodeHtml($child, $defaultNodeId, $addUnlocalizedClass, $selectedNode, $truncateSize - 1);
            }
            $html .= '</ul>';
        }

        $html .= '</li>';

        return $html;
    }

    /**
     * Get the html for an anchor
     * @param string $href the href of the anchor
     * @param string $label the label for the anchor
     * @param boolean $translate true to translate the label
     * @param string $class Style class for the anchor
     * @return string html of the anchor
     */
    private function getAnchorHtml($href, $label, $translate, $class = null, $id = null, $title = null) {
        if ($translate) {
            $label = $this->translator->translate($label);
        }

        if ($href != '#') {
        	$href = $this->baseUrl . Module::ROUTE_JOPPA . $href;
        }

        if (!$label) {
        	$label = 'N/A';
        }

        $anchor = new Anchor($label, $href);
        if ($id) {
        	$anchor->setId($id);
        }
        if ($class) {
        	$anchor->appendToClass($class);
        }
        if ($title) {
        	$anchor->setAttribute('title', $title);
        }

        return $anchor->getHtml();
    }

    /**
     * Gets the path to the node icon based on the style class of the node
     * @param string $class
     * @return string
     */
    private function getIcon($class) {
    	switch ($class) {
    		case 'folder':
                return 'web/images/joppa/folder.png';
    		case 'page':
                return 'web/images/joppa/page.png';
    		case 'pageHidden':
                return 'web/images/joppa/page.hidden.png';
    		case 'securedPage':
                return 'web/images/joppa/page.secured.png';
    		case 'securedPageHidden':
                return 'web/images/joppa/page.hidden.secured.png';
    		case 'pageDefault':
                return 'web/images/joppa/page.default.png';
    		case 'site':
    		case 'siteHidden':
                return 'web/images/joppa/site.png';
            default:
                return 'web/images/joppa/node.png';
    	}
    }

}