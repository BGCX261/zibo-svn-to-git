<?php

namespace joppa\search\view;

use joppa\search\form\SearchForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the search form widget
 */
class SearchFormView extends SmartyView {

    /**
     * Construct this view
     * @param joppa\search\form\SearchForm $form
     * @return null
     */
    public function __construct(SearchForm $form = null) {
        parent::__construct('joppa/search/form');

        $this->set('form', $form);
    }

}