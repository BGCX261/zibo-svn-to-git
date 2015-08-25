<?php

namespace joppa\model\content;

use zibo\library\Data;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Generic data container of a content type
 */
class Content extends Data {

    /**
     * Title or name of the content
     * @var string
     */
    public $title;

    /**
     * Teaser of the content
     * @var string
     */
    public $teaser;

    /**
     * Url to the full content
     * @var string
     */
    public $url;

    /**
     * Url to the image of the content
     * @var string
     */
    public $image;

    /**
     * Date of the content
     * @var integer
     */
    public $date;

    /**
     * The data object of the content
     * @var mixed
     */
    public $data;

    /**
     * Construct this data container
     * @param string $title title or name of the content
     * @param string $url url to the full content
     * @param string $teaser teaser of the content
     * @param string $image url to the image of the content
     * @param string $date Date of the content
     * @param mixed $data actual data object of the content
     * @return null
     */
    public function __construct($title, $url = null, $teaser = null, $image = null, $date = null, $data = null) {
        $this->setTitle($title);

        $this->url = $url;
        $this->teaser = $teaser;
        $this->image = $image;
        $this->date = $date;
        $this->data = $data;
    }

    /**
     * Sets the title of this content
     * @param string $title
     * @return null
     * @throws zibo\ZiboException when the title is empty
     */
    private function setTitle($title) {
        if (String::isEmpty($title)) {
            throw new ZiboException('Provided title is empty');
        }
        $this->title = $title;
    }

}