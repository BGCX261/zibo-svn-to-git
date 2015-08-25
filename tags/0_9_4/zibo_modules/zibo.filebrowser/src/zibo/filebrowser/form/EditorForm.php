<?php

namespace zibo\filebrowser\form;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;

/**
 * Form for the editor
 */
class EditorForm extends AbstractForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formEditor';

    /**
     * Name of the content field
     * @var string
     */
    const FIELD_CONTENT = 'content';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'button.save';

    /**
     * Constructs a new editor form
     * @param string $action URL where this form will point to
     * @param zibo\library\filesystem\File $path File to edit or to create a new file, the directory of the new file
     * @return null
     */
    public function __construct($action, File $path = null, $name = null, $content = null) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT, $path);

        $fieldFactory = FieldFactory::getInstance();

        $contentField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_CONTENT, $content);

        $this->addField($contentField);

        $this->setValue(self::FIELD_NAME, $name);
    }

    /**
     * Gets the content of the form
     * @return string
     */
    public function getFileContent() {
        return $this->getValue(self::FIELD_CONTENT);
    }

}