<?php

namespace zibo\filebrowser\view;

use zibo\admin\view\BaseView as AdminBaseView;

/**
 * Base view for the file browser
 */
class BaseView extends AdminBaseView {

    /**
     * Path to the JS of this view
     * @var string
     */
    const SCRIPT_BROWSER = 'web/scripts/filebrowser/filebrowser.js';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_BROWSER = 'web/styles/filebrowser/filebrowser.css';

    /**
     * Constructs a new base view for the file browser
     * @param string $template Path to the template of this view
     * @return null
     */
    public function __construct($template) {
        parent::__construct($template);

        $this->addStyle(self::STYLE_BROWSER);
    }

}