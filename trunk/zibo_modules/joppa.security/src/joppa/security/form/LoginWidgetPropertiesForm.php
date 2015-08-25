<?php

namespace joppa\security\form;

use joppa\security\controller\LoginWidget;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;

/**
 * Form to manage the properties of the login widget
 */
class LoginWidgetPropertiesForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formLoginWidgetProperties';

	/**
	 * Name of the redirect field
	 * @var string
	 */
	const FIELD_REDIRECT = 'redirect';

	/**
	 * Constructs a new properties form for a login widget
	 * @param string $action URL where this form will point to
	 * @param string $redirect Value for the redirect field
	 * @return null
	 */
	public function __construct($action, $redirect) {
		parent::__construct($action, self::NAME);

		$fieldFactory = FieldFactory::getInstance();

		$redirectOptions = LoginWidget::getRedirectOptions();

		$redirectField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_REDIRECT, $redirect);
		$redirectField->setOptions($redirectOptions);

		$this->addField($redirectField);
	}

	/**
	 * Gets the redirect value of the form
	 * @return string
	 */
	public function getRedirect() {
	    return $this->getValue(self::FIELD_REDIRECT);
	}

}