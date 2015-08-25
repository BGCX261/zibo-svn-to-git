<?php

namespace zibo\library\orm\cache;

/**
 * Cache implementation for the orm with the result cache disabled
 */
class DisabledResultModelCache extends FileModelCache {

    /**
     * Gets the result of a query from the cache
     * @param string $resultId Unique identifier of the result
     * @return null|ResultCacheObject The result cache object if cached, null otherwise
     */
    public function getResult($resultId) {
        return null;
    }

    /**
     * Sets the result of a query to the cache
     * @param string $resultId Unique identifier of the result
     * @param ResultCacheObject $resultCacheObject Result cache object containing the cached data of the result
     * @param array $usedModels Array with the names of the models used by the query
     * @return null
     */
    public function setResult($resultId, ResultCacheObject $resultCacheObject, array $usedModels) {

    }

    /**
     * Clears the result cache for all the queries which use the provided model
     * @param string $modelName Name of the model
     * @return null
     */
    public function clearResults($modelName) {

    }

}