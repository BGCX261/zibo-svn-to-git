<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;
use zibo\api\model\doc\DocParameter;

/**
 * Parser for the throws tag
 */
class ThrowsTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'throws';

    /**
     * Construct a new tag parser
     * @param string $name name of the tag
     * @return null
     */
    public function __construct($name = self::NAME) {
        parent::__construct($name);
    }

    /**
     * Parse the lines for this tag into the Doc data container
     * @param zibo\api\model\doc\Doc $doc Doc data container
     * @param array $lines doc comment lines for this tag
     * @return null
     */
    public function parse(Doc $doc, array $lines) {
        $exception = implode("\n", $lines);

        $positionSpace = strpos($exception, ' ');
        if ($positionSpace === false) {
            $type = $exception;
            $description = null;
        } else {
            $type = substr($exception, 0, $positionSpace);
            $description = substr($exception, $positionSpace + 1);
        }

        $exception = new DocParameter();
        $exception->setType($type);
        $exception->setDescription($description);

        $doc->addException($exception);
    }

}