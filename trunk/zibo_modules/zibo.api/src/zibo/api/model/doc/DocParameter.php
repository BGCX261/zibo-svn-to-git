<?php

namespace zibo\api\model\doc;

/**
 * Data container for parsed PhpDoc parameter
 */
class DocParameter {

    /**
     * Type of the parameter
     * @var string
     */
    private $type;

    /**
     * Name of the parameter
     * @var string
     */
    private $name;

    /**
     * Description of the parameter
     * @var string
     */
    private $description;

    /**
     * Set the type of this parameter
     * @param string $type
     * @return null
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Get the type of this parameter
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set the name of this parameter
     * @param string $name
     * @return null
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get the name of this parameter
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the description of this parameter
     * @param string $description
     * @return null
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Get the description of this parameter
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

}