<?php

namespace joppa\search\view;

use joppa\search\form\SearchFormPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View of the properties of a search form widget
 */
class SearchFormPropertiesView extends SmartyView {

    /**
     * Construct this view
     * @param joppa\search\form\SearchFormPropertiesForm $form
     * @return null
     */
    public function __construct(SearchFormPropertiesForm $form) {
		parent::__construct('joppa/search/form.properties');

		$this->set('form', $form);
	}

}