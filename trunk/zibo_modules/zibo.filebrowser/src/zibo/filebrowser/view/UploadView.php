<?php

namespace zibo\filebrowser\view;

use zibo\filebrowser\form\UploadForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View to upload a file
 */
class UploadView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'filebrowser/upload';

    /**
     * Constructs a new upload view
     * @param zibo\library\html\form\Form $form
     * @return null
     */
    public function __construct(UploadForm $form) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);

        $fileField = $form->getField(UploadForm::FIELD_FILE);
        $fileField->setAttribute('onChange', 'updateUploadForm(this)');

        $this->addJavascript(BaseView::SCRIPT_BROWSER);
    }

}