<?php

namespace zibo\orm\builder\wizard;

use zibo\library\database\definition\Index;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RegexValidator;
use zibo\library\validation\validator\RequiredValidator;
use zibo\library\wizard\step\AbstractWizardStep;

use zibo\orm\builder\table\decorator\IndexRemoveActionDecorator;
use zibo\orm\builder\table\SimpleModelIndexTable;
use zibo\orm\builder\view\wizard\IndexStepView;

/**
 * Step to setup the indexes of a model
 */
class IndexStep extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'index';

    /**
     * Action to edit a index
     * @var string
     */
    const ACTION_EDIT = 'edit';

    /**
     * Name of the index name field
     * @var string
     */
    const FIELD_INDEX_NAME = 'indexName';

    /**
     * Name of the index fields field
     * @var string
     */
    const FIELD_INDEX_FIELDS = 'indexFields';

    /**
     * Name for the add button
     * @var string
     */
    const BUTTON_ADD = 'add';

    /**
     * Translation key for the add button
     * @var string
     */
    const TRANSLATION_ADD = 'button.add';

    /**
     * Name for the remove button
     * @var string
     */
    const BUTTON_REMOVE = 'remove';

    /**
     * Translation key for the add button
     * @var string
     */
    const TRANSLATION_REMOVE = 'button.remove';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        if (!$this->wizard->isLimitedToIndex()) {
            $field = $this->wizard->getField(self::FIELD_INDEX_FIELDS);
            if (!$field->getOptions()) {
                $this->wizard->setVariable(BuilderWizard::VARIABLE_CURRENT_STEP, FinishStep::NAME);

                $request = $this->wizard->getRequest();
                $response = $this->wizard->getResponse();

                $response->setRedirect($request->getBasePath());
                return;
            }
        }

        $modelTable = $this->wizard->getModelTable();

        if ($this->wizard->isSubmitted()) {
            if ($this->wizard->getValue(self::BUTTON_ADD)) {
                return $this->processAdd($modelTable);
            }

            $remove = $this->wizard->getValue(self::BUTTON_REMOVE);
            if ($remove) {
                return $this->processRemove($modelTable, $remove);
            }
        }

        $request = $this->wizard->getRequest();
        $parameters = $request->getParameters();
        if ($parameters) {
            $numParameters = count($parameters);

            if ($numParameters == 2 && $parameters[0] == self::ACTION_EDIT) {
                $index = $modelTable->getIndex($parameters[1]);
                $this->wizard->setValue(self::FIELD_INDEX_NAME, $index->getName());
                $this->wizard->setValue(self::FIELD_INDEX_FIELDS, $index->getFields());
            }
        }

        return $this->getIndexView($modelTable);
    }

    /**
     * Processes the add action
     * @param zibo\library\orm\definition\ModelTable $modelTable
     * @return null
     */
    private function processAdd(ModelTable $modelTable) {
        try {
            $this->wizard->validate();
        } catch (ValidationException $exception) {
            return $this->getIndexView($modelTable);
        }

        $indexName = $this->wizard->getValue(self::FIELD_INDEX_NAME);
        $indexFieldNames = $this->wizard->getValue(self::FIELD_INDEX_FIELDS);

        $indexFields = array();
        foreach ($indexFieldNames as $indexFieldName) {
            $indexFields[$indexFieldName] = $modelTable->getField($indexFieldName);
        }

        $index = new Index($indexName, $indexFields);

        $modelTable->setIndex($index);

        $this->wizard->setVariable(BuilderWizard::VARIABLE_MODEL_TABLE, $modelTable);

        $request = $this->wizard->getRequest();
        $response = $this->wizard->getResponse();

        $response->setRedirect($request->getBasePath());

        return null;
    }

    /**
     * Processes the remove action
     * @param zibo\library\orm\definition\ModelTable $modelTable
     * @return null
     */
    private function processRemove(ModelTable $modelTable, array $remove) {
        $remove = array_keys($remove);

        foreach ($remove as $indexName) {
            $modelTable->removeIndex($indexName);
        }

        $this->wizard->setVariable(BuilderWizard::VARIABLE_MODEL_TABLE, $modelTable);

        $request = $this->wizard->getRequest();
        $response = $this->wizard->getResponse();

        $response->setRedirect($request->getBasePath());

        return null;
    }

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    private function getIndexView(ModelTable $modelTable) {
        $request = $this->wizard->getRequest();

        $indexTable = new SimpleModelIndexTable($modelTable, $request->getBasePath() . '/' . self::ACTION_EDIT . '/');
        $indexTable->addDecorator(new IndexRemoveActionDecorator($this->wizard));

        return new IndexStepView($this->wizard, $indexTable);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $modelTable = $this->wizard->getModelTable();

        $indexName = null;
        $indexFields = null;

        $fieldFactory = FieldFactory::getInstance();

        $indexNameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_INDEX_NAME, $indexName);
        $indexNameField->addValidator(new RegexValidator(array(RegexValidator::OPTION_REGEX => ModelField::REGEX_NAME)));

        $indexFieldsField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_INDEX_FIELDS, $indexFields);
        $indexFieldsField->setIsMultiple(true);
        $indexFieldsField->setOptions($modelTable->getIndexFields());
        $indexFieldsField->addValidator(new RequiredValidator());

        $addButton = $fieldFactory->createSubmitField(self::BUTTON_ADD, self::TRANSLATION_ADD);

        $translator = I18n::getInstance()->getTranslator();
        $translationRemove = $translator->translate(self::TRANSLATION_REMOVE);

        $indexes = $modelTable->getIndexes();
        foreach ($indexes as $indexName => $indexFields) {
            $indexes[$indexName] = $translationRemove;
        }

        $removeButton = $fieldFactory->createField(FieldFactory::TYPE_SUBMIT, self::BUTTON_REMOVE);
        $removeButton->setIsMultiple(true);
        $removeButton->setOptions($indexes);

        $this->wizard->addField($indexNameField);
        $this->wizard->addField($indexFieldsField);
        $this->wizard->addField($addButton);
        $this->wizard->addField($removeButton);
    }

    /**
     * Processes the next action of this step
     * @return strin Name of the previous step
     */
    public function next() {
        return FinishStep::NAME;
    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        return !$this->wizard->isLimitedToIndex();
    }

    /**
     * Processes the previous action of this step
     * @return strin Name of the previous step
     */
    public function previous() {
        return DataFormatStep::NAME;
    }

}