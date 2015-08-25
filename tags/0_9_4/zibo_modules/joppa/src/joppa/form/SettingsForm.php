<?php

namespace joppa\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;

/**
 * Form to manage the properties of a site
 */
class SettingsForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
	const NAME = 'formJoppaSettings';

	/**
	 * Default publish value
	 * @var string
	 */
	const FIELD_IS_PUBLISHED = 'isPublished';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Construct this form
     * @param string $action url where this form will point to
     * @param boolean $isPublished
     * @return null
	 */
	public function __construct($action, $isPublished) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$factory = FieldFactory::getInstance();

		$isPublishedField = $factory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_IS_PUBLISHED, $isPublished);

		$this->addField($isPublishedField);
	}

	/**
	 * Gets the submitted isPublished flag
	 * @return boolean
	 */
	public function isPublished() {
		return $this->getValue(self::FIELD_IS_PUBLISHED);
	}

}