<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;
use zibo\api\model\doc\DocParameter;

/**
 * Parser for the return tag
 */
class ReturnTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'return';

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
        $return = implode("\n", $lines);

        $positionSpace = strpos($return, ' ');
        if ($positionSpace === false) {
            $type = trim($return);
            $description = null;
        } else {
            $type = substr($return, 0, $positionSpace);
            $description = trim(substr($return, $positionSpace));
        }

        if ($type == 'null' && !$description) {
            return;
        }

        $parameter = new DocParameter();
        $parameter->setType($type);
        $parameter->setDescription($description);

        $doc->setReturn($parameter);
    }

}