<?php

namespace zibo\library\orm\builder;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\exception\ModelIncompleteException;
use zibo\library\orm\exception\ModelDeleteException;
use zibo\library\orm\exception\ModelException;
use zibo\library\orm\loader\io\XmlModelIO;
use zibo\library\orm\loader\ModelReader;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\model\Model;
use zibo\library\orm\ModelManager;
use zibo\library\ObjectFactory;
use zibo\library\String;
use zibo\library\Structure;

use zibo\ZiboException;

use \Exception;

/**
 * Register of custom build models
 */
class ModelBuilder {

    /**
     * Directory to store the registered models
     * @var zibo\library\filesystem\File
     */
    private $directory;

    /**
     * The current model loader
     * @var zibo\library\orm\loader\ModelLoader
     */
    private $modelLoader;

    /**
     * The reader of the model loader
     * @var zibo\library\orm\loader\ModelReader
     */
    private $modelReader;

    /**
     * Constructs a new model builder
     * @param zibo\library\filesystem\File $directory Directory to store the registered models
     * @param zibo\library\orm\loader\ModelLoader $modelLoader
     * @return null
     */
    public function __construct(File $directory = null, ModelLoader $modelLoader = null) {
        if (!$directory) {
            $directory = new File(Zibo::DIRECTORY_APPLICATION);
        }

        if (!$modelLoader) {
            $modelManager = ModelManager::getInstance();
            $modelLoader = $modelManager->getModelLoader();
        }

        $this->directory = $directory;
        $this->modelLoader = $modelLoader;
        $this->modelReader = $this->modelLoader->getModelReader();
    }

    /**
     * Creates a model
     * @param zibo\library\orm\definition\ModelTable $table Table definition of the model
     * @param string $modelClassName Class name for the model
     * @param string $dataClassName Class name for the data objects of the model
     * @return zibo\library\orm\model\Model
     * @throws
     */
    public function createModel(ModelTable $table, $modelClassName = null, $dataClassName = null) {
        if (is_null($modelClassName)) {
            $modelClassName = ModelManager::DEFAULT_MODEL;
        } elseif (String::isEmpty($modelClassName)) {
            throw new ModelException('Provided class name for the model is empty');
        }

        $modelMeta = new ModelMeta($table, $dataClassName);

        $objectFactory = new ObjectFactory();
        return $objectFactory->create($modelClassName, ModelManager::INTERFACE_MODEL, array($modelMeta));
    }

    /**
     * Registers a new model, taking into account the models defined by modules
     * @param zibo\library\orm\model\Model $model
     * @param boolean $defineModels
     * @return null
     */
    public function registerModel(Model $model, $defineModels = true) {
        $modelName = $model->getName();

        $moduleModel = $this->getModuleModel($modelName);

        if ($moduleModel) {
            $this->checkModuleModel($moduleModel, $model);
        }

        $builderModels = $this->getBuilderModels();

        $builderModels[$modelName] = $model;

        $this->writeBuilderModels($builderModels);

        $this->defineModels($defineModels);
    }

    /**
     * Unregisteres a model from the builder
     * @param string $modelName Name of the model
     * @param boolean $defineModels
     * @return boolean True when the model is completly removed, false when the model is still defined by a module
     * @throws zibo\ZiboException when the provided model name is empty or not a string
     * @throws zibo\library\orm\exception\ModelDeleteException when the model is not defined by this builder
     */
    public function unregisterModel($modelName, $defineModels = true) {
        if (String::isEmpty($modelName)) {
            throw new ZiboException('Provided model name is empty');
        }

        $builderModels = $this->getBuilderModels();

        if (!array_key_exists($modelName, $builderModels)) {
            throw new ModelDeleteException('Model is not registered in this builder');
        }

        unset($builderModels[$modelName]);

        $this->writeBuilderModels($builderModels);

        $this->defineModels($defineModels);

        $moduleModel = $this->getModuleModel($modelName);

        if ($moduleModel) {
            return false;
        }
        return true;
    }

    /**
     * Creates a model definition file of the provided models
     * @param zibo\library\filesystem\File File where the models are to be written
     * @param array $modelNames
     * @return null
     */
    public function exportModels(File $file, array $modelNames) {
        $modelManager = ModelManager::getInstance();

        $models = array();
        foreach ($modelNames as $modelName) {
            $models[] = $modelManager->getModel($modelName);
        }

        $modelIO = new XmlModelIO();
        $modelIO->writeModelsToFile($file, $models);
    }

    /**
     * Import a model definition file
     * @param zibo\library\filesystem\File File where the models are to be read
     * @return null
     */
    public function importModels(File $file, $defineModels = true) {
        $modelIO = new XmlModelIO();
        $models = $modelIO->readModelsFromFile($file);

        foreach ($models as $model) {
            $this->registerModel($model, false);
        }

        $this->defineModels($defineModels);
    }

    /**
     * Gets all the models as defined in the modules
     * @return array Array with Model objects
     */
    public function getModuleModels() {
        return $this->modelReader->readModelsFromIncludePaths(true);
    }

    /**
     * Gets the model as defined in a module
     * @param string $modelName Name of the model to lookup
     * @return null|zibo\library\orm\model\Model Null when the model is not defined in a module, the Model otherwise
     */
    public function getModuleModel($modelName) {
        $models = $this->getModuleModels();

        if (array_key_exists($modelName, $models)) {
            return $models[$modelName];
        }

        return null;
    }

    /**
     * Gets the models as defined by this builder
     * @return array Array with Model objects
     */
    public function getBuilderModels() {
        return $this->modelReader->readModelsFromPath($this->directory);
    }

    /**
     * Store the defined models of this builder
     * @param array Array with Model objects
     * @return null
     */
    private function writeBuilderModels(array $models) {
        $modelIO = $this->modelReader->getModelIO();
        $modelIO->writeModelsToPath($this->directory, $models);
    }

    /**
     * Define the models
     * @param boolean $defineModels True clears the model cache and define database tables, false only clears the model cache
     * @return null
     */
    private function defineModels($defineModels) {
        $modelManager = ModelManager::getInstance();

        if ($defineModels) {
            $modelManager->defineModels();
        } else {
            $modelManager->getModelCache()->clearModels();
        }
    }

    /**
     * Checks if all the necessairy information from the module model is in the builder model
     * @param zibo\library\orm\model\Model $moduleModel
     * @param zibo\library\orm\model\Model $builderModel
     * @return null
     * @throws zibo\library\orm\exception\ModelIncompleteException when the model misses information defined in the module model
     */
    private function checkModuleModel(Model $moduleModel, Model $builderModel) {
        $moduleTable = $moduleModel->getMeta()->getModelTable();
        $builderTable = $builderModel->getMeta()->getModelTable();

        $moduleFields = $moduleTable->getFields();
        foreach ($moduleFields as $fieldName => $moduleField) {
            if (!$builderTable->hasField($fieldName)) {
                throw new ModelIncompleteException('Field ' . $fieldName . ' not found while it\'s defined in the module model');
            }

            $builderField = $builderTable->getField($fieldName);
            $this->checkModuleField($fieldName, $moduleField, $builderField);
        }
    }

    /**
     * Checks if all the necessairy information from the module field is in the builder field
     * @param string $name Name of the field
     * @param zibo\library\orm\model\field\ModelField $moduleField
     * @param zibo\library\orm\model\field\ModelField $builderField
     * @return null
     * @throws zibo\library\orm\exception\ModelIncompleteException when the model misses information defined in the module model
     */
    private function checkModuleField($fieldName, ModelField $moduleField, ModelField $builderField) {
        if ($moduleField->isLocalized() != $builderField->isLocalized()) {
            throw new ModelIncompleteException('Field ' . $fieldName . ' has not the same localized flag as defined in the module model');
        }

        $moduleValidators = $moduleField->getValidators();
        $builderValidators = $moduleField->getValidators();
        foreach ($moduleValidators as $moduleValidator) {
            $validatorFound = false;

            foreach ($builderValidators as $builderValidator) {
                if ($moduleValidator->equals($builderValidator)) {
                    $validatorFound = true;
                    break;
                }
            }

            if (!$validatorFound) {
                throw new ModelIncompleteException('Field ' . $fieldName . ' does not contain the validator ' . $moduleValidator->getName() . ' as defined in the module model');
            }
        }

        if ($moduleField instanceof PropertyField) {
            if (!($builderField instanceof PropertyField)) {
                throw new ModelIncompleteException('Field ' . $fieldName . ' should be a property as defined in the module model');
            }

            if ($moduleField->getType() != $builderField->getType()) {
                throw new ModelIncompleteException('Property ' . $fieldName . ' should be of the type ' . $moduleField->getType() . ' as defined in the module model');
            }

            return;
        }

        $relationModelName = $moduleField->getRelationModelName();
        if ($relationModelName != $builderField->getRelationModelName()) {
            throw new ModelIncompleteException('Field ' . $fieldName . ' should have model ' . $relationModelName . ' as relation model as defined in the module model');
        }

        $moduleLinkModelName = $moduleField->getLinkModelName();
        $builderLinkModelName = $builderField->getLinkModelName();
        if ($moduleLinkModelName && (!$builderLinkModelName || $moduleLinkModelName != $builderLinkModelName)) {
            throw new ModelIncompleteException('Field ' . $fieldName . ' should have model ' . $moduleLinkModelName . ' as link model as defined in the module model');
        }

        if ($moduleField instanceof BelongsToField) {
            if (!($builderField instanceof BelongsToField)) {
                throw new ModelIncompleteException('Field ' . $fieldName . ' should be a belongsTo relation as defined in the module model');
            }
        }

        if ($moduleField instanceof HasOneField) {
            if (!($builderField instanceof HasOneField)) {
                throw new ModelIncompleteException('Field ' . $fieldName . ' should be a hasOne relation as defined in the module model');
            }
        }

        if ($moduleField instanceof HasManyField) {
            if (!($builderField instanceof HasManyField)) {
                throw new ModelIncompleteException('Field ' . $fieldName . ' should be a hasMany relation as defined in the module model');
            }
        }
    }

}