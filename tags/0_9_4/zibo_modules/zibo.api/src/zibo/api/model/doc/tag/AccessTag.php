<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;

/**
 * Parser for the access tag
 */
class AccessTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'access';

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
        $access = array_shift($lines);
        if ($access == Doc::ACCESS_PRIVATE || $access == Doc::ACCESS_PROTECTED || $access == Doc::ACCESS_PUBLIC) {
            $doc->setAccess($access);
        }
    }

}