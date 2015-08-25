<?php

namespace zibo\library\orm\cache;

use zibo\library\filesystem\File;
use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\io\TypeMemoryCacheIO;
use zibo\library\cache\SimpleCache;
use zibo\library\orm\model\Model;

/**
 * File cache implementation for the orm
 */
class FileModelCache implements ModelCache {

    /**
     * Path for the cache
     * @var string
     */
    const CACHE_PATH = 'application/data/cache/orm';

    /**
     * Cache type for the indexes
     * @var string
     */
    const CACHE_TYPE_INDEX = 'index';

    /**
     * Cache type for the models
     * @var string
     */
    const CACHE_TYPE_MODEL = 'model';

    /**
     * Cache type for the queries
     * @var string
     */
    const CACHE_TYPE_QUERY = 'query';

    /**
     * Cache type for the results
     * @var string
     */
    const CACHE_TYPE_RESULT = 'result';

    /**
     * The cache
     * @var zibo\library\cache\Cache
     */
    private $cache;

    /**
     * The directory for the result index
     * @var zibo\library\filesystem\File
     */
    private $directoryResultIndex;

    /**
     * Array with the names of the available models
     * @var array
     */
    private $indexModel;

    /**
     * Construct a new model query cache
     * @return null
     */
    public function __construct() {
        $cachePath = new File(self::CACHE_PATH);

        $cacheIO = new FileCacheIO($cachePath);
        $this->cache = new SimpleCache($cacheIO);

        $this->directoryResultIndex = new File($cachePath, self::CACHE_TYPE_INDEX . File::DIRECTORY_SEPARATOR . self::CACHE_TYPE_RESULT);

        $this->readModelIndex();
    }

    /**
     * Gets the SQL of a query from the cache
     * @param string $queryId Unique identifier of the query
     * @return null|QueryCacheObject The query cache object if cached, null otherwise
     */
    public function getQuery($queryId) {
        return $this->cache->get(self::CACHE_TYPE_QUERY, $queryId);
    }

    /**
     * Sets the SQL of a query to the cache
     * @param string $queryId Unique identifier of the query
     * @param string $queryCacheObject Query cache object containing the cached data of the query
     * @return null
     */
    public function setQuery($queryId, QueryCacheObject $queryCacheObject) {
        $this->cache->set(self::CACHE_TYPE_QUERY, $queryId, $queryCacheObject);
    }

    /**
     * Clears the query cache
     * @return null
     */
    public function clearQueries() {
        $this->cache->clear(self::CACHE_TYPE_QUERY);
    }

    /**
     * Gets the result of a query from the cache
     * @param string $resultId Unique identifier of the result
     * @return null|ResultCacheObject The result cache object if cached, null otherwise
     */
    public function getResult($resultId) {
        return $this->cache->get(self::CACHE_TYPE_RESULT, $resultId);
    }

    /**
     * Sets the result of a query to the cache
     * @param string $resultId Unique identifier of the result
     * @param ResultCacheObject $resultCacheObject Result cache object containing the cached data of the result
     * @param array $usedModels Array with the names of the models used by the query
     * @return null
     */
    public function setResult($resultId, ResultCacheObject $resultCacheObject, array $usedModels) {
        $this->cache->set(self::CACHE_TYPE_RESULT, $resultId, $resultCacheObject);

        $this->indexResult($resultId, $usedModels);
    }

    /**
     * Clears the result cache for all the queries which use the provided model
     * @param string $modelName Name of the model
     * @return null
     */
    public function clearResults($modelName) {
        $directoryIndex = new File($this->directoryResultIndex, $modelName);

        if (!$directoryIndex->exists()) {
            return;
        }

        $files = $directoryIndex->read();
        foreach ($files as $file) {
            $this->cache->clear(self::CACHE_TYPE_RESULT, $file->getName());
        }

        $directoryIndex->delete();
    }

    /**
     * Indexes the result of a query
     * @param string $resultId Unique identifier of the result
     * @param array $modelName Array with the names of the models used in the query of this result
     * @return null
     */
    private function indexResult($resultId, array $modelNames) {
        foreach ($modelNames as $modelName) {
            $file = new File($this->directoryResultIndex, $modelName . File::DIRECTORY_SEPARATOR . $resultId);

            $parent = $file->getParent();
            $parent->create();

            $file->write();
        }
    }

    /**
     * Sets a model to the cache
     * @param zibo\library\orm\model\Model $model Model to cache
     * @return null
     */
    public function setModel(Model $model) {
        $modelName = $model->getName();

        $this->cache->set(self::CACHE_TYPE_MODEL, $modelName, $model);

        $this->indexModel[$modelName] = $modelName;

        $this->cache->set(self::CACHE_TYPE_INDEX, self::CACHE_TYPE_MODEL, $this->indexModel);
    }

    /**
     * Gets a model from the cache
     * @return zibo\library\orm\model\Model
     */
    public function getModel($modelName) {
        return $this->cache->get(self::CACHE_TYPE_MODEL, $modelName);
    }

    /**
     * Gets all the cached models
     * @return array
     */
    public function getModels() {
        return $this->cache->get(self::CACHE_TYPE_MODEL);
    }

    /**
     * Gets the index of the models
     * @return array
     */
    public function getModelIndex() {
        return $this->indexModel;
    }

    /**
     * Reads the index of the models
     * @return null
     */
    private function readModelIndex() {
        $this->indexModel = $this->cache->get(self::CACHE_TYPE_INDEX, self::CACHE_TYPE_MODEL, array());
    }

    /**
     * Clears the cache of the models
     * @return null
     */
    public function clearModels() {
        $this->cache->clear(self::CACHE_TYPE_INDEX, self::CACHE_TYPE_MODEL);
        $this->cache->clear(self::CACHE_TYPE_MODEL);

        $this->indexModel = array();
    }

    /**
     * Clears the complete cache
     * @return null
     */
    public function clear() {
        $this->cache->clear();

        $this->indexModel = array();
    }

}