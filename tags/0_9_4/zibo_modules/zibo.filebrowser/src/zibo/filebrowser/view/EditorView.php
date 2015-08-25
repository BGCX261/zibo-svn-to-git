<?php

namespace zibo\filebrowser\view;

use zibo\filebrowser\form\EditorForm;

use zibo\library\filesystem\File;

/**
 * View for the editor
 */
class EditorView extends BaseFormView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'filebrowser/editor';

    /**
     * Constructs a new editor view
     * @param zibo\filebrowser\form\EditorForm $form
     * @param string $path
     * @return null
     */
    public function __construct(EditorForm $form, $path) {
        parent::__construct(self::TEMPLATE, $form, $path);
    }

}