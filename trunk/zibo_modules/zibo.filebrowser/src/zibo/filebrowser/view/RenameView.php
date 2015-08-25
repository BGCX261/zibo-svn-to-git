<?php

namespace zibo\filebrowser\view;

use zibo\library\html\form\Form;

/**
 * View to rename a file or directory
 */
class RenameView extends BaseFormView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'filebrowser/rename';

    /**
     * Constructs a new rename view
     * @param zibo\library\html\form\Form $form
     * @param string $path
     * @return null
     */
    public function __construct(Form $form, $path) {
        parent::__construct(self::TEMPLATE, $form, $path);
    }

}