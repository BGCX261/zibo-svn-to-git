<?php

namespace zibo\library\orm\cache;

/**
 * Data container for the cached data of a query
 */
class QueryCacheObject {

    /**
     * The SQL of the query
     * @var string
     */
    private $sql;

    /**
     * Array with the names of the models used in this query
     * @var array
     */
    private $usedModels;

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
     * Constructs a new query cache object
     * @param string $sql SQL of the query
     * @param array $usedModels Array with used models
     * @param array $belongsTo Array with belongsTo fields
     * @param array $has Array with has fields
     * @return null
     */
    public function __construct($sql, array $usedModels, array $belongsTo = null, array $has = null) {
        $this->sql = $sql;
        $this->usedModels = $usedModels;
        $this->belongsTo = $belongsTo;
        $this->has = $has;
    }

    /**
     * Gets the SQL of this query
     * @return string
     */
    public function getSql() {
        return $this->sql;
    }

    /**
     * Gets the names of the models used in this query
     * @return array Array with the names of the models
     */
    public function getUsedModels() {
        return $this->usedModels;
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