<?php

namespace zibo\library\orm\model\meta;

/**
 * Meta definition for a relation field
 */
class RelationMeta {

    /**
     * Flag to set whether the relation model is the same as the model who made the relation
     * @var boolean
     */
    private $isRelationWithSelf = false;

    /**
     * Flag to set whether this is a many to many relation
     * @var boolean
     */
    private $isHasManyAndBelongsToMany = false;

    /**
     * Name of the foreign key for this relation
     * @var string|array
     */
    private $foreignKey;

    /**
     * Name of the foreign key for the relation back to the model
     * @var string
     */
    private $foreignKeyToSelf;

    /**
     * Name of the link model for this relation
     * @var string
     */
    private $linkModelName;

    /**
     * Return the fields to serialize
     * @return array Array with field names
     */
    public function __sleep() {
        $fields = array();

        if ($this->isRelationWithSelf) {
            $fields[] = 'isRelationWithSelf';
        }

        if ($this->isHasManyAndBelongsToMany) {
            $fields[] = 'isHasManyAndBelongsToMany';
        }

        if ($this->foreignKey) {
            $fields[] = 'foreignKey';
        }

        if ($this->foreignKeyToSelf) {
            $fields[] = 'foreignKeyToSelf';
        }

        if ($this->linkModelName) {
            $fields[] = 'linkModelName';
        }

        return $fields;
    }

    /**
     * Reinitialize the meta after sleeping
     * @return null
     */
    public function __wakeup() {
        if (!$this->isRelationWithSelf) {
            $this->isRelationWithSelf = false;
        }

        if (!$this->isHasManyAndBelongsToMany) {
            $this->isHasManyAndBelongsToMany = false;
        }
    }

    /**
     * Sets whether the relation model is the same as the model who made the relation
     * @param boolean $flag
     * @return null
     */
    public function setIsRelationWithSelf($flag) {
        $this->isRelationWithSelf = $flag;
    }

    /**
     * Gets whether the relation model is the same as the model who made the relation
     * @return boolean
     */
    public function isRelationWithSelf() {
        return $this->isRelationWithSelf;
    }

    /**
     * Sets whether this is a many to many relation
     * @param boolean $flag
     * @return null
     */
    public function setIsHasManyAndBelongsToMany($flag) {
        $this->isHasManyAndBelongsToMany = $flag;
    }

    /**
     * Gets whether this is a many to many relation
     * @return boolean
     */
    public function isHasManyAndBelongsToMany() {
        return $this->isHasManyAndBelongsToMany;
    }

    /**
     * Sets the foreign key to the relation model
     * @param string|array $foreignKey String or array with the name(s) of the foreign key(s)
     * @return null
     */
    public function setForeignKey($foreignKey) {
        $this->foreignKey = $foreignKey;
    }

    /**
     * Gets the foreign key to the relation model
     * @return string|array String or array with the name(s) of the foreign key(s)
     */
    public function getForeignKey() {
        return $this->foreignKey;
    }

    /**
     * Sets the foreign key for the relation back to the model
     * @param string $foreignKey Name of the foreign key
     * @return null
     */
    public function setForeignKeyToSelf($foreignKey) {
        $this->foreignKeyToSelf = $foreignKey;
    }

    /**
     * Gets the foreign key for the relation back to the model
     * @return string Name of the foreign key
     */
    public function getForeignKeyToSelf() {
        return $this->foreignKeyToSelf;
    }

    /**
     * Sets the name of the link model for this relation
     * @param string $linkModelName Name of the link model
     * @return null
     */
    public function setLinkModelName($linkModelName) {
        $this->linkModelName = $linkModelName;
    }

    /**
     * Gets the name of the link model for this relation
     * @return string Name of the link model
     */
    public function getLinkModelName() {
        return $this->linkModelName;
    }

}