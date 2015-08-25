<?php

namespace zibo\library\cache;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Data container for a cached value
 */
class CacheObject {

    /**
     * Type of the cache object
     * @var string
     */
    private $type;

    /**
     * Id of the cache object
     * @var string
     */
    private $id;

    /**
     * Value to cache
     * @var mixed
     */
    private $data;

    /**
     * Number of times this cache object has been accessed
     * @var int
     */
    private $timesAccessed;

    /**
     * The timestamp of the last access date
     * @var int
     */
    private $dateLastAccessed;

    /**
     * The timestamp this object was created
     * @var int
     */
    private $dateCreated;

    /**
     * Constructs a new cache object
     * @param string $type Type of the cache object
     * @param string $id Id of the cache obejct
     * @param mixed $data Value to cache
     * @return null
     */
    public function __construct($type, $id, $data) {
        $this->setType($type);
        $this->setId($id);
        $this->setData($data);
        $this->dateCreated = $this->dateLastAccessed = time();
        $this->timesAccessed = 0;
    }

    /**
     * Sets the type of this cache object
     * @param string $type
     * @return null
     * @throws zibo\ZiboException when the provided type is empty or invalid
     */
    private function setType($type) {
        if (!String::isString($type, String::NOT_EMPTY)) {
            throw new ZiboException('Provided type is empty');
        }

        $this->type = $type;
    }

    /**
     * Gets the type of this cache object
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the id of this cache object
     * @param string $id
     * @return null
     * @throws zibo\ZiboException when the provided id is empty or invalid
     */
    private function setId($id) {
        if (!String::isString($id, String::NOT_EMPTY)) {
            throw new ZiboException('Provided id is empty');
        }

        $this->id = $id;
    }

    /**
     * Gets the id of this cache object
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the value to cache
     * @param mixed $data
     * @return null
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * Gets the value to cache
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Gets the creation date of this cache object
     * @return int Timestamp of the creation date
     */
    public function getCreationDate() {
        return $this->dateCreated;
    }

    /**
     * Gets the date of the last access
     * @return int Timestamp of the last access
     */
    public function getLastAccessDate() {
        return $this->dateLastAccessed;
    }

    /**
     * Gets the number of accesses
     * @return int Times this cache object has been accessed
     */
    public function getTimesAccessed() {
        return $this->timesAccessed;
    }

    /**
     * Access this cache object. This will update the access values.
     * @return null
     */
    public function access() {
        $this->timesAccessed++;
        $this->dateLastAccessed = time();
    }

}