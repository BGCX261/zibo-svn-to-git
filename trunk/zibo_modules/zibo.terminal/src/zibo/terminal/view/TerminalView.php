<?php

namespace zibo\terminal\view;

use zibo\admin\view\BaseView;

use zibo\terminal\form\TerminalForm;

/**
 * View for the terminal
 */
class TerminalView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'terminal/index';

    /**
     * Path to the javascript of this view
     * @var string
     */
    const SCRIPT = 'web/scripts/terminal.js';

    /**
     * Path to the style of this view
     * @var string
     */
    const STYLE = 'web/styles/terminal.css';

    /**
     * Constructs a new terminal view
     * @param zibo\terminal\form\TerminalForm $form The form of the terminal
     * @param string $path The current path
     * @return null
     */
    public function __construct(TerminalForm $form, $path) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);
        $this->set('path', $path);

        $this->addStyle(self::STYLE);
        $this->addJavascript(self::SCRIPT);
        $this->addInlineJavascript('zibo.terminal.init();');
    }

}