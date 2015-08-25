<?php

namespace zibo\orm\scaffold\form;

use zibo\library\html\form\field\AbstractArrayField;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\field\Field;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\query\ModelQuery;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\model\ExtendedModel;
use zibo\library\orm\model\LocalizedModel;
use zibo\library\orm\model\Model;

use zibo\orm\scaffold\form\field\decorator\DataDecorator;
use zibo\orm\scaffold\form\field\decorator\IdDecorator;

use zibo\ZiboException;

/**
 * Scaffolded form for the data of a Model
 */
class ScaffoldForm extends DataForm {

    /**
     * Prefix for the name of this form
     * @var string
     */
    const NAME = 'form';

    /**
     * Data object set to this form
     * @var mixed
     */
    protected $data;

    /**
     * Array with the field names of the data object
     * @var array
     */
    protected $dataFieldNames;

    /**
     * Array with the field labels
     * @var array
     */
    protected $dataFieldLabels;

    /**
     * Flag to see if this object is in construction or not
     * @var boolean
     */
    private $isConstructing;

    /**
     * Constructs a new scaffold form
     * @param string $action URL where this form will point to
     * @param zibo\library\orm\model\Model $model Model of the data on the form
     * @param mixed $data Data object of the model to preset the form
     * @param array $fields Array with the names of the fields which need to be added to the form. If not provided, all the fields of the model will be added
     * @param boolean $skipFields Set to true to skip the provided fields instead of adding them
     * @return null
     */
    public function __construct($action, Model $model, $data = null, array $fields = null, $skipFields = false) {
        if ($data == null) {
            $data = $model->createData();
        }

        $this->isConstructing = true;

        parent::__construct($action, self::NAME . $model->getName(), $data);

        $this->isConstructing = false;

        $this->dataFieldNames = array();
        $this->addFieldsToForm($model, $fields, $skipFields);
    }

    /**
     * Gets the labels for the fields
     * @return array Array with the field name as key and the translation key for the label of the field as value
     */
    public function getFieldLabels() {
        return $this->dataFieldLabels;
    }

    /**
     * Sets the fields of the submitted form to the data object
     * @return null
     */
    protected function setFormValuesToData() {
        foreach ($this->dataFieldNames as $fieldName) {
            $this->data->$fieldName = $this->getValue($fieldName);

            $field = $this->getField($fieldName);
            if ($field instanceof AbstractArrayField && $field->isMultiple()) {
                $this->data->$fieldName = array_keys($this->data->$fieldName);
            }
        }
    }

    /**
     * Adds a field to this form
     * @param zibo\library\html\form\field\Field $field Field to add
     * @return null
     */
    public function addField(Field $field) {
        parent::addField($field);

        if (!$this->isConstructing) {
            $this->dataFieldNames[] = $field->getName();
        }
    }

    /**
     * Adds the fields to the form
     * @param zibo\library\orm\model\Model $model Model of the data
     * @param array $fields Array with the names of the fields which need to be added to the form. If not provided, all the fields of the model will be added
     * @param boolean $skipFields
     *                      @
     * @return null
     */
    protected function addFieldsToForm(Model $model, array $fields = null, $skipFields = false) {
        $meta = $model->getMeta();

        $hiddenFields = $this->getHiddenFieldNames($model);
        $fields = $this->getModelFields($meta, $fields, $skipFields);

        $fieldFactory = FieldFactory::getInstance();

        foreach ($hiddenFields as $fieldName) {
            if (!array_key_exists($fieldName, $fields)) {
                continue;
            }

            $hiddenField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, $fieldName, $this->data->$fieldName);

            $this->addField($hiddenField);

            unset($fields[$fieldName]);
        }

        foreach ($fields as $fieldName => $field) {
            $value = null;
            if (isset($this->data->$fieldName)) {
                $value = $this->data->$fieldName;
            }

            $formField = $this->createFormField($fieldFactory, $meta, $field, $value);

            $this->addField($formField);
        }
    }

    /**
     * Gets the names of the hidden fields for this model
     * @param zibo\library\orm\model\Model $model Model of the data
     * @return array Array with the names of the hidden fields
     */
    protected function getHiddenFieldNames(Model $model) {
        $hiddenFields = array(ModelTable::PRIMARY_KEY);

        if ($model instanceof ExtendedModel) {
            $automaticFields = $model->getAutomaticFields();
            foreach ($automaticFields as $automaticField) {
                $hiddenFields[] = $automaticField;
            }
        }

        return $hiddenFields;
    }

    /**
     * Gets the model fields to use in this form.
     * @param zibo\library\orm\model\meta\ModelMeta $meta Meta of the model
     * @param array $fields Array with the names of the fields which need to be added to the form. If not provided, all the fields of the model will be returned
     * @param boolean $skipFields Set to true to skip the provided fields instead of adding them
     * @return array Array with the name of the field as key and a ModelField object as value
     */
    protected function getModelFields(ModelMeta $meta, array $fieldNames = null, $skipFields = false) {
        if (!$fieldNames) {
            $fields = $meta->getFields();
        } elseif ($skipFields) {
            $fields = $meta->getFields();

            foreach ($fieldNames as $fieldName) {
                if ($fieldName == ModelTable::PRIMARY_KEY) {
                    continue;
                }

                if (array_key_exists($fieldName, $fields)) {
                    unset($fields[$fieldName]);
                }
            }

        } else {
            $fields = array();
            $fields[ModelTable::PRIMARY_KEY] = $meta->getField(ModelTable::PRIMARY_KEY);

            foreach ($fieldNames as $fieldName) {
                $fields[$fieldName] = $meta->getField($fieldName);
            }
        }

        $this->dataFieldLabels = array();
        foreach ($fields as $fieldName => $field) {
            $label = $field->getLabel();
            if ($label) {
                $this->dataFieldLabels[$fieldName] = $label;
            }
        }

        return $fields;
    }

    /**
     * Creates a form field for the provided model field
     * @param zibo\library\html\form\field\FieldFactory $fieldFactory Instance of the field factory
     * @param zibo\library\orm\model\meta\ModelMeta $meta Meta of the model of the model field
     * @param zibo\library\orm\definition\field\ModelField $field Definition of the model field
     * @param mixed $value Value for the form field
     * @return zibo\library\html\form\field\Field
     */
    protected function createFormField(FieldFactory $fieldFactory, ModelMeta $meta, ModelField $field, $value) {
        if ($field instanceof RelationField) {
            return $this->createFormFieldFromRelationField($fieldFactory, $meta, $field, $value);
        } else {
            return $this->createFormFieldFromModelField($fieldFactory, $field, $value);
        }
    }

    /**
     * Creates a form field for the provided model property field
     * @param zibo\library\html\form\field\FieldFactory $fieldFactory Instance of the field factory
     * @param zibo\library\orm\definition\field\ModelField $field Definition of the model field
     * @param mixed $value Value for the form field
     * @return zibo\library\html\form\field\Field
     */
    protected function createFormFieldFromModelField(FieldFactory $fieldFactory, ModelField $field, $value) {
        $fieldName = $field->getName();
        try {
            return $fieldFactory->createField($field->getType(), $fieldName, $value);
        } catch (ZiboException $e) {
            return $fieldFactory->createField(FieldFactory::TYPE_STRING, $fieldName, $value);
        }
    }

    /**
     * Creates a form field for the provided model relation field
     * @param zibo\library\html\form\field\FieldFactory $fieldFactory Instance of the field factory
     * @param zibo\library\orm\model\ModelMeta $meta Meta of the model of the relation field
     * @param zibo\library\orm\definition\field\RelationField $field Definition of the relation field
     * @param mixed $value Value for the form field
     * @return zibo\library\html\form\field\Field
     */
    protected function createFormFieldFromRelationField(FieldFactory $fieldFactory, ModelMeta $meta, RelationField $field, $value) {
        $relationModel = $meta->getRelationModel($field->getName());

        $list = $relationModel->getDataList();

        if (!is_array($value) && is_object($value)) {
            $value = $value->id;
        }

        $formField = $fieldFactory->createField(FieldFactory::TYPE_LIST, $field->getName(), $value);
        $formField->setOptions($list);

        if ($field instanceof HasManyField) {
            $formField->setIsMultiple(true);
            $formField->setShowSelectedOptionsFirst(true);
        } else {
            $formField->addEmpty('---', '');
        }

        return $formField;
    }

}