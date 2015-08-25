<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;
use zibo\api\model\doc\DocParameter;

/**
 * Parser for the param tag
 */
class ParamTag extends Tag {

    /**
     * Name of this tag
     * @var string
     */
    const NAME = 'param';

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
        $param = implode("\n", $lines);

        $positionSpace = strpos($param, ' ');
        if ($positionSpace === false) {
            $type = $param;
            $name = null;
            $description = null;
        } else {
            $type = substr($param, 0, $positionSpace);

            $positionDescription = strpos($param, ' ', $positionSpace + 1);
            if ($positionDescription === false) {
                $name = substr($param, $positionSpace + 1);
                $description = null;
            } else {
                $positionName = $positionSpace + 1;
                $name = substr($param, $positionName, $positionDescription - ($positionName));
                $description = substr($param, $positionDescription + 1);
            }

            if ($name && $name[0] != '$') {
                $description = $name . ($description ? ' ' . $description : '');
                $name = null;
            }
        }

        $parameter = new DocParameter();
        $parameter->setType($type);
        $parameter->setName($name);
        $parameter->setDescription($description);

        $doc->addParameter($parameter);
    }

}