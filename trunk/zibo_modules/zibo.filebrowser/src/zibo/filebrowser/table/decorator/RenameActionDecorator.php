<?php

namespace zibo\filebrowser\table\decorator;

use zibo\library\filesystem\File;

/**
 * Decorator to create a rename action of a File
 */
class RenameActionDecorator extends FileActionDecorator {

    /**
     * Translation key for the rename button
     * @var string
     */
    const TRANSLATION_RENAME = 'filebrowser.button.rename';

    /**
     * Constructs a new rename action decorator
     * @param string $action The URL to the rename action
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::TRANSLATION_RENAME);
    }

}