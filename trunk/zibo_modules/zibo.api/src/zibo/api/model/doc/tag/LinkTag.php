<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;

/**
 * Parser for the link tag
 */
class LinkTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'link';

    /**
     * Construct a new tag parser
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME);
    }

    /**
     * Parse the lines for this tag into the Doc data container
     * @param zibo\api\model\doc\Doc $doc Doc data container
     * @param array $lines doc comment lines for this tag
     * @return null
     */
    public function parse(Doc $doc, array $lines) {
        $link = implode("\n", $lines);
        $doc->setLink($link);
    }

}