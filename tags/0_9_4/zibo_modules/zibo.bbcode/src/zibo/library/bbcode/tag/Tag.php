<?php

namespace zibo\library\bbcode\tag;

/**
 * Interface for a tag
 */
interface Tag {

    /**
     * Symbol to open a tag
     * @var string
     */
    const OPEN = '[';

    /**
     * Symbol to close a tag
     * @var string
     */
    const CLOSE = ']';

    /**
     * Prefix for a tag's name to mark the tag as a closing tag
     * @var string
     */
    const CLOSE_PREFIX = '/';

    /**
     * Gets the name of the tage. eg. [b]'s name is b
     * @return string
     */
    public function getTagName();

    /**
     * Gets whether this tag needs a close tag
     * @return boolean
     */
    public function hasCloseTag();

    /**
     * Gets whether the content of this tag needs to be processed
     * @return boolean
     */
    public function needsContentParsing();

    /**
     * Parses the tag
     * @param string $content Content of the tag
     * @param array $parameters Parameters of the tag
     * @return string|false HTML replacement for the tag, false on parse error
     */
    public function parseTag($content, array $parameters);

}