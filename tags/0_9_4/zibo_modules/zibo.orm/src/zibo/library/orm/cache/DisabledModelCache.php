<?php

namespace zibo\library\orm\cache;

/**
 * Cache implementation for the orm with the query and result cache disabled
 */
class DisabledModelCache extends DisabledResultModelCache {

    /**
     * Gets the SQL of a query from the cache
     * @param string $queryId Unique identifier of the query
     * @return null|QueryCacheObject The query cache object if cached, null otherwise
     */
    public function getQuery($queryId) {
        return null;
    }

    /**
     * Sets the SQL of a query to the cache
     * @param string $queryId Unique identifier of the query
     * @param string $queryCacheObject Query cache object containing the cached data of the query
     * @return null
     */
    public function setQuery($queryId, QueryCacheObject $queryCacheObject) {

    }

    /**
     * Clears the query cache
     * @return null
     */
    public function clearQueries() {

    }

}