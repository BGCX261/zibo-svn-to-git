<?php

namespace zibo\widget\google\search\controller;

use zibo\library\widget\controller\AbstractWidget;

use zibo\widget\google\search\form\GoogleSearchForm;
use zibo\widget\google\search\view\GoogleSearchView;

/**
 * Controller of the Google search widget
 */
class GoogleSearchWidget extends AbstractWidget {

    /**
     * Icon of this widget
     * @var string
     */
    const ICON = 'web/images/google/icon.png';

    /**
     * Translation key for the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'widget.google.search.name';

    /**
     * Construct a new Google search widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Action to set the Google search form to the response
     * @return null
     */
    public function indexAction() {
        $form = new GoogleSearchForm();
        $view = new GoogleSearchView($form);
        $this->response->setView($view);
    }

}