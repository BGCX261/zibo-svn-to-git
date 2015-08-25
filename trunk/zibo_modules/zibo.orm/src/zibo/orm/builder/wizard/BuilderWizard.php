<?php

namespace zibo\orm\builder\wizard;

use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\ModelManager;
use zibo\library\wizard\Wizard;

use zibo\ZiboException;

/**
 * Wizard to create a new model
 */
class BuilderWizard extends Wizard {

    /**
     * Name of the wizard
     * @var string
     */
    const NAME = 'wizardModel';

    /**
     * Value to limit the wizard to the model steps
     * @var string
     */
    const LIMIT_MODEL = 'model';

    /**
     * Value to limit the wizard to the index step
     * @var string
     */
    const LIMIT_INDEX = 'index';

    /**
     * Value to limit the wizard to the data formats step
     * @var string
     */
    const LIMIT_DATA_FORMAT = 'format';

    /**
     * Name for the model table variable
     * @var string
     */
    const VARIABLE_MODEL_TABLE = 'modelTable';

    /**
     * Name for the model class variable
     * @var string
     */
    const VARIABLE_MODEL_CLASS = 'modelClass';

    /**
     * Name for the data class variable
     * @var string
     */
    const VARIABLE_DATA_CLASS = 'dataClass';

    /**
     * Name for the current field variable
     * @var string
     */
    const VARIABLE_FIELD = 'field';

    /**
     * Name for the limit field
     * @var string
     */
    const VARIABLE_LIMIT = 'limit';

    /**
     * Name for the limit field field
     * @var string
     */
    const VARIABLE_LIMIT_FIELD = 'limitField';

    /**
     * Name for the is new model field
     * @var string
     */
    const VARIABLE_MODEL_EXISTS = 'modelExists';

    /**
     * Constructs a new model wizard
     * @param string $action URL where the wizard form will point to
     * @param string $modelName Name of a model to initialize the wizard with
     * @return null
     */
    public function __construct($action, $modelName = null) {
        parent::__construct($action, self::NAME);

        $this->addStep(ModelGeneralStep::NAME, new ModelGeneralStep());
        $this->addStep(FieldStep::NAME, new FieldStep());
        $this->addStep(DataFormatStep::NAME, new DataFormatStep());
        $this->addStep(IndexStep::NAME, new IndexStep());
        $this->addStep(FinishStep::NAME, new FinishStep());

        if (!$modelName) {
            return;
        }

        $this->reset();

        $model = ModelManager::getInstance()->getModel($modelName);
        $meta = $model->getMeta();
        $modelTable = clone($meta->getModelTable());
        $modelClass = get_class($model);
        $dataClass = $meta->getDataClassName();

        $this->setVariable(self::VARIABLE_MODEL_TABLE, $modelTable);
        $this->setVariable(self::VARIABLE_MODEL_CLASS, $modelClass);
        $this->setVariable(self::VARIABLE_DATA_CLASS, $dataClass);
        $this->setVariable(self::VARIABLE_MODEL_EXISTS, true);
    }

    /**
     * Sets the wizard on the field step for the provided field
     * @param string $fieldName
     * @return null
     */
    public function gotoFieldStep($fieldName) {
        $modelTable = $this->getModelTable();
        if (!$modelTable) {
            throw new ZiboException('Wizard not yet initialized with a model');
        }

        if (!$modelTable->hasField($fieldName)) {
            throw new ZiboException('Field is not set in the model');
        }

        $this->setVariable(self::VARIABLE_FIELD, $fieldName);
        $this->setVariable(self::VARIABLE_CURRENT_STEP, FieldStep::NAME);
    }

    /**
     * Gets whether the wizard is editing a new model or not
     * @return boolean
     */
    public function isNewModel() {
        return !$this->getVariable(self::VARIABLE_MODEL_EXISTS, false);
    }

    /**
     * Limits the wizard to only the provided field
     * @param string $fieldName Name of the field to limit the wizard to
     * @return null
     */
    public function limitToField($fieldName) {
        if ($fieldName !== true) {
            $modelTable = $this->getModelTable();
            if (!$modelTable->hasField($fieldName)) {
                throw new ZiboException('Provided field not found in model ' . $modelTable->getName());
            }
        }

        $this->setVariable(self::VARIABLE_LIMIT_FIELD, $fieldName);
        $this->setVariable(self::VARIABLE_CURRENT_STEP, FieldStep::NAME);

        if ($fieldName === true) {
            $this->setVariable(self::VARIABLE_FIELD, false);
        } else {
            $this->setVariable(self::VARIABLE_FIELD, $fieldName);
        }
    }

    /**
     * Gets the name of the field this wizard is limited to
     * @return nulł|string
     */
    public function getLimitField() {
        return $this->getVariable(self::VARIABLE_LIMIT_FIELD);
    }

    /**
     * Limits the wizard to the model steps
     * @return null
     */
    public function limitToModel() {
        $this->setVariable(self::VARIABLE_LIMIT, self::LIMIT_MODEL);
        $this->setVariable(self::VARIABLE_CURRENT_STEP, ModelGeneralStep::NAME);
    }

    /**
     * Gets whether this wizard is limited to the model steps
     * @return boolean
     */
    public function isLimitedToModel() {
        return $this->getVariable(self::VARIABLE_LIMIT) == self::LIMIT_MODEL;
    }

    /**
     * Limits the wizard to the index step
     * @return null
     */
    public function limitToIndex() {
        $this->setVariable(self::VARIABLE_LIMIT, self::LIMIT_INDEX);
        $this->setVariable(self::VARIABLE_CURRENT_STEP, IndexStep::NAME);
    }

    /**
     * Gets whether this wizard is limited to the index step
     * @return boolean
     */
    public function isLimitedToIndex() {
        return $this->getVariable(self::VARIABLE_LIMIT) == self::LIMIT_INDEX;
    }

    /**
     * Limits the wizard to the data formats step
     * @return null
     */
    public function limitToDataFormats() {
        $this->setVariable(self::VARIABLE_LIMIT, self::LIMIT_DATA_FORMAT);
        $this->setVariable(self::VARIABLE_CURRENT_STEP, DataFormatStep::NAME);
    }

    /**
     * Gets whether this wizard is limited to the data formats
     * @return boolean
     */
    public function isLimitedToDataFormats() {
        return $this->getVariable(self::VARIABLE_LIMIT) == self::LIMIT_DATA_FORMAT;
    }

    /**
     * Gets the model table this wizard is working on
     * @return zibo\library\orm\definition\ModelTable
     */
    public function getModelTable() {
        return $this->getVariable(self::VARIABLE_MODEL_TABLE);
    }

    /**
     * Sets the model table to this wizard
     * @param zibo\library\orm\definition\ModelTable $modelTable
     * @return null
     */
    public function setModelTable(ModelTable $modelTable) {
        $this->setVariable(self::VARIABLE_MODEL_TABLE, $modelTable);
    }

}