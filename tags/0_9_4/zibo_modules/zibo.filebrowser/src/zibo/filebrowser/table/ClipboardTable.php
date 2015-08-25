<?php

namespace zibo\filebrowser\table;

use zibo\filebrowser\table\decorator\ClipboardFileDecorator;
use zibo\filebrowser\table\decorator\FileOptionDecorator;

use zibo\library\filesystem\File;
use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;

/**
 * Table to show the contents of the clipboard
 */
class ClipboardTable extends ExtendedTable {

    /**
     * Name of the table form
     * @var string
     */
    const FORM_NAME = 'formClipboard';

    /**
     * Constructs a new clipboard table
     * @param string $action URL where the table form will point to
     * @param zibo\library\filesystem\File $root Path of the root for the filebrowser
     * @param array $files The values for the table: array with File objects
     * @return null
     */
    public function __construct($action, File $root, array $files) {
        parent::__construct($files, $action, self::FORM_NAME);

        $this->addDecorator(new FileOptionDecorator());
        $this->addDecorator(new ZebraDecorator(new ClipboardFileDecorator($root)));
    }

}