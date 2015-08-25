<?php

namespace joppa\model\content\mapper;

use joppa\model\NodeModel;

use zibo\core\Zibo;

use zibo\library\orm\ModelManager;

/**
 * Abstract implementation of a ContentMapper
 */
abstract class AbstractContentMapper implements ContentMapper {

    /**
     * The base URL
     * @var string
     */
    private $baseUrl;

    /**
     * Get the title or name of the data
     * @param mixed $data
     * @return string name or title of the data
     */
    public function getTitle($data) {
        $content = $this->getContent($data);
        return $content->title;
    }

    /**
     * Get the teaser of the data
     * @param mixed $data
     */
    public function getTeaser($data) {
        $content = $this->getContent($data);
        return $content->teaser;
    }

    /**
     * Get the url to the data
     * @param mixed $data
     */
    public function getUrl($data) {
        $content = $this->getContent($data);
        return $content->url;
    }

    /**
     * Get the image of the data
     * @param mixed $data
     */
    public function getImage($data) {
        $content = $this->getContent($data);
        return $content->image;
    }

    /**
     * Gets the base URL
     * @return string
     */
    protected function getBaseUrl() {
        if ($this->baseUrl) {
            return $this->baseUrl;
        }

        return $this->baseUrl = Zibo::getInstance()->getRequest()->getBaseUrl();
    }

    /**
     * Get the nodes which contain the provided widget
     * @param string $namespace Namespace of the widget
     * @param string $name Name of the widget
     * @return array Array with the nodes which contain the provided widget
     */
    protected function getNodesForWidget($namespace, $name) {
        $nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
        return $nodeModel->getNodesForWidget($namespace, $name);
    }

}