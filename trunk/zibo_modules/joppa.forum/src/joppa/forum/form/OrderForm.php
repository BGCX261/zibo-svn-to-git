<?php

namespace joppa\forum\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;

/**
 * Form to order the forum objects
 */
class OrderForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formForumOrder';

    /**
     * Name of the order field
     * @var string
     */
    const FIELD_ORDER = 'order';

    /**
     * Translation key for the submit button
     * @var string
     */
    const TRANSLATION_SAVE = 'joppa.forum.button.order.save';

    /**
     * Constructs a new forum category order form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

        $fieldFactory = FieldFactory::getInstance();

        $fieldOrder = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ORDER);

        $this->addField($fieldOrder);
    }

    /**
     * Gets the value of the order field
     * @return array Array with the object ids
     */
    public function getOrder() {
        $order = $this->getValue(self::FIELD_ORDER);
        return explode(' ', $order);
    }

}