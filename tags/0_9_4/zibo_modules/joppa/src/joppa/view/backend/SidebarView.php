<?php

namespace joppa\view\backend;

use joppa\form\backend\SiteSelectForm;

use joppa\model\Node;
use joppa\model\NodeTypeFacade;
use joppa\model\Site;
use joppa\model\SiteModel;

use joppa\Module;

use zibo\library\i18n\I18n;
use zibo\library\smarty\view\SmartyView;

/**
 * View for the sidebar of the Joppa backend view
 */
class SidebarView extends SmartyView {

    /**
     * Relative path to the javascript of a tree
     * @var string
     */
    const SCRIPT_TREE = 'web/scripts/tree.js';

    /**
     * Translation key for the node delete confirmation message
     * @var string
     */
    const TRANSLATION_DELETE_NODE_CONFIRM = 'joppa.label.node.delete';

    /**
     * Construct this view
     * @param joppa\form\backend\SiteSelectForm $siteSelectForm form to select another site
     * @param joppa\model\Site $site the current site (optional)
     * @param joppa\model\Node $node the current node (optional)
     * @return null
     */
    public function __construct (SiteSelectForm $siteSelectForm, Site $site = null, Node $node = null) {
        parent::__construct('joppa/backend/sidebar');

        $createActions = array();

        $baseUrl = $this->get('_baseUrl') . '/';
        $nodeTypeFacade = NodeTypeFacade::getInstance();

        $nodeTreeHtml = null;
        if ($site) {
        	$this->setSubview('tree', new TreeView($site, $node));

        	$toggleAction = $baseUrl . Module::ROUTE_AJAX_TREE;
        	$orderAction = $baseUrl . Module::ROUTE_JOPPA . '/node/order/';

        	$translator = I18n::getInstance()->getTranslator();
        	$deleteConfirmMessage = $translator->translate(self::TRANSLATION_DELETE_NODE_CONFIRM);

        	$this->addJavascript(self::SCRIPT_TREE);
            $this->addInlineJavascript("joppaInitializeNodeTree('$toggleAction', '$orderAction', '$deleteConfirmMessage');");

	        $labels = $nodeTypeFacade->getLabels();
	        foreach ($labels as $type => $label) {
	            $action = array(
	               'type' => $type,
	               'label' => $label,
	               'url' => $baseUrl . Module::ROUTE_JOPPA . '/' . $type . '/add',
	            );
	            $createActions[] = $action;
	        }
        } else {
            $createActions[] = array(
                'type' => SiteModel::NODE_TYPE,
                'label' => $nodeTypeFacade->getLabel(SiteModel::NODE_TYPE),
                'url' => $baseUrl . Module::ROUTE_JOPPA . '/' . SiteModel::NODE_TYPE . '/add',
            );
        }

        $this->set('formSiteSelect', $siteSelectForm);
        $this->set('createActions', $createActions);
    }

}