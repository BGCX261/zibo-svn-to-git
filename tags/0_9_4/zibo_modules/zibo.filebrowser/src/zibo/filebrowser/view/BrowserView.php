<?php

namespace zibo\filebrowser\view;

use zibo\filebrowser\table\BrowserTable;

use zibo\library\filesystem\File;

/**
 * View for the browser
 */
class BrowserView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'filebrowser/browser';

    /**
     * Constructs a new browser view
     * @param zibo\filebrowser\table\BrowserTable $browserTable Table with the contents of the current directory
     * @param string $createDirectoryAction URL to the action to create a new directory
     * @param string $createFileAction URL to the action to create a new file
     * @return null
     */
    public function __construct(BrowserTable $browserTable) {
        parent::__construct(self::TEMPLATE);

        $this->set('browser', $browserTable);

        $this->addJavascript(self::SCRIPT_TABLE);
    }

}