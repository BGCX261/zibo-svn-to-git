<?php

namespace zibo\api\model\doc\tag;

use zibo\api\model\doc\Doc;

/**
 * Abstract parser for a doc tag
 */
class Tag {

    /**
     * Name of this tag
     * @var string
     */
    private $name;

    /**
     * Construct a new tag parser
     * @param string $name name of this tag
     * @return null
     */
    public function __construct($name) {
        $this->setName($name);
    }

    /**
     * Set the name of this tag
     * @param string $name
     * @return null
     */
    private function setName($name) {
        $this->name = $name;
    }

    /**
     * Get the name of this tag
     * @return string name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Parse the lines for this tag into the Doc data container
     * @param zibo\api\model\doc\Doc $doc Doc data container
     * @param array $lines doc comment lines for this tag
     * @return null
     */
    public function parse(Doc $doc, array $lines) {

    }

}