<?php

namespace joppa\model\content\mapper;

use joppa\model\content\Content;
use joppa\model\NodeModel;

/**
 * Node content mapper
 */
class NodeContentMapper extends OrmContentMapper {

    /**
     * Construct a new node content mapper
     * @return null
     */
    public function __construct() {
    	parent::__construct(NodeModel::NAME, 0);
    }

    /**
     * Creates a generic content object from the provided data
     * @param mixed $data
     * @return joppa\model\content\Content
     */
    protected function getContentFromData($data) {
		return new Content($data->name, $this->getBaseUrl() . '/' . $data->getRoute());
    }

}