<?php

namespace zibo\filebrowser\form;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to with a hidden path field and a string name field
 */
abstract class AbstractForm extends SubmitCancelForm {

    /**
     * Name of the path field
     * @var string
     */
    const FIELD_PATH = 'path';

    /**
     * Name of the name field
     * @var string
     */
    const FIELD_NAME = 'name';

    /**
     * Constructs a new form
     * @param string $action URL where this form will point to
     * @param string $name Name of the form
     * @param string $translationSubmit Translation key for the submit button
     * @param zibo\library\filesystem\File $path Path of the file or directory
     * @return null
     */
    public function __construct($action, $name, $translationSubmit, File $path = null) {
        parent::__construct($action, $name, $translationSubmit);

        $pathValue = '.';
        if ($path != null) {
            $pathValue = $path->getPath();
        }

        $fieldFactory = FieldFactory::getInstance();
        $requiredValidator = new RequiredValidator();

        $pathField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_PATH, $pathValue);
        $pathField->addValidator($requiredValidator);

        $nameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME);
        $nameField->addValidator($requiredValidator);

        $this->addField($pathField);
        $this->addField($nameField);
    }

    /**
     * Gets the path of the form
     * @return string
     */
    public function getFilePath() {
        return $this->getValue(self::FIELD_PATH);
    }

    /**
     * Gets the name of the form
     * @return string
     */
    public function getFileName() {
        return $this->getValue(self::FIELD_NAME);
    }

}