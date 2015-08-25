<?php

namespace zibo\core\router;

/**
 * A alias maps an extra path to a existing route
 */
class Alias {

	/**
	 * The path of this alias
	 * @var string
	 */
    private $path;

    /**
     * The destination path of this alias
     * @var string
     */
    private $destination;

    /**
     * Constructs a new alias
     * @param string $path The path of this alias
     * @param string $destination The destination path of this alias
     * @return null
     */
    public function __construct($path, $destination) {
        $this->setPath($path);
        $this->setDestination($destination);
    }

    /**
     * Sets the path of this alias
     * @param string $path
     * @return null
     * @throws zibo\ZiboException if the path is empty or invalid
     */
    private function setPath($path) {
		Route::validatePath($path);

        $this->path = $path;
    }

    /**
     * Gets the path of this alias
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Sets the destination path
     * @param string $destination
     * @return null
     * @throws zibo\ZiboException if the path is empty or invalid
     */
    private function setDestination($destination) {
    	Route::validatePath($destination);

        $this->destination = $destination;
    }

    /**
     * Gets the destination path of this alias
     * @return string
     */
    public function getDestination() {
        return $this->destination;
    }

}