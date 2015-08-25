<?php

namespace joppa\content\model\mapper;

use joppa\content\model\ContentProperties;

use joppa\model\content\mapper\OrmContentMapper;
use joppa\model\NodeModel;

use zibo\library\orm\ModelManager;

use zibo\ZiboException;

class ContentMapper extends OrmContentMapper {

	/**
	 * The base path for the content type
	 * @var string
	 */
	private $basePath;

	/**
	 * The id field to add to the base path
	 * @var string
	 */
	private $idField;

    /**
     * Get the url to the data
     * @param mixed $data
     */
    public function getUrl($data) {
        return $this->getBasePath() . '/' . $this->getDataId($data);
    }

    /**
     * Gets the id of the data to add to the base path of the URL
     * @param mixed $data
     * @return string
     */
    private function getDataId($data) {
    	if ($this->idField == 'id') {
    		if (is_numeric($data)) {
    			return $data;
    		}

    		return $data->id;
    	}

    	$field = $this->idField;
    	$data = $this->getData($data);

    	if (!isset($data->$field)) {
    		throw new ZiboException('Could not get the id of ' . $this->model->getName() . ' with id ' . $data->id . ': ' . $field . ' is not set');
    	}

    	return $data->$field;
    }

    /**
     * Gets the base path for a data object of this mapper
     * @return string
     */
    private function getBasePath() {
    	if ($this->basePath) {
    		return $this->basePath;
    	}

    	$modelName = $this->model->getName();

    	$nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
    	$nodes = $nodeModel->getNodesForWidget('joppa', 'contentDetail');

    	foreach ($nodes as $node) {
    		if ($modelName != $node->widgetProperties->getWidgetProperty(ContentProperties::PROPERTY_MODEL_NAME)) {
    			continue;
    		}

    		$this->idField = $node->widgetProperties->getWidgetProperty(ContentProperties::PROPERTY_PARAMETER_ID);

    		$this->basePath = $this->getBaseUrl() . '/' . $node->getRoute();
    		break;
    	}

    	return $this->basePath;
    }

}