<?php

namespace zibo\filebrowser\view;

use zibo\filebrowser\table\ClipboardTable;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the clipboard of the browser
 */
class ClipboardView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'filebrowser/clipboard';

    /**
     * Constructs a new browser sidebar view
     * @param zibo\filebrowser\table\ClipboardTable $clipboardTable Table with the clipboard contents
     * @return null
     */
    public function __construct(ClipboardTable $clipboardTable = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('clipboard', $clipboardTable);

        $this->addJavascript(BaseView::SCRIPT_TABLE);
    }

}