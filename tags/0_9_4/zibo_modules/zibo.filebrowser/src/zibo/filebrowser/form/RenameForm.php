<?php

namespace zibo\filebrowser\form;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to rename a directory or file
 */
class RenameForm extends AbstractForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formRename';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'filebrowser.button.rename';

    /**
     * Constructs a new form
     * @param string $action URL where this form will point to
     * @param zibo\library\filesystem\File $path
     * @return null
     */
    public function __construct($action, File $path) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT, $path);

        $this->setValue(self::FIELD_NAME, $path->getName());
    }

}