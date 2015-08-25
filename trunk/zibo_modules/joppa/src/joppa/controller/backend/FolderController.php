<?php

namespace joppa\controller\backend;

use joppa\form\backend\NodeForm;

use joppa\model\FolderNodeType;

use joppa\view\backend\NodeFormView;

/**
 * Controller to manage the folders
 */
class FolderController extends NodeTypeController {

    /**
     * Construct this controller
     * @return null
     */
    public function __construct() {
        parent::__construct(FolderNodeType::NAME);
    }

    /**
     * Get the node form view
     * @param joppa\form\backend\NodeForm $form
     * @return joppa\view\backend\NodeFormView
     */
    protected function getFormView(NodeForm $form) {
        return new NodeFormView($this->getSiteSelectForm(), $form, $this->site, $this->node, 'joppa/backend/folder');
    }

}