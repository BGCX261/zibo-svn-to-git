<?php

namespace zibo\library\orm\cache;

/**
 * Data container for the cached data of a result
 */
class ResultCacheObject {

    /**
     * The result to cache
     * @var mixed
     */
    private $result;

    /**
     * Array with belongs to fields
     * @var array
     */
    private $belongsTo;

    /**
     * Array with has fields
     * @var array
     */
    private $has;

    /**
     * Constructs a new result cache object
     * @param mixed $result Result to cache
     * @param array $usedModels Array with used models
     * @param array $belongsTo Array with belongsTo fields
     * @param array $has Array with has fields
     * @return null
     */
    public function __construct($result, array $belongsTo = null, array $has = null) {
        $this->result = $result;
        $this->belongsTo = $belongsTo;
        $this->has = $has;
    }

    /**
     * Gets the result
     * @return mixed
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Gets the belongs to fields which still need to be fetched
     * @return array Array with the name of the field as key and the field object as value
     */
    public function getBelongsToFields() {
        return $this->belongsTo;
    }

    /**
     * Gets the has fields which still need to be fetched
     * @return array Array with the name of the field as key and the field object as value
     */
    public function getHasFields() {
        return $this->has;
    }

}