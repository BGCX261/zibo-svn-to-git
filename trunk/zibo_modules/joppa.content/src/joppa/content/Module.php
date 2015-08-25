<?php

namespace joppa\content;

use joppa\content\model\mapper\ContentMapper;
use joppa\content\model\ContentProperties;

use joppa\model\content\ContentFacade;
use joppa\model\NodeModel;

use joppa\router\JoppaRequest;

use zibo\core\Zibo;

use zibo\library\orm\ModelManager;

class Module {

	public function initialize() {
        Zibo::getInstance()->registerEventListener(Zibo::EVENT_POST_ROUTE, array($this, 'initializeContentMappers'));
	}

	public function initializeContentMappers() {
		$request = Zibo::getInstance()->getRequest();

		if (!($request instanceof JoppaRequest)) {
			return;
		}

		$contentFacade = ContentFacade::getInstance();
		$contentMappers = $contentFacade->getTypes();

        $nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
        $nodes = $nodeModel->getNodesForWidget('joppa', 'contentDetail');

        foreach ($nodes as $node) {
            $modelName = $node->widgetProperties->getWidgetProperty(ContentProperties::PROPERTY_MODEL_NAME);

            if (in_array($modelName, $contentMappers)) {
            	continue;
            }

            $recursiveDepth = $node->widgetProperties->getWidgetProperty(ContentProperties::PROPERTY_RECURSIVE_DEPTH);

            $mapper = new ContentMapper($modelName, $recursiveDepth);

            $contentFacade->setMapper($modelName, $mapper);
            $contentMappers[] = $modelName;
        }
	}

}