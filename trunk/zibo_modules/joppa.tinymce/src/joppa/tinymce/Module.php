<?php

namespace joppa\tinymce;

use joppa\model\NodeModel;
use joppa\model\SiteModel;

use zibo\core\Zibo;

use zibo\library\orm\ModelManager;

use zibo\tinymce\controller\TinyMCEController;

/**
 * Joppa TinyMCE module
 */
class Module {

    /**
     * Initializes the module
     * @return null
     */
	public function initialize() {
		Zibo::getInstance()->registerEventListener(TinyMCEController::EVENT_PRE_LINK_LIST, array($this, 'prepareTinyMCE'));
	}

	/**
	 * Prepares TinyMCE with all the nodes from the site tree
	 * @param zibo\tinymce\controller\TinyMCEController $controller The TinyMCE controller
	 * @return null
	 */
	public function prepareTinyMCE(TinyMCEController $controller) {
		$modelManager = ModelManager::getInstance();
		$nodeModel = $modelManager->getModel(NodeModel::NAME);
		$siteModel = $modelManager->getModel(SiteModel::NAME);

		$sites = $siteModel->getSites();

		foreach ($sites as $site) {
			$nodeTree = $siteModel->getNodeTreeForSite($site);
            $nodeList = $nodeModel->createListFromNodeTree($nodeTree);

            $prefix = '';
            if (count($sites) > 1) {
            	$prefix = '/' . $site->node->name;
            }

			foreach ($nodeList as $nodeId => $nodeName) {
	            $controller->addLink('%node.' . $nodeId . '.url%', $prefix . $nodeName);
			}
		}
	}

}