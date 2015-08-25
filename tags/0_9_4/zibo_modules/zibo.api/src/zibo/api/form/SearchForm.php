<?php

namespace zibo\api\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form to search through the API
 */
class SearchForm extends Form {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formApiSearch';

    /**
     * Name of the query field
     * @var string
     */
    const FIELD_QUERY = 'query';

    /**
     * Name of the submit button
     * @var string
     */
    const FIELD_SUBMIT = 'submit';

    /**
     * Translation key for the value of the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'api.button.search';

    /**
     * Construct a new API search form
     * @param string $action url where this form will point to
     * @param string $query search query to initialize the form with
     * @return null
     */
    public function __construct($action, $query = null) {
        parent::__construct($action, self::NAME);

        $factory = FieldFactory::getInstance();

        $this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_QUERY, $query));
        $this->addField($factory->createSubmitField(self::FIELD_SUBMIT, self::TRANSLATION_SUBMIT));
    }

    /**
     * Get the search query of the form
     * @return string
     */
    public function getQuery() {
        return $this->getValue(self::FIELD_QUERY);
    }

}