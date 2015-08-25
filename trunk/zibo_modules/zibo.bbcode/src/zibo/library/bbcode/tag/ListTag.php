<?php

namespace zibo\library\bbcode\tag;

/**
 * Implementation of the list tag
 */
class ListTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'list';

    /**
     * Constructs the list tag
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

    /**
     * Parses the tag
     * @param string $content Content of the tag
     * @param array $parameters Parameters of the tag
     * @return string HTML replacement for the tag
     */
    public function parseTag($content, array $parameters) {
        $output = '';

        $items = explode('[*]', $content);
        foreach ($items as $item) {
            if (!trim(str_replace('<br />', '', $item))) {
                continue;
            }

            $item = rtrim($item, "\n");

            $output .= '<li>' . $item . '</li>';
        }

        return '<ul>' . $output . '</ul>';
    }

}