<?php

namespace zibo\filebrowser\form;

use zibo\i18n\Translator;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to upload a file
 */
class UploadForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
    const NAME = 'formUpload';

    /**
     * Name of the file field
     * @var string
     */
    const FIELD_FILE = 'file';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'filebrowser.button.upload';

    /**
     * Constructs a new upload form
     * @param string $action URL where this form will point to
     * @param zibo\library\filesystem\File $uploadPath Path to upload to
     * @return null
     */
    public function __construct($action, File $uploadPath) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT);

        $fieldFactory = FieldFactory::getInstance();

        $fileField = $fieldFactory->createField(FieldFactory::TYPE_FILE, self::FIELD_FILE);
        $fileField->addValidator(new RequiredValidator());
        $fileField->setIsMultiple(true);
        $fileField->setUploadPath($uploadPath);
        $fileField->setWillOverwrite(true);

        $this->addField($fileField);
    }

    /**
     * Gets the names of the uploaded files
     * @return array
     */
    public function getFiles() {
        $values = $this->getValue(self::FIELD_FILE);

        if (!is_array($values)) {
            $values = array($values);
        }

        return $values;
    }

}