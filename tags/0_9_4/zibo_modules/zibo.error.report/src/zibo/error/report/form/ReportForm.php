<?php

namespace zibo\error\report\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form to ask a users comment on an occured error
 */
class ReportForm extends Form {

    /**
     * Name of this form
     * @var string
     */
    const NAME = 'formReportError';

    /**
     * Name of the comment field
     * @var string
     */
    const FIELD_COMMENT = 'comment';

    /**
     * Name of the submit button
     * @var string
     */
    const FIELD_SUBMIT = 'submit';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'error.report.button.submit';

    /**
     * Construct a new form
     * @param string $action action where this form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME);

        $factory = FieldFactory::getInstance();

        $this->addField($factory->createField(FieldFactory::TYPE_TEXT, self::FIELD_COMMENT));
        $this->addField($factory->createSubmitField(self::FIELD_SUBMIT, self::TRANSLATION_SUBMIT));
    }

    /**
     * Get the comment of this form
     * @return string
     */
    public function getComment() {
        return $this->getValue(self::FIELD_COMMENT);
    }

}