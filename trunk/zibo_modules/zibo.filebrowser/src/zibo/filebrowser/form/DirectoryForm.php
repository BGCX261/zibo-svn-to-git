<?php

namespace zibo\filebrowser\form;

use zibo\library\filesystem\File;

/**
 * Form to create a directory
 */
class DirectoryForm extends AbstractForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formDirectory';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'filebrowser.button.create';

    /**
     * Constructs a new form
     * @param string $action URL where this form will point to
     * @param zibo\library\filesystem\File $path Path for the new directory
     * @return null
     */
    public function __construct($action, File $path = null) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT, $path);
    }

}