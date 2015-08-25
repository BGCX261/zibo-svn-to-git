<?php

namespace zibo\filebrowser\view;

use zibo\library\html\form\Form;

/**
 * View to create a directory
 */
class CreateView extends BaseFormView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'filebrowser/create';

    /**
     * Constructs a new create view
     * @param zibo\library\html\form\Form $form
     * @param string $path
     * @return null
     */
    public function __construct (Form $form, $path) {
        parent::__construct(self::TEMPLATE, $form, $path);
    }

}