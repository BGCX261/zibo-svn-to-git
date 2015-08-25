<?php

namespace zibo\library\bbcode\tag;

/**
 * Implementation of the quote tag
 */
class QuoteTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'quote';

    /**
     * Constructs the quote tag
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
        $prefix = '';
        if (count($parameters) == 1 && array_key_exists(0, $parameters)) {
            $prefix = '<strong>' . $parameters[0] . ':</strong> ';
        }

        return '<div class="quote">' . $prefix . $content . '</div>';
    }

}