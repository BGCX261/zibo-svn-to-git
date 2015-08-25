<?php

namespace joppa\search\view;

use joppa\search\form\SearchResultPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of a search result widget
 */
class SearchResultPropertiesView extends SmartyView {

    /**
     * Construct this view
     * @param joppa\search\form\SearchResultPropertiesForm $form
     * @return null
     */
    public function __construct(SearchResultPropertiesForm $form) {
		parent::__construct('joppa/search/result.properties');

		$this->set('form', $form);
	}

}