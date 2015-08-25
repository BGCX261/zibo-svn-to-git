<?php

namespace zibo\orm\builder\wizard;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\ModelManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RegexValidator;
use zibo\library\validation\validator\ClassValidator;
use zibo\library\wizard\step\AbstractWizardStep;

use zibo\orm\builder\view\wizard\ModelGeneralStepView;

/**
 * Step to ask the general info of a model
 */
class ModelGeneralStep extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'general';

    /**
     * Name of the model name field
     * @var string
     */
    const FIELD_MODEL_NAME = 'modelName';

    /**
     * Name of the is logged field
     * @var string
     */
    const FIELD_IS_LOGGED = 'isLogged';

    /**
     * Name of the will block delete field
     * @var string
     */
    const FIELD_WILL_BLOCK_DELETE = 'willBlockDelete';

    /**
     * Name of the model class field
     * @var string
     */
    const FIELD_MODEL_CLASS = 'modelClass';

    /**
     * Name of the data class field
     * @var string
     */
    const FIELD_DATA_CLASS = 'dataClass';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        return new ModelGeneralStepView($this->wizard);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $modelTable = $this->wizard->getModelTable();
        $modelClass = $this->wizard->getVariable(BuilderWizard::VARIABLE_MODEL_CLASS);
        $dataClass = $this->wizard->getVariable(BuilderWizard::VARIABLE_DATA_CLASS);

        $modelName = null;
        $isLogged = null;
        $willBlockDelete = null;
        if ($modelTable) {
            $modelName = $modelTable->getName();
            $isLogged = $modelTable->isLogged();
            $willBlockDelete = $modelTable->willBlockDeleteWhenUsed();
        }

        if (!$isLogged) {
            $isLogged = false;
        }
        if (!$willBlockDelete) {
            $willBlockDelete = false;
        }

        $fieldFactory = FieldFactory::getInstance();

        $modelNameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_MODEL_NAME, $modelName);
        $modelNameField->addValidator(new RegexValidator(array(RegexValidator::OPTION_REGEX => ModelTable::REGEX_NAME)));
        $modelNameField->setIsDisabled(!$this->wizard->isNewModel());

        $isLoggedField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_IS_LOGGED, $isLogged);

        $willBlockDeleteField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_WILL_BLOCK_DELETE, $willBlockDelete);

        $modelClassField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_MODEL_CLASS, $modelClass);
        $modelClassField->addValidator(new ClassValidator(array(ClassValidator::OPTION_REQUIRED => false, ClassValidator::OPTION_CLASS => ModelManager::INTERFACE_MODEL)));

        $dataClassField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_DATA_CLASS, $dataClass);
        $dataClassField->addValidator(new ClassValidator(array(ClassValidator::OPTION_REQUIRED => false)));

        $this->wizard->addField($modelNameField);
        $this->wizard->addField($isLoggedField);
        $this->wizard->addField($willBlockDeleteField);
        $this->wizard->addField($modelClassField);
        $this->wizard->addField($dataClassField);
    }

    /**
     * Processes the next action of this step
     * return string Name of the next step
     */
    public function next() {
        try {
            $this->wizard->validate();
        } catch (ValidationException $validationException) {
            return null;
        }

        if ($this->wizard->isNewModel()) {
            $modelName = $this->wizard->getValue(self::FIELD_MODEL_NAME);
        } else {
            $modelTable = $this->wizard->getModelTable();
            $modelName = $modelTable->getName();
        }

        $isLogged = $this->wizard->getValue(self::FIELD_IS_LOGGED);
        $willBlockDelete = $this->wizard->getValue(self::FIELD_WILL_BLOCK_DELETE);

        $modelClass = $this->wizard->getValue(self::FIELD_MODEL_CLASS);
        if (!$modelClass) {
            $modelClass = null;
        }

        $dataClass = $this->wizard->getValue(self::FIELD_DATA_CLASS);
        if (!$dataClass) {
            $dataClass = null;
        }

        $modelTable = $this->wizard->getModelTable();
        if ($modelTable) {
            if ($modelTable->getName() != $modelName || $modelTable->isLogged() != $isLogged) {
                $newModelTable = new ModelTable($modelName, $isLogged);

                $this->copyModel($modelTable, $newModelTable);
            } else {
                $newModelTable = $modelTable;
            }
        } else {
            $newModelTable = new ModelTable($modelName, $isLogged);
        }

        $newModelTable->setWillBlockDeleteWhenUsed($willBlockDelete);

        $this->wizard->setModelTable($newModelTable);
        $this->wizard->setVariable(BuilderWizard::VARIABLE_MODEL_CLASS, $modelClass);
        $this->wizard->setVariable(BuilderWizard::VARIABLE_DATA_CLASS, $dataClass);

        if ($this->wizard->isLimitedToModel()) {
            return FinishStep::NAME;
        }

        return FieldStep::NAME;
    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        return false;
    }

    /**
     * Copies a model table into another
     * @param zibo\library\orm\definition\ModelTable $source
     * @param zibo\library\orm\definition\ModelTable $destination
     * @return null
     */
    private function copyModel(ModelTable $source, ModelTable $destination) {
        $destination->setWillBlockDeleteWhenUsed($source->willBlockDeleteWhenUsed);

        $fields = $source->getFields();
        foreach ($fields as $field) {
            if ($field->getName() == ModelTable::PRIMARY_KEY) {
                continue;
            }
            $destination->addField($field);
        }

        $indexes = $source->getIndexes();
        foreach ($indexes as $index) {
            $destination->addIndex($index);
        }

        $dataFormats = $source->getDataFormats();
        foreach ($dataFormats as $name => $format) {
            $destination->setDataFormat($name, $format);
        }
    }

}