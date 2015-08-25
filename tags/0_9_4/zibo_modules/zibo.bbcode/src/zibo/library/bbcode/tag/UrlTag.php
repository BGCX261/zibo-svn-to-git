<?php

namespace zibo\library\bbcode\tag;

/**
 * Implementation of the url tag
 */
class UrlTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'url';

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
        $countParameters = count($parameters);

        if ($countParameters > 1) {
            return false;
        }

        $href = $content;
        if ($countParameters == 1 && array_key_exists(0, $parameters)) {
            $href = $parameters[0];
        }

        return '<a href="' . $href . '" rel="nofollow" target="_blank">' . $content . '</a>';
    }

}