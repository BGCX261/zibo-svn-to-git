<?php

namespace zibo\orm\log\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\i18n\I18n;
use zibo\library\orm\model\LogModel;
use zibo\library\Structure;

/**
 * Form to filter the model logs
 */
class LogFilterForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formLogFilter';

    /**
     * Name of the include module models field
     * @var string
     */
    const FIELD_INCLUDE = 'include';

    /**
     * Name of the data model field
     * @var string
     */
    const FIELD_DATA_MODEL = 'dataModel';

    /**
     * Name of the data id field
     * @var string
     */
    const FIELD_DATA_ID = 'dataId';

    /**
     * Name of the field field
     * @var string
     */
    const FIELD_DATA_FIELD = 'dataField';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_FILTER = 'button.filter';

    /**
     * Constructs a new log filter form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action, array $include = null, $dataModel = null, $dataId = null, $dataField = null) {
        parent::__construct($action, self::NAME, self::TRANSLATION_FILTER);

        $translator = I18n::getInstance()->getTranslator();

        $includeOptions = array(
            LogModel::ACTION_INSERT => LogModel::ACTION_INSERT,
            LogModel::ACTION_UPDATE => LogModel::ACTION_UPDATE,
            LogModel::ACTION_DELETE => LogModel::ACTION_DELETE,
        );

        if ($include == null) {
            $include = $includeOptions;
        }

        $fieldFactory = FieldFactory::getInstance();

        $fieldInclude = $fieldFactory->createField(FieldFactory::TYPE_OPTION, self::FIELD_INCLUDE, $include);
        $fieldInclude->setIsMultiple(true);
        $fieldInclude->setOptions($includeOptions);

        $dataModelField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_DATA_MODEL, $dataModel);

        $dataIdField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_DATA_ID, $dataId);

        $dataFieldField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_DATA_FIELD, $dataField);

        $this->addField($fieldInclude);
        $this->addField($dataModelField);
        $this->addField($dataIdField);
        $this->addField($dataFieldField);
    }

    /**
     * Gets whether to include custom models
     * @return boolean
     */
    public function includeInsert() {
        return $this->includeAction(LogModel::ACTION_INSERT);
    }

    /**
     * Gets whether to include module models
     * @return boolean
     */
    public function includeUpdate() {
        return $this->includeAction(LogModel::ACTION_UPDATE);
    }

    /**
     * Gets whether to include localized models
     * @return boolean
     */
    public function includeDelete() {
        return $this->includeAction(LogModel::ACTION_DELETE);
    }

    /**
     * Gets whether to include models
     * @param string $type
     * @return boolean
     */
    private function includeAction($name) {
        $include = $this->getValue(self::FIELD_INCLUDE);
        if ($include === null) {
            $include = $this->getField(self::FIELD_INCLUDE)->getDefaultValue();
        }

        if (array_key_exists($name, $include)) {
            return true;
        }

        return false;
    }

    /**
     * Gets the array of actions to include
     * @return array
     */
    public function getInclude() {
        return Structure::getKeyArray($this->getValue(self::FIELD_INCLUDE));
    }

    /**
     * Gets the name of the model
     * @return string
     */
    public function getDataModel() {
        return $this->getValue(self::FIELD_DATA_MODEL);
    }

    /**
     * Gets the id of the data
     * @return string
     */
    public function getDataId() {
        return $this->getValue(self::FIELD_DATA_ID);
    }

    /**
     * Gets the name of the modified field
     * @return string
     */
    public function getDataField() {
        return $this->getValue(self::FIELD_DATA_FIELD);
    }

}