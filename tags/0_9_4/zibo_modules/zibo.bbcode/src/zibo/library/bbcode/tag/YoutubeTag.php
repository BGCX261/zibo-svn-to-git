<?php

namespace zibo\library\bbcode\tag;

use zibo\library\google\Youtube;

/**
 * Implementation of the youtube tag
 */
class YoutubeTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'youtube';

    /**
     * Constructs the url tag
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME, true, false);
    }

    /**
     * Parses the tag
     * @param string $content Content of the tag
     * @param array $parameters Parameters of the tag
     * @return string HTML replacement for the tag
     */
    public function parseTag($content, array $parameters) {
        $youtube = new Youtube($content);
        return $youtube->getHtml();
    }

}