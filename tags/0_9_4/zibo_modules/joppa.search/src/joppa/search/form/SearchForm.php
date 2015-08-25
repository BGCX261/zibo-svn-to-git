<?php

namespace joppa\search\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form for the search form widget
 */
class SearchForm extends Form {

    /**
     * Name of this form
     * @var string
     */
    const NAME = 'formSearch';

    /**
     * Name of the search query field
     * @var string
     */
    const FIELD_QUERY = 'query';

    /**
     * Name of the search button
     * @var string
     */
    const FIELD_SEARCH = 'search';

    /**
     * Translation key of the search button
     * @var string
     */
    const TRANSLATION_SEARCH = 'joppa.search.button.search';

    /**
     * Construct this form
     * @param string $action url where this form will point to
     * @param string $query optional search query to preset the form
     * @return null
     */
    public function __construct($action, $query = null) {
        parent::__construct($action, self::NAME);

        $factory = FieldFactory::getInstance();

        $this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_QUERY, $query));
        $this->addField($factory->createSubmitField(self::FIELD_SEARCH, self::TRANSLATION_SEARCH));
    }

    /**
     * Get the query from this form
     * @return string
     */
    public function getQuery() {
        return $this->getValue(self::FIELD_QUERY);
    }

}