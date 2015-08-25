<?php

namespace zibo\orm\builder\wizard;

use zibo\core\Zibo;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\orm\builder\ModelBuilder;
use zibo\library\orm\definition\ModelTable;
use zibo\library\wizard\step\AbstractWizardStep;

use zibo\orm\builder\table\SimpleDataFormatTable;
use zibo\orm\builder\table\SimpleModelFieldTable;
use zibo\orm\builder\table\SimpleModelIndexTable;
use zibo\orm\builder\view\wizard\FinishStepView;
use zibo\orm\Module;

/**
 * Step to ask the general info of a model
 */
class FinishStep extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'finish';

    /**
     * Default value for the define model field
     * @var boolean
     */
    const DEFAULT_DEFINE_MODEL = true;

    /**
     * Name of the define model field
     * @var string
     */
    const FIELD_DEFINE_MODEL = 'modelDefine';

    /**
     * The exception which occured while writing the model
     * @var Exception
     */
    private $exception;

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        $modelTable = $this->wizard->getModelTable();

        $fieldTable = new SimpleModelFieldTable($modelTable);
        $formatTable = new SimpleDataFormatTable($modelTable);
        $indexTable = new SimpleModelIndexTable($modelTable);

        return new FinishStepView($this->wizard, $modelTable, $fieldTable, $formatTable, $indexTable);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $fieldFactory = FieldFactory::getInstance();

        $modelDefineField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_DEFINE_MODEL, self::DEFAULT_DEFINE_MODEL);

        $this->wizard->addField($modelDefineField);
    }

    /**
     * Processes the finish action of this step. This will write the model to the system
     * @return null
     */
    public function finish() {
        $defineModel = $this->wizard->getValue(self::FIELD_DEFINE_MODEL);

        $modelTable = $this->wizard->getModelTable();
        $modelClass = $this->wizard->getVariable(BuilderWizard::VARIABLE_MODEL_CLASS);
        $dataClass = $this->wizard->getVariable(BuilderWizard::VARIABLE_DATA_CLASS);

        try {
            $modelBuilder = new ModelBuilder();

            $model = $modelBuilder->createModel($modelTable, $modelClass, $dataClass);
            $modelBuilder->registerModel($model, $defineModel);

            $request = $this->wizard->getRequest();
            $response = $this->wizard->getResponse();

            $this->wizard->reset();

            $response->setRedirect($this->wizard->getCancelUrl());
        } catch (Exception $exception) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
            $this->exception = $exception;
        }

        return null;
    }

    /**
     * Processes the previous action of this step
     * @return strin Name of the previous step
     */
    public function previous() {
        $modelTable = $this->wizard->getModelTable();

        $limitField = $this->wizard->getLimitField();
        if ($limitField) {
            if ($limitField === true) {
                $fields = $modelTable->getFields();
                $field = array_pop($fields);
                $fieldName = $field->getName();
                if ($fieldName == ModelTable::PRIMARY_KEY) {
                    $fieldName = null;
                }

                $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD, $fieldName);
            }

            return FieldStep::NAME;
        }

        if ($this->wizard->isLimitedToModel()) {
            return ModelExtraStep::NAME;
        }

        if ($this->wizard->isLimitedToIndex()) {
            return IndexStep::NAME;
        }

        if ($modelTable->getIndexFields()) {
            return IndexStep::NAME;
        } else {
            return ModelExtraStep::NAME;
        }
    }

    /**
     * Gets whether this step has a finish step
     * @return boolean
     */
    public function hasFinish() {
        return true;
    }

    /**
     * Gets whether this step has a next step
     * @return boolean
     */
    public function hasNext() {
        return false;
    }

}