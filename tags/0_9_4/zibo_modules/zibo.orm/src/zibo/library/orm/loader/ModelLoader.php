<?php

namespace zibo\library\orm\loader;

use zibo\core\Zibo;

use zibo\library\cache\io\CacheIO;
use zibo\library\cache\io\FileCacheIO;
use zibo\library\cache\SimpleCache;
use zibo\library\filesystem\File;
use zibo\library\orm\exception\OrmException;
use zibo\library\orm\ModelManager;

use zibo\orm\Module;

/**
 * Load the defined models into the ModelManager
 */
class ModelLoader {

    /**
     * Path to cache the models
     * @var string
     */
    const CACHE_PATH = 'application/data/cache/orm';

    /**
     * Object type for the cache
     * @var string
     */
    const CACHE_TYPE = 'model';

    /**
     * Object id for the cache for the index of the models
     * @var string
     */
    const CACHE_ID_INDEX = '_index';

    /**
     * Model reader to read the models from the include paths
     * @var ModelReader
     */
    private $modelReader;

    /**
     * The cache of the models
     * @var zibo\library\cache\Cache
     */
    private $cache;

    /**
     * Index of the available models
     * @var array
     */
    private $index;

    /**
     * Constructs a new model loader
     * @param ModelReader $modelReader Model reader to read the models from the include paths
     * @return null
     */
    public function __construct(ModelReader $modelReader) {
        $this->modelReader = $modelReader;
    }

    /**
     * Gets the model reader
     * @return ModelReader
     */
    public function getModelReader() {
        return $this->modelReader;
    }

    /**
     * Loads a model
     * @param string $modelName Name of the model to load
     * @return zibo\library\orm\Model The model
     * @throws zibo\library\orm\exception\OrmException when the model does not exist
     */
    public function loadModel($modelName) {
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Loading model ' . $modelName, '', 0, Module::LOG_NAME);

        $index = $this->getIndex();

        $models = null;

        if (!$index) {
            $models = $this->registerModels();
            $index = array_keys($models);
        }

        if (!in_array($modelName, $index)) {
            throw new OrmException('Model ' . $modelName . ' does not exist');
        }

        if ($models) {
            return $models[$modelName];
        }

        $cache = $this->getCache();

        $model = $cache->getModel($modelName);
        if ($model) {
            return $model;
        }

        throw new OrmException('Model ' . $modelName . ' is not cached');
    }

    /**
     * Loads all the registered models
     * @return array Array with the loaded models
     */
    public function loadModels() {
        $index = $this->getIndex();

        if (!$index) {
            return $this->registerModels();
        }

        $cache = $this->getCache();

        return $cache->getModels();
    }

    /**
     * Registers all the models defined in the include paths
     * @return array Array with the registered models
     */
    private function registerModels() {
        $models = $this->modelReader->readModelsFromIncludePaths();

        $register = new ModelRegister();
        $register->registerModels($models);

        $models = $register->getModels();

        $this->index = array_keys($models);

        $modelManager = ModelManager::getInstance();
        foreach ($models as $model) {
            $modelManager->addModel($model);
        }

        $cache = $modelManager->getModelCache();

        foreach ($models as $model) {
            $meta = $model->getMeta();
            $meta->getProperties(); // make sure the meta is parsed before caching the model

            $cache->setModel($model);
        }

        return $models;
    }

    /**
     * Gets the index of the models from the cache
     * @return array
     */
    private function getIndex() {
        $modelCache = $this->getCache();
        return $modelCache->getModelIndex();
    }

    /**
     * Gets the cache
     * @return zibo\library\orm\cache\ModelCache
     */
    private function getCache() {
        $modelManager = ModelManager::getInstance();
        return $modelManager->getModelCache();
    }

}