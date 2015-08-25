<?php

namespace zibo\filebrowser\view;

use zibo\library\html\form\Form;

/**
 * Base form view for the file browser application
 */
class BaseFormView extends BaseView {

    /**
     * Constructs a new form view
     * @param string $template Path to the template for this view
     * @param zibo\library\html\form\Form $form Form to display
     * @param string $path Path of the current directory or file
     * @return null
     */
    public function __construct($template, Form $form, $path) {
        parent::__construct($template);

        $this->set('form', $form);
        $this->set('path', $path);
    }

}