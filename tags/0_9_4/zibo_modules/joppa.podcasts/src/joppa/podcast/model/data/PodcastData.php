<?php

namespace joppa\podcast\model\data;

use joppa\podcast\model\PodcastModel;

use zibo\library\orm\model\data\Data;

/**
 * Data container of a podcast
 */
class PodcastData extends Data {

    /**
     * The author of this podcast
     * @var integer|zibo\library\security\model\User
     */
    public $author;

	/**
	 * The title of the podcast
	 * @var string
	 */
	public $title;

	/**
	 * The teaser of the podcast
	 * @var string
	 */
	public $teaser;

	/**
	 * The text of the podcast
	 * @var string
	 */
	public $text;

	/**
	 * The path to the image of the podcast
	 * @var string
	 */
	public $image;

	/**
	 * The path to the audio of the podcast
	 * @var string
	 */
	public $audio;

	/**
	 * Flag to see if the audio is downloadable
	 * @var boolean
	 */
	public $isDownloadable;

    /**
     * The slug for the podcast
     * @var string
     */
    public $slug;

	/**
	 * The timestamp of the publication date
	 * @var integer
	 */
	public $datePublication;

	/**
	 * The locale code of the content of the podcast
	 * @var string
	 */
	public $locale;

	/**
	 * Gets the image of this podcast
	 * @return string Path to the image
	 */
	public function getImage() {
		if ($this->image) {
			return $this->image;
		}

		return PodcastModel::DEFAULT_IMAGE;
	}

}