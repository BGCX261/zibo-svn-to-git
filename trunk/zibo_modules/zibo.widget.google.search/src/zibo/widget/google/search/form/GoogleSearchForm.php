<?php

namespace zibo\widget\google\search\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Google search form
 */
class GoogleSearchForm extends Form {

    /**
     * Action to the google search engine
     * @var string
     */
    const ACTION = 'http://www.google.com/search';

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formGoogleSearch';

    /**
     * Name of the query field
     * @var string
     */
    const FIELD_QUERY = 'q';

    /**
     * Name of the submit field
     * @var string
     */
    const FIELD_SUBMIT = 'submit';

    /**
     * Translation key of the submit field
     * @var string
     */
    const TRANSLATION_SUBMIT = 'widget.google.search.button.search';

    /**
     * Construct a Google search form
     * @return null
     */
    public function __construct() {
        parent::__construct(self::ACTION, self::NAME);

        $this->setMethod(Form::METHOD_GET);
        $this->setAttribute('target', '_blank');

        $factory = FieldFactory::getInstance();

        $this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_QUERY));
        $this->addField($factory->createSubmitField(self::FIELD_SUBMIT, self::TRANSLATION_SUBMIT));
    }

}