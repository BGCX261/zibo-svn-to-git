<?php

namespace zibo\library\orm\cache;

use zibo\library\filesystem\File;
use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\io\TypeMemoryCacheIO;
use zibo\library\cache\SimpleCache;
use zibo\library\orm\model\Model;

/**
 * Interface for the cache implementation for the orm
 */
interface ModelCache {

    /**
     * Gets the a query from the cache
     * @param string $queryId Unique identifier of the query
     * @return null|QueryCacheObject The query cache object if cached, null otherwise
     */
    public function getQuery($queryId);

    /**
     * Sets a query to the cache
     * @param string $queryId Unique identifier of the query
     * @param string $queryCacheObject Query cache object containing the cached data of the query
     * @return null
     */
    public function setQuery($queryId, QueryCacheObject $queryCacheObject);

    /**
     * Clears the query cache
     * @return null
     */
    public function clearQueries();

    /**
     * Gets the result of a query from the cache
     * @param string $resultId Unique identifier of the result
     * @return null|ResultCacheObject The result cache object if cached, null otherwise
     */
    public function getResult($resultId);

    /**
     * Sets the result of a query to the cache
     * @param string $resultId Unique identifier of the result
     * @param ResultCacheObject $resultCacheObject Result cache object containing the cached data of the result
     * @param array $usedModels Array with the names of the models used by the query
     * @return null
     */
    public function setResult($resultId, ResultCacheObject $resultCacheObject, array $usedModels);

    /**
     * Clears the result cache for all the queries which use the provided model
     * @param string $modelName Name of the model
     * @return null
     */
    public function clearResults($modelName);

    /**
     * Sets a model to the cache
     * @param zibo\library\orm\model\Model $model Model to cache
     * @return null
     */
    public function setModel(Model $model);

    /**
     * Gets a model from the cache
     * @return zibo\library\orm\model\Model
     */
    public function getModel($modelName);

    /**
     * Gets all the cached models
     * @return array
     */
    public function getModels();

    /**
     * Gets the index of the models
     * @return array
     */
    public function getModelIndex();

    /**
     * Clears the cache of the models
     * @return null
     */
    public function clearModels();

    /**
     * Clears the complete cache
     * @return null
     */
    public function clear();

}