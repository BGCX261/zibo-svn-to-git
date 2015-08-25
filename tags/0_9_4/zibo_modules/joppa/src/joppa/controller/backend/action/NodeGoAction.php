<?php

namespace joppa\controller\backend\action;

use joppa\model\Node;
use joppa\model\NodeTypeFacade;

/**
 * Controller of the advanced node action
 */
class NodeGoAction extends AbstractNodeAction {

    /**
     * Route of this action
     * @var string
     */
    const ROUTE = 'go';

    /**
     * Translation key of the label
     * @var string
     */
    const TRANSLATION_LABEL = 'joppa.button.go';

    /**
     * Construct this node action
     * @return null
     */
    public function __construct() {
        parent::__construct(self::ROUTE, self::TRANSLATION_LABEL, false);
    }

    /**
     * Checks if this action is available for the node
     * @param joppa\model\Node $node
     * @return boolean true if available
     */
    public function isAvailableForNode(Node $node) {
        return NodeTypeFacade::getInstance()->isAvailableInFrontend($node->type);
    }

    /**
     * Perform the go node action
     */
    public function indexAction() {
        $url = $this->request->getBaseUrl() . '/' . $this->node->getRoute();
        $this->response->setRedirect($url);
    }

}