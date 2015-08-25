<?php

namespace joppa\model\content;

use joppa\model\content\mapper\ContentMapper;

use zibo\core\Zibo;

use zibo\library\ObjectFactory;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Facade to the generic content
 */
class ContentFacade {

    /**
     * Class name of the content mapper interface
     * @var string
     */
	const CLASS_MAPPER = 'joppa\\model\\content\\mapper\\ContentMapper';

	/**
	 * Configuration key of the content mapper definition
	 * @var stirng
	 */
	const CONFIG_MAPPERS = 'joppa.mapper';

	/**
	 * Instance of the ContentFacade
	 * @var ContentFacade
	 */
	private static $instance;

	/**
	 * The registered content mappers
	 * @var array
	 */
	private $mappers;

	/**
	 * Construct this facade
	 * @return null
	 */
	private function __construct() {
		$this->loadMappers();
	}

	/**
	 * Get the instance of the ContentFacade
	 * @return ContentFacade
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

    /**
     * Get the title or name of the data
     * @param string $type type of the data
     * @param mixed $data
     * @return string title or name of the data
     */
	public function getTitle($type, $data) {
		$mapper = $this->getMapper($type);
		return $mapper->getTitle($data);
	}

    /**
     * Get the teaser of the data
     * @param string $type type of the data
     * @param mixed $data
     * @return string teaser of the data
     */
	public function getTeaser($type, $data) {
		$mapper = $this->getMapper($type);
		return $mapper->getTeaser($data);
	}

    /**
     * Get the url to the data
     * @param string $type type of the data
     * @param mixed $data
     * @return string url to the data
     */
	public function getUrl($type, $data) {
		$mapper = $this->getMapper($type);
		return $mapper->getUrl($data);
	}

	/**
     * Get the image of the data
     * @param string $type type of the data
     * @param mixed $data
     * @return string image to the data
     */
	public function getImage($type, $data) {
		$mapper = $this->getMapper($type);
		return $mapper->getImage($data);
	}

	/**
     * Get a generic content object for the data
     * @param string $type type of the data
     * @param mixed $data
     * @return joppa\model\content\Content generic content object of the data
     */
	public function getContent($type, $data) {
		$mapper = $this->getMapper($type);
		return $mapper->getContent($data);
	}

    /**
     * Get the registered content types
     * @return array Array with the names of the content types
     */
    public function getTypes() {
        return array_keys($this->mappers);
    }

	/**
     * Get the mapper for a content type
     * @param string $type content type
     * @return joppa\model\mapper\content\ContentMapper mapper for the content type
     * @throws zibo\ZiboException when no mapper could be found
	 */
	public function getMapper($type) {
		if (!array_key_exists($type, $this->mappers)) {
			throw new ZiboException('Could not find an content mapper for ' . $type);
		}

		return $this->mappers[$type];
	}

	/**
	 * Sets a mapper for a content type
	 * @param string $type content type
	 * @param ContentMapper $mapper The mapper for the content type
	 * @return null
	 */
	public function setMapper($type, ContentMapper $mapper) {
		$this->mappers[$type] = $mapper;
	}

	/**
	 * Load the content mappers as defined in the Zibo configuration
	 * @return null
	 */
	private function loadMappers() {
		$mappers = Zibo::getInstance()->getConfigValue(self::CONFIG_MAPPERS, array());
		$objectFactory = new ObjectFactory();

		$this->mappers = array();
		foreach ($mappers as $type => $className) {
			$mapper = $objectFactory->create($className, self::CLASS_MAPPER);
			$this->mappers[$type] = $mapper;
		}
	}

}