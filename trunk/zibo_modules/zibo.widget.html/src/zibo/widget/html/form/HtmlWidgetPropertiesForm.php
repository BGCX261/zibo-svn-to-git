<?php

namespace zibo\widget\html\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;

/**
 * Form to manage the properties of the HTML widget
 */
class HtmlWidgetPropertiesForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
	const NAME = 'formHtmlWidgetProperties';

	/**
	 * Name of the locale field
	 * @var string
	 */
	const FIELD_LOCALE = 'locale';

	/**
	 * Name of the content field
	 * @var string
	 */
	const FIELD_CONTENT = 'content';

	/**
	 * Translation key for the submit button
	 * @var string
	 */
	const TRANSLATION_SUBMIT = 'button.save';

	/**
	 * Constructs a new HTML widget form
	 * @param string $action URL where this form should point to
	 * @param string $locale Locale code
	 * @param string $content Content
	 * @return null
	 */
	public function __construct($action, $locale, $content) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT);

		$fieldFactory = FieldFactory::getInstance();

		$localeField = $fieldFactory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_LOCALE, $locale);
		$contentField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_CONTENT, $content);

		$this->addField($localeField);
		$this->addField($contentField);
	}

	/**
	 * Gets the locale value
	 * @return string
	 */
	public function getLocale() {
	    return $this->getValue(self::FIELD_LOCALE);
	}

	/**
	 * Gets the content value
	 * @return string
	 */
	public function getContent() {
	    return $this->getValue(self::FIELD_CONTENT);
	}

}