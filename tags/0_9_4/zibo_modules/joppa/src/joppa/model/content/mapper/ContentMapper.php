<?php

namespace joppa\model\content\mapper;

/**
 * Interface to map a data type to generic content
 */
interface ContentMapper {

    /**
     * Get the title or name of the data
     * @param mixed $data
     * @return string title or name of the data
     */
	public function getTitle($data);

	/**
     * Get the teaser of the data
     * @param mixed $data
     * @return string teaser of the data
	 */
	public function getTeaser($data);

	/**
     * Get the url to the data
     * @param mixed $data
     * @return string url to the data
	 */
	public function getUrl($data);

	/**
     * Get the image of the data
     * @param mixed $data
     * @return string route to the image of the data
	 */
	public function getImage($data);

	/**
     * Get a generic content object of the data
     * @param mixed $data
     * @return joppa\model\content\Content
	 */
	public function getContent($data);

}