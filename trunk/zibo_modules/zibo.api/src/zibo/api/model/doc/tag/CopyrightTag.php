<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;

/**
 * Parser for the copyright tag
 */
class CopyrightTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'copyright';

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
        $copyright = implode("\n", $lines);
        $doc->setCopyright($copyright);
    }

}