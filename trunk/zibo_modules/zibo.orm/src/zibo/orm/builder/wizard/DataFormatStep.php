<?php

namespace zibo\orm\builder\wizard;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\DataFormat;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RequiredValidator;
use zibo\library\wizard\step\AbstractWizardStep;

use zibo\orm\builder\table\decorator\DataFormatRemoveActionDecorator;
use zibo\orm\builder\table\SimpleDataFormatTable;
use zibo\orm\builder\view\wizard\DataFormatStepView;

/**
 * Step to ask the extra info of a model
 */
class DataFormatStep extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'format';

    /**
     * Action to edit a index
     * @var string
     */
    const ACTION_EDIT = 'edit';

    /**
     * Name of the predefined format names field
     * @var string
     */
    const FIELD_PREDEFINED = 'predefined';

    /**
     * Name of the title format field
     * @var string
     */
    const FIELD_NAME = 'name';

    /**
     * Name of the teaser format field
     * @var string
     */
    const FIELD_FORMAT = 'format';

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
     * Translation key for the title format
     * @var string
     */
    const TRANSLATION_FORMAT_TITLE = 'orm.label.format.title';

    /**
     * Translation key for the teaser format
     * @var string
     */
    const TRANSLATION_FORMAT_TEASER = 'orm.label.format.teaser';

    /**
     * Translation key for the image format
     * @var string
     */
    const TRANSLATION_FORMAT_IMAGE = 'orm.label.format.image';

    /**
     * Translation key for the date format
     * @var string
     */
    const TRANSLATION_FORMAT_DATE = 'orm.label.format.date';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
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
                $dataFormat = $modelTable->getDataFormat($parameters[1]);
                $this->wizard->setValue(self::FIELD_NAME, $dataFormat->getName());
                $this->wizard->setValue(self::FIELD_FORMAT, $dataFormat->getFormat());
            }
        }

        return $this->getDataFormatView($modelTable);
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
            return $this->getDataFormatView($modelTable);
        }

        $name = $this->wizard->getValue(self::FIELD_NAME);
        $format = $this->wizard->getValue(self::FIELD_FORMAT);

        $dataFormat = new DataFormat($name, $format);

        $modelTable = $this->wizard->getModelTable();

        $modelTable->setDataFormat($dataFormat);

        $this->wizard->setModelTable($modelTable);

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

        foreach ($remove as $dataFormatName) {
            $modelTable->removeDataFormat($dataFormatName);
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
    private function getDataFormatView(ModelTable $modelTable) {
        $request = $this->wizard->getRequest();

        $dataFormatTable = new SimpleDataFormatTable($modelTable, $request->getBasePath() . '/' . self::ACTION_EDIT . '/');
        $dataFormatTable->addDecorator(new DataFormatRemoveActionDecorator($this->wizard));

        return new DataFormatStepView($this->wizard, $dataFormatTable);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $modelTable = $this->wizard->getModelTable();
        $validator = new RequiredValidator();

        $translator = I18n::getInstance()->getTranslator();
        $dataFormats = $modelTable->getDataFormats();

        $translationRemove = $translator->translate(self::TRANSLATION_REMOVE);

        $formats = array();
        foreach ($dataFormats as $dataFormat) {
            $formats[$dataFormat->getName()] = $translationRemove;
        }

        $fieldFactory = FieldFactory::getInstance();

        $predefinedFormats = array(
            '' => '---',
            DataFormatter::FORMAT_TITLE => $translator->translate(self::TRANSLATION_FORMAT_TITLE),
            DataFormatter::FORMAT_TEASER => $translator->translate(self::TRANSLATION_FORMAT_TEASER),
            DataFormatter::FORMAT_IMAGE => $translator->translate(self::TRANSLATION_FORMAT_IMAGE),
            DataFormatter::FORMAT_DATE => $translator->translate(self::TRANSLATION_FORMAT_DATE),
        );

        $predefinedField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_PREDEFINED);
        $predefinedField->setOptions($predefinedFormats);

        $nameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME);
        $nameField->addValidator($validator);

        $formatField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_FORMAT);
        $formatField->addValidator($validator);

        $addButton = $fieldFactory->createSubmitField(self::BUTTON_ADD, self::TRANSLATION_ADD);

        $removeButton = $fieldFactory->createField(FieldFactory::TYPE_SUBMIT, self::BUTTON_REMOVE);
        $removeButton->setIsMultiple(true);
        $removeButton->setOptions($formats);

        $this->wizard->addField($predefinedField);
        $this->wizard->addField($nameField);
        $this->wizard->addField($formatField);
        $this->wizard->addField($addButton);
        $this->wizard->addField($removeButton);
    }

    /**
     * Processes the next action of this step
     * @return string Name of the next step
     */
    public function next() {
        if ($this->wizard->isLimitedToDataFormats()) {
            return FinishStep::NAME;
        }

        return IndexStep::NAME;
    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        return !$this->wizard->isLimitedToDataFormats();
    }

    /**
     * Processes the previous action
     * @return string Name of the previous step
     */
    public function previous() {
        $modelTable = $this->wizard->getModelTable();

        $fields = $modelTable->getFields();
        $field = array_pop($fields);
        $fieldName = $field->getName();
        if ($fieldName != ModelTable::PRIMARY_KEY) {
            $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD, $fieldName);
        }

        return FieldStep::NAME;
    }

}