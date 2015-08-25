<?php

namespace zibo\orm\builder\wizard;

use zibo\core\view\JsonView;
use zibo\core\Zibo;

use zibo\library\database\DatabaseManager;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\ModelManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RegexValidator;
use zibo\library\wizard\step\AbstractWizardStep;

use zibo\orm\builder\view\wizard\FieldStepView;

/**
 * Step to ask the general info of a model
 */
class FieldStep extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'field';

    /**
     * Action to retrieve the fields of a model over JSON
     * @var string
     */
    const ACTION_FIELDS = 'fields';

    /**
     * Property field type
     * @var string
     */
    const TYPE_PROPERTY = 'property';

    /**
     * Relation field type
     * @var string
     */
    const TYPE_RELATION = 'relation';

    /**
     * Value for automatic link model option
     * @var string
     */
    const AUTOMATIC = 'automatic';

    /**
     * Translation key for the property field type
     * @var string
     */
    const TRANSLATION_PROPERTY = 'orm.label.field.property';

    /**
     * Translation key for the relation field type
     * @var string
     */
    const TRANSLATION_RELATION = 'orm.label.field.relation';

    /**
     * Translation key for the automatic link model option
     * @var string
     */
    const TRANSLATION_AUTOMATIC = 'orm.label.automatic';

    /**
     * Name of the field name field
     * @var string
     */
    const FIELD_FIELD_NAME = 'fieldName';

    /**
     * Name of the label field
     * @var string
     */
    const FIELD_FIELD_LABEL = 'fieldLabel';

    /**
     * Name of the is localized field
     * @var string
     */
    const FIELD_IS_LOCALIZED = 'isLocalized';

    /**
     * Name of the field type field
     * @var string
     */
    const FIELD_FIELD_TYPE = 'fieldType';

    /**
     * Name of the is property type field
     * @var string
     */
    const FIELD_PROPERTY_TYPE = 'propertyType';

    /**
     * Name of the is property default value field
     * @var string
     */
    const FIELD_PROPERTY_DEFAULT = 'propertyDefault';

    /**
     * Name of the is relation type field
     * @var string
     */
    const FIELD_RELATION_TYPE = 'relationType';

    /**
     * Name of the is relation model field
     * @var string
     */
    const FIELD_RELATION_MODEL = 'relationModel';

    /**
     * Name of the is relation dependant field
     * @var string
     */
    const FIELD_RELATION_IS_DEPENDANT = 'relationIsDependant';

    /**
     * Name of the is relation link model field
     * @var string
     */
    const FIELD_RELATION_LINK_MODEL = 'relationLinkModel';

    /**
     * Name of the is relation foreign key field
     * @var string
     */
    const FIELD_RELATION_FOREIGN_KEY = 'relationForeignKey';

    /**
     * Name of the is relation order field
     * @var string
     */
    const FIELD_RELATION_ORDER = 'relationOrder';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        $request = $this->wizard->getRequest();

        $parameters = $request->getParameters();
        if ($parameters) {
            $numParameters = count($parameters);

            if ($numParameters == 2 && $parameters[0] == self::ACTION_FIELDS) {
                $modelName = $parameters[1];

                $fields = $this->getForeignKeyFields($modelName);

                return new JsonView(array('fields' => $fields));
            }
        }

        return new FieldStepView($this->wizard, $request->getBasePath() . '/' . self::ACTION_FIELDS . '/');
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $modelTable = $this->wizard->getVariable(BuilderWizard::VARIABLE_MODEL_TABLE);
        $fieldName = $this->wizard->getVariable(BuilderWizard::VARIABLE_FIELD);

        $field = null;
        $fields = $modelTable->getFields();
        foreach ($fields as $modelFieldName => $modelField) {
            if ($modelFieldName == ModelTable::PRIMARY_KEY) {
                continue;
            }

            if ($fieldName === null) {
                $field = $modelField;
                $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD, $modelFieldName);
                break;
            }

            if ($fieldName == $modelFieldName) {
                $field = $modelField;
                break;
            }
        }

        $isFieldNameRequired = false;
        if (count($fields) <= 1) {
            $isFieldNameRequired = true;
        }

        $fieldName = null;
        $fieldLabel = null;
        $isLocalized = false;
        $fieldType = self::TYPE_PROPERTY;
        $propertyType = null;
        $propertyDefault = null;
        $relationType = null;
        $relationModel = null;
        $relationLinkModel = null;
        $relationForeignKey = null;
        $relationIsDependant = false;
        $relationOrder = null;
        if ($field) {
            $fieldName = $field->getName();
            $fieldLabel = $field->getLabel();
            $isLocalized = $field->isLocalized();

            if ($field instanceof PropertyField) {
                $propertyType = $field->getType();
                $propertyDefault = $field->getDefaultValue();
            } else {
                $fieldType = self::TYPE_RELATION;
                $relationModel = $field->getRelationModelName();
                $relationIsDependant = $field->isDependant();
                $relationLinkModel = $field->getLinkModelName();
                $relationForeignKey = $field->getForeignKeyName();

                if ($field instanceof BelongsToField) {
                    $relationType = ModelTable::BELONGS_TO;
                } elseif ($field instanceof HasOneField) {
                    $relationType = ModelTable::HAS_ONE;
                } elseif ($field instanceof HasManyField) {
                    $relationType = ModelTable::HAS_MANY;
                    $relationOrder = $field->getRelationOrder();
                }
            }
        }

        $translator = I18n::getInstance()->getTranslator();
        $fieldFactory = FieldFactory::getInstance();

        $fieldTypes = array(
            self::TYPE_PROPERTY => $translator->translate(self::TRANSLATION_PROPERTY),
            self::TYPE_RELATION => $translator->translate(self::TRANSLATION_RELATION),
        );

        $models = $this->getRelationModels($modelTable->getName());
        if (!$relationModel) {
            $model = each($models);
            $relationModel = $model['key'];
        }

        $fieldNameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_FIELD_NAME, $fieldName);
        $fieldNameField->addValidator(new RegexValidator(array(RegexValidator::OPTION_REGEX => ModelField::REGEX_NAME, RegexValidator::OPTION_REQUIRED => $isFieldNameRequired)));

        $fieldLabelField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_FIELD_LABEL, $fieldLabel);

        $isLocalizedField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_IS_LOCALIZED, $isLocalized);

        $fieldTypeField = $fieldFactory->createField(FieldFactory::TYPE_OPTION, self::FIELD_FIELD_TYPE, $fieldType);
        $fieldTypeField->setOptions($fieldTypes);

        $propertyTypeField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_PROPERTY_TYPE, $propertyType);
        $propertyTypeField->setOptions($this->getPropertyTypes());

        $propertyDefaultField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_PROPERTY_DEFAULT, $propertyDefault);

        $relationTypeField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_RELATION_TYPE, $relationType);
        $relationTypeField->setOptions($this->getRelationTypes());

        $relationModelField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_RELATION_MODEL, $relationModel);
        $relationModelField->setOptions($models);

        $relationIsDependantField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_RELATION_IS_DEPENDANT, $relationIsDependant);

        $models = array(self::AUTOMATIC => $translator->translate(self::TRANSLATION_AUTOMATIC)) + $models;

        $relationLinkModelField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_RELATION_LINK_MODEL, $relationLinkModel);
        $relationLinkModelField->setOptions($models);

        $relationForeignKeyField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_RELATION_FOREIGN_KEY, $relationForeignKey);
        $relationForeignKeyField->setOptions($this->getForeignKeyFields($relationModel, $translator));

        $relationOrderField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_RELATION_ORDER, $relationOrder);

        $this->wizard->addField($fieldNameField);
        $this->wizard->addField($fieldLabelField);
        $this->wizard->addField($isLocalizedField);
        $this->wizard->addField($fieldTypeField);
        $this->wizard->addField($propertyTypeField);
        $this->wizard->addField($propertyDefaultField);
        $this->wizard->addField($relationTypeField);
        $this->wizard->addField($relationModelField);
        $this->wizard->addField($relationIsDependantField);
        $this->wizard->addField($relationLinkModelField);
        $this->wizard->addField($relationForeignKeyField);
        $this->wizard->addField($relationOrderField);
    }

    /**
     * Processes the next action of this step
     * return string Name of the next step
     */
    public function next() {
        try {
            $this->wizard->validate();
        } catch (ValidationException $exception) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
            return null;
        }

        $modelTable = $this->wizard->getVariable(BuilderWizard::VARIABLE_MODEL_TABLE);

        $fieldName = $this->wizard->getValue(self::FIELD_FIELD_NAME);
        if (!$fieldName) {
            $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD);

            if ($this->wizard->getLimitField()) {
                return FinishStep::NAME;
            }
            return DataFormatStep::NAME;
        }

        $field = null;
        $newField = null;
        if ($modelTable->hasField($fieldName)) {
            $field = $modelTable->getField($fieldName);
        }

        $fieldType = $this->wizard->getValue(self::FIELD_FIELD_TYPE);
        if ($fieldType == self::TYPE_PROPERTY) {
            $propertyType = $this->wizard->getValue(self::FIELD_PROPERTY_TYPE);
            $propertyDefault = $this->wizard->getValue(self::FIELD_PROPERTY_DEFAULT);

            $newField = new PropertyField($fieldName, $propertyType, $propertyDefault);

            if ($field) {
                $this->copyField($field, $newField);
            }
        } else {
            $relationType = $this->wizard->getValue(self::FIELD_RELATION_TYPE);
            $relationModel = $this->wizard->getValue(self::FIELD_RELATION_MODEL);
            switch ($relationType) {
                case ModelTable::BELONGS_TO:
                    $newField = new BelongsToField($fieldName, $relationModel);
                    break;
                case ModelTable::HAS_ONE:
                    $newField = new HasOneField($fieldName, $relationModel);
                    break;
                case ModelTable::HAS_MANY:
                    $newField = new HasManyField($fieldName, $relationModel);

                    $relationOrder = $this->wizard->getValue(self::FIELD_RELATION_ORDER);
                    if ($relationOrder) {
                        $newField->setRelationOrder($relationOrder);
                    }
                    break;
            }

            if ($field) {
                $this->copyField($field, $newField);
            }

            $relationLinkModel = $this->wizard->getValue(self::FIELD_RELATION_LINK_MODEL);
            if ($relationLinkModel && $relationLinkModel != self::AUTOMATIC) {
                $newField->setLinkModelName($relationLinkModel);
            }

            $relationForeignKey = $this->wizard->getValue(self::FIELD_RELATION_FOREIGN_KEY);
            if ($relationForeignKey && $relationForeignKey != self::AUTOMATIC) {
                $newField->setForeignKeyName($relationForeignKey);
            }

            $relationIsDependant = $this->wizard->getValue(self::FIELD_RELATION_IS_DEPENDANT);
            if (!$relationIsDependant) {
                $relationIsDependant = false;
            }
            $newField->setIsDependant($relationIsDependant);
        }

        $fieldLabel = $this->wizard->getValue(self::FIELD_FIELD_LABEL);
        if ($fieldLabel) {
            $newField->setLabel($fieldLabel);
        }

        $isLocalized = $this->wizard->getValue(self::FIELD_IS_LOCALIZED);
        if ($isLocalized === null) {
            $isLocalized = false;
        }
        $newField->setIsLocalized($isLocalized);

        try {
            $this->setFieldToModelTable($modelTable, $newField);
        } catch (Exception $exception) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
            return null;
        }

        $limitField = $this->wizard->getLimitField();
        if ($limitField && $limitField !== true) {
            return FinishStep::NAME;
        }

        $this->setNextField($fieldName, $modelTable->getFields());

        return self::NAME;
    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        $limitField = $this->wizard->getLimitField();
        if (!$limitField) {
            return true;
        } elseif ($limitField !== true) {
            return false;
        }

        $modelTable = $this->wizard->getModelTable();
        $currentFieldName = $this->wizard->getVariable(BuilderWizard::VARIABLE_FIELD);

        $fields = $modelTable->getFields();
        foreach ($fields as $fieldName => $field) {
            if ($fieldName == ModelTable::PRIMARY_KEY) {
                continue;
            }

            if ($fieldName == $currentFieldName) {
                return false;
            }

            break;
        }

        return true;
    }

    /**
     * Processes the previous action of this step
     * @return strin Name of the previous step
     */
    public function previous() {
        $modelTable = $this->wizard->getModelTable();
        $currentFieldName = $this->wizard->getVariable(BuilderWizard::VARIABLE_FIELD);

        $previousFieldName = null;
        $fields = $modelTable->getFields();
        foreach ($fields as $fieldName => $field) {
            if ($fieldName == ModelTable::PRIMARY_KEY) {
                continue;
            }

            if ($fieldName == $currentFieldName) {
                if ($previousFieldName) {
                    $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD, $previousFieldName);
                    return self::NAME;
                } else {
                    $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD);
                    return ModelGeneralStep::NAME;
                }
            }

            $previousFieldName = $fieldName;
        }

        if ($previousFieldName) {
            $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD, $previousFieldName);
            return self::NAME;
        } else {
            $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD);
            return ModelGeneralStep::NAME;
        }
    }

    /**
     * Sets the submitted field to the model table
     * @param zibo\library\orm\definition\ModelTable $modelTable
     * @param zibo\library\orm\definition\field\ModelField $field
     * @return null
     */
    private function setFieldToModelTable(ModelTable $modelTable, ModelField $field) {
        $modelTable->setField($field);

        $fieldName = $field->getName();
        $currentFieldName = $this->wizard->getVariable(BuilderWizard::VARIABLE_FIELD);

        if (!$currentFieldName || $fieldName == $currentFieldName) {
            $this->wizard->setVariable(BuilderWizard::VARIABLE_MODEL_TABLE, $modelTable);
            return;
        }

        $order = array();

        $fields = $modelTable->getFields();
        foreach ($fields as $modelFieldName => $modelField) {
            if ($modelFieldName == $fieldName) {
                continue;
            }

            if ($modelFieldName == $currentFieldName) {
                $modelFieldName = $fieldName;
            }

            $order[] = $modelFieldName;
        }

        $modelTable->orderFields($order);
        $modelTable->removeField($currentFieldName);

        $this->wizard->setVariable(BuilderWizard::VARIABLE_MODEL_TABLE, $modelTable);
    }

    /**
     * Sets the next field in the model table as current wizard field
     * @param string $currentFieldName Name of the current field
     * @param array $fields Array with the fields of the model table
     * @return null
     */
    private function setNextField($currentFieldName, array $fields) {
        $submittedFieldName = $currentFieldName;
        $currentFieldName = false;
        $previousFieldName = null;

        foreach ($fields as $fieldName => $field) {
            if ($fieldName == ModelTable::PRIMARY_KEY) {
                continue;
            }

            if ($previousFieldName == $submittedFieldName) {
                $currentFieldName = $fieldName;
                break;
            }

            $previousFieldName = $fieldName;
        }

        $this->wizard->setVariable(BuilderWizard::VARIABLE_FIELD, $currentFieldName);
    }

    /**
     * Gets all the field types as defined in the database definer
     * @return array Array with the property type as key and as value
     */
    private function getPropertyTypes() {
        $connection = DatabaseManager::getInstance()->getConnection();
        $definer = $connection->getDefiner();
        $fieldTypes = $definer->getFieldTypes();

        $propertyTypes = array();
        foreach ($fieldTypes as $fieldType => $databaseType) {
            $propertyTypes[$fieldType] = $fieldType;
        }

        return $propertyTypes;
    }

    /**
     * Gets a list of all the possible relation types
     * @return array Array with the relation type constant as key and the name as value
     */
    private function getRelationTypes() {
        return array(
            ModelTable::BELONGS_TO => 'belongsTo',
            ModelTable::HAS_MANY => 'hasMany',
            ModelTable::HAS_ONE => 'hasOne',
        );
    }

    /**
     * Gets a list of all the possible relation models
     * @param string $modelName Name of the model of the wizard
     * @return array Array with the model names as key and as value
     */
    private function getRelationModels($modelName) {
        $models = ModelManager::getInstance()->getModels(true);

        $models = array_keys($models);
        $models[] = $modelName;

        $relationModels = array();
        foreach ($models as $modelName) {
            $relationModels[$modelName] = $modelName;
        }

        ksort($relationModels);

        return $relationModels;
    }

    /**
     * Gets a list of the possible foreign key fields of the provided link model
     * @param string $linkModelName Name of the link model
     * @return array Array with the name of the field as key and as value
     */
    private function getForeignKeyFields($linkModelName, Translator $translator = null) {
        if (!$translator) {
            $translator = I18n::getInstance()->getTranslator();
        }

        $modelTable = $this->wizard->getVariable(BuilderWizard::VARIABLE_MODEL_TABLE);
        $modelName = $modelTable->getName();

        $foreignKeys = array(
            self::AUTOMATIC => $translator->translate(self::TRANSLATION_AUTOMATIC),
        );

        if (!$linkModelName || $linkModelName == self::AUTOMATIC) {
            return $foreignKeys;
        }

        $modelManager = ModelManager::getInstance();
        if (!$modelManager->hasModel($linkModelName)) {
            return $foreignKeys;
        }

        $model = $modelManager->getModel($linkModelName);

        $fields = $model->getMeta()->getFields();
        foreach ($fields as $fieldName => $field) {
            if (!($field instanceof BelongsToField)) {
                continue;
            }

            if ($field->getRelationModelName() == $modelName) {
                $foreignKeys[$fieldName] = $fieldName;
            }
        }

        return $foreignKeys;
    }

    /**
     * Copy the properties of one field to another
     * @param zibo\library\orm\definition\field\ModelField $source
     * @param zibo\library\orm\definition\field\ModelField $destination
     * @return null
     */
    private function copyField(ModelField $source, ModelField $destination) {
        $destination->setLabel($source->getLabel());
        $destination->setIsLocalized($source->isLocalized());

        $validators = $source->getValidators();
        foreach ($validators as $validator) {
            $destination->addValidator($validator);
        }
    }

}