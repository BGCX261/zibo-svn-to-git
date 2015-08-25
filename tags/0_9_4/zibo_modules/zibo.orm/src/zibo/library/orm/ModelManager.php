<?php

namespace zibo\library\orm;

use zibo\library\orm\cache\ModelCache;
use zibo\library\orm\exception\OrmException;
use zibo\library\orm\loader\ModelLoader;
use zibo\library\orm\loader\ModelReader;
use zibo\library\orm\model\Model;
use zibo\library\ObjectFactory;
use zibo\library\String;

/**
 * Manager of the models
 */
class ModelManager {

    /**
     * Configuration key for the i/o implementation of the model definitions
     * @var string
     */
    const CONFIG_MODEL_IO = 'orm.model.io';

    /**
     * Configuration key for the cache implementation
     * @var string
     */
    const CONFIG_MODEL_CACHE = 'orm.model.cache';

    /**
     * Interface class name of a model
     * @var string
     */
    const INTERFACE_MODEL = 'zibo\\library\\orm\\model\\Model';

    /**
     * Interface class name for the i/o implementation of the model definitions
     * @var string
     */
    const INTERFACE_MODEL_IO = 'zibo\\library\\orm\\loader\\io\\ModelIO';

    /**
     * Interface class name for a model
     * @var string
     */
    const INTERFACE_MODEL_CACHE = 'zibo\\library\\orm\\cache\\ModelCache';

    /**
     * Default class for a model
     * @var string
     */
    const DEFAULT_MODEL = 'zibo\\library\\orm\\model\\ExtendedModel';

    /**
     * Default class name for the i/o implementation of the model definitions
     * @var string
     */
    const DEFAULT_MODEL_IO = 'zibo\\library\\orm\\loader\\io\\XmlModelIO';

    /**
     * Default class for the model cache
     * @var string
     */
    const DEFAULT_MODEL_CACHE = 'zibo\\library\\orm\\cache\\DisabledModelCache';

    /**
     * Instance of the model manager, singleton pattern
     * @var ModelManager
     */
    private static $instance;

    /**
     * Array with the loaded models
     * @var array
     */
    private $models;

    /**
     * Loader of the models
     * @var zibo\library\orm\model\loader\ModelLoader
     */
    private $modelLoader;

    /**
     * Cache for the queries and results
     * @var zibo\library\orm\cache\ModelCache
     */
    private $modelCache;

    /**
     * Constructs a new model manager
     * @return null
     */
    private function __construct() {
        $this->models = array();

        $objectFactory = new ObjectFactory();
        $modelIO = $objectFactory->createFromConfig(self::CONFIG_MODEL_IO, self::DEFAULT_MODEL_IO, self::INTERFACE_MODEL_IO);

        $modelReader = new ModelReader($modelIO);

        $this->modelLoader = new ModelLoader($modelReader);

        $this->modelCache = $objectFactory->createFromConfig(self::CONFIG_MODEL_CACHE, self::DEFAULT_MODEL_CACHE, self::INTERFACE_MODEL_CACHE);
    }

    /**
     * Gets the instance of the ModelManager
     * @return ModelManager
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Adds a model to the manager
     * @param zibo\library\orm\model\Model $model
     * @return null
     */
    public function addModel(Model $model) {
        $this->models[$model->getName()] = $model;
    }

    /**
     * Removes a model from the manager
     * @param string $modelName
     * @return null
     * @throws zibo\ZiboException when the provided model name is empty or not a string
     * @throws zibo\library\orm\exception\OrmException when the model is not loaded
     */
    public function removeModel($modelName) {
        if (String::isEmpty($modelName)) {
            throw new OrmException('Provided model name is empty');
        }

        if (!array_key_exists($modelName, $this->models)) {
            throw new OrmException('Model ' . $modelName . ' is not loaded.');
        }

        unset($this->models[$modelName]);
    }

    /**
     * Checks whether a model exists or not
     * @param string $modelName Name of the model
     * @return boolean True when the model exists, false otherwise
     * @throws zibo\ZiboException when the provided model name is empty or not a string
     */
    public function hasModel($modelName) {
        if (String::isEmpty($modelName)) {
            throw new OrmException('Provided model name is empty');
        }

        if (array_key_exists($modelName, $this->models)) {
            return true;
        }

        try {
            $model = $this->modelLoader->loadModel($modelName);
        } catch (OrmException $exception) {
            return false;
        }

        $this->addModel($model);

        return true;
    }

    /**
     * Gets a model
     * @param string $modelName
     * @return Model
     * @throws zibo\ZiboException when $modelName is not a string
     * @throws zibo\library\orm\exception\OrmException when $modelName is empty or when the model does not exists
     */
    public function getModel($modelName) {
        if (!$this->hasModel($modelName)) {
            throw new OrmException('Model ' . $modelName . ' does not exist');
        }

        return $this->models[$modelName];
    }

    /**
     * Get the loaded models
     * @param boolean $loadAllModels Set to true to load all the models
     * @return array Array with Model objects
     */
    public function getModels($loadAllModels = false) {
        if ($loadAllModels) {
            $this->models = $this->modelLoader->loadModels();
        }

        return $this->models;
    }

    /**
     * Gets the model cache
     * @return zibo\library\orm\cache\ModelCache
     */
    public function getModelCache() {
        return $this->modelCache;
    }

    /**
     * Clears the cache of the model loader
     * @return null
     */
    public function clearCache() {
        $this->modelCache->clear();
    }

    /**
     * Defines all the models in the database
     * @return null
     */
    public function defineModels() {
        $this->clearCache();

        $this->models = $this->getModels(true);

        $definer = new ModelDefiner();
        $definer->defineModels($this->models);
    }

    /**
     * Defines all the models in the database
     * @return null
     */
    public function getUnusedTables() {
        $this->clearCache();

        $this->models = $this->getModels(true);

        $definer = new ModelDefiner();
        return $definer->getUnusedTables($this->models);
    }

    /**
     * Gets the model loader
     * @return zibo\library\orm\loader\ModelLoader
     */
    public function getModelLoader() {
        return $this->modelLoader;
    }

}