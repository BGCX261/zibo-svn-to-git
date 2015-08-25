<?php

namespace joppa\model\content\mapper;

use zibo\library\orm\ModelManager;

use zibo\ZiboException;

/**
 * Abstract implementation of a ContentMapper for a model of the orm module
 */
class OrmContentMapper extends AbstractContentMapper {

	/**
	 * The model of the content to map
	 * @var zibo\library\orm\Model
	 */
	protected $model;

	/**
	 * The recursive depth of the query
	 * @var integer
	 */
	protected $recursiveDepth;

	/**
	 * The data format for the title
	 * @var string|boolean
	 */
	protected $titleFormat;

	/**
	 * The data format for the teaser
	 * @var string|boolean
	 */
	protected $teaserFormat;

	/**
	 * The data format for the image
	 * @var string|boolean
	 */
	protected $imageFormat;

	/**
	 * The data format for the date
	 * @var string|boolean
	 */
	protected $dateFormat;

	/**
     * Construct a new content mapper
     * @param string $modelName Name of the model
     * @return null
	 */
	public function __construct($modelName, $recursiveDepth = 1) {
        $this->model = ModelManager::getInstance()->getModel($modelName);
        $this->recursiveDepth = $recursiveDepth;
	}

    /**
     * Get a generic content object for the provided data
     * @param mixed $data data object of the model or the id of a data object
     * @return joppa\model\content\Content Generic content object
     */
    public function getContent($data) {
        if ($data === null) {
            throw new ZiboException('Provided data is empty');
        }

        $data = $this->getData($data, $this->recursiveDepth);

        return $this->getContentFromData($data);
    }

	/**
     * Get a data object from the model
     * @param int|object $data When an object is provided, the object will be returned. When a primary key is provided,
     * the data object will be looked up in the model
     * @return object
     * @throws zibo\ZiboException when the data object was not found in the model
	 */
    protected function getData($data, $recursive = 1) {
        if (!is_numeric($data)) {
            return $data;
        }

        $id = $data;

        $data = $this->model->findById($id, $recursive);
        if ($data === null) {
            throw new ZiboException('Could not find ' . $this->model->getName() . ' with id ' . $id);
        }

        return $data;
    }

    /**
     * Creates a generic content object from the provided data
     * @param mixed $data
     * @return joppa\model\content\Content
     */
    protected function getContentFromData($data) {
        if (!$this->titleFormat) {
        	$this->initFormats();
        }

        $meta = $this->model->getMeta();
        $dataFormatter = $meta->getDataFormatter();

        $title = $dataFormatter->formatData($data, $this->titleFormat);
        $url = null;
        $teaser = null;
        $image = null;
        $date = null;

        if ($this->teaserFormat) {
            $teaser = $dataFormatter->formatData($data, $this->teaserFormat);
        }

        if ($this->imageFormat) {
            $image = $dataFormatter->formatData($data, $this->imageFormat);
        }

        if ($this->dateFormat) {
            $date = $dataFormatter->formatData($data, $this->dateFormat);
        }

        $url = $this->getUrl($data);

        return new Content($title, $url, $teaser, $image, $date, $data);
    }

    /**
     * Initialize the formats
     * @return null
     */
    private function initFormats() {
        $meta = $this->model->getMeta();

        $modelTable = $meta->getModelTable();
        $dataFormatter = $meta->getDataFormatter();

        $this->titleFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_TITLE)->getFormat();

        $this->teaserFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_TEASER, false);
        if ($this->teaserFormat) {
            $this->teaserFormat = $teaserFormat->getFormat();
        }

        $this->imageFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_IMAGE, false);
        if ($this->imageFormat) {
            $this->imageFormat = $imageFormat->getFormat();
        }

        $this->dateFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_DATE, false);
        if ($this->dateFormat) {
            $this->dateFormat = $dateFormat->getFormat();
        }
    }

}