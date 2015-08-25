<?php

namespace zibo\repository\form;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to upload a module to the repository
 */
class ModuleUploadForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
    const NAME = 'formRepositoryModuleUpload';

    /**
     * Name of the module field
     * @var string
     */
    const FIELD_MODULE = 'module';

    /**
     * Translation key for the submit button
     * @var unknown_type
     */
    const TRANSLATION_SUBMIT = 'repository.button.add';

    /**
     * Constructs a new module upload form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT);

        $factory = FieldFactory::getInstance();

        $this->addField($factory->createField(FieldFactory::TYPE_FILE, self::FIELD_MODULE));

        $this->addValidator(self::FIELD_MODULE, new RequiredValidator());
    }

    /**
     * Gets the file of the uploaded module
     * @return zibo\library\filesystem\File
     */
    public function getModule() {
        return new File($this->getValue(self::FIELD_MODULE));
    }

}