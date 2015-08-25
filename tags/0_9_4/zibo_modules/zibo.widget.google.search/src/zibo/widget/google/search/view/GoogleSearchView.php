<?php

namespace zibo\widget\google\search\view;

use zibo\library\smarty\view\SmartyView;

use zibo\widget\google\search\form\GoogleSearchForm;

/**
 * View for the Google search widget
 */
class GoogleSearchView extends SmartyView {

    /**
     * Construct a new Google search widget view
     * @param zibo\widget\google\search\form\GoogleSearchForm $form
     * @return null
     */
    public function __construct(GoogleSearchForm $form) {
        parent::__construct('google/search');

        $this->set('form', $form);

        $this->addStyle('web/styles/google/search.css');
    }

}