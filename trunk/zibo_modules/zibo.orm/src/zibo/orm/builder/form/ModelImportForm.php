<?php

namespace zibo\orm\builder\form;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to import a model definition file
 */
class ModelImportForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formModelImport';

    /**
     * Name of the file field
     * @var string
     */
    const FIELD_FILE = 'file';

    /**
     * Constructs a new model import form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME);

        $fieldFactory = FieldFactory::getInstance();

        $fieldFile = $fieldFactory->createField(FieldFactory::TYPE_FILE, self::FIELD_FILE);
        $fieldFile->addValidator(new RequiredValidator());

        $this->addField($fieldFile);
    }

    /**
     * Gets whether to include custom models
     * @return boolean
     */
    public function getFile() {
        return new File($this->getValue(self::FIELD_FILE));
    }

    /**
     * Clears the file value
     * @return null
     */
    public function clearFile() {
        $this->setValue(self::FIELD_FILE, null);
    }

}