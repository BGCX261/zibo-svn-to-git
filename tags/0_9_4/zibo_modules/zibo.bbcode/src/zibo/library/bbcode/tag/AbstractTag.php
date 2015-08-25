<?php

namespace zibo\library\bbcode\tag;

/**
 * Abstract implementation of a tag
 */
abstract class AbstractTag implements Tag {

    /**
     * The name of this tag
     * @var string
     */
    private $name;

    /**
     * Flag to see if this tag needs a close tag
     * @var boolean
     */
    private $hasCloseTag;

    /**
     * Flag to see whether the content of this tag needs to be processed
     * @var boolean
     */
    private $needsContentParsing;

    /**
     * Constructs a new tag
     * @param string $name Name of the tag
     * @param boolean $hasCloseTag Flag to see if this tag needs a close tag
     * @param boolean $needsContentParsing Flag to see whether the content of this tag needs to be processed
     * @return null
     */
    public function __construct($name, $hasCloseTag = true, $needsContentParsing = true) {
        $this->name = $name;
        $this->hasCloseTag = $hasCloseTag;
        $this->needsContentParsing = $needsContentParsing;
    }

    /**
     * Gets the name of the tage. eg. [b]'s name is b
     * @return string
     */
    public function getTagName() {
        return $this->name;
    }

    /**
     * Gets whether this tag needs a close tag
     * @return boolean
     */
    public function hasCloseTag() {
        return $this->hasCloseTag;
    }

    /**
     * Gets whether the content of this tag needs to be processed
     * @return boolean
     */
    public function needsContentParsing() {
        return $this->needsContentParsing;
    }


}