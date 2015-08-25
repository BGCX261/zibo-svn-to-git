<?php

namespace zibo\repository\view;

use zibo\admin\view\BaseView;

/**
 * View for the repository
 */
abstract class AbstractRepositoryView extends BaseView {

    /**
     * Path to the style of this view
     * @var string
     */
    const STYLE = 'web/styles/repository/repository.css';

    /**
     * Constructs a new abstract repository view
     * @param string $template The template for the view
     * @return null
     */
    public function __construct($template) {
        parent::__construct($template);

        $this->addStyle(self::STYLE);
        $this->addJavascript(self::SCRIPT_TABLE);
    }

}