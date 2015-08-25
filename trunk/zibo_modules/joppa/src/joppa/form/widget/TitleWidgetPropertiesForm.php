<?php

namespace joppa\form\widget;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;

/**
 * Form to manage the properties of the title widget
 */
class TitleWidgetPropertiesForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formTitleWidgetProperties';

	/**
	 * Name of the level field
	 * @var string
	 */
	const FIELD_LEVEL = 'level';

	/**
	 * Name of the class field
	 * @var string
	 */
	const FIELD_STYLE_CLASS = 'styleClass';

	/**
	 * Name of the id field
	 * @var string
	 */
	const FIELD_STYLE_ID = 'styleId';

	/**
	 * Translation key of the submit button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
     * Construct this form
     * @param string $action action where this form will point to
     * @param int $level Level of the heading
     * @param int $styleClass Style class for the heading
     * @param int $styleId Style id for the heading
     * @return null
	 */
	public function __construct($action, $level = null, $styleClass = null, $styleId = null) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$factory = FieldFactory::getInstance();

		$levels = array();
		for ($i = 1; $i <= 5; $i++) {
		    $levels[$i] = $i;
		}
		$levelField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_LEVEL, $level);
		$levelField->setOptions($levels);

		$styleClassField = $factory->createField(FieldFactory::TYPE_STRING, self::FIELD_STYLE_CLASS, $styleClass);

		$styleIdField = $factory->createField(FieldFactory::TYPE_STRING, self::FIELD_STYLE_ID, $styleId);

		$this->addField($levelField);
		$this->addField($styleClassField);
		$this->addField($styleIdField);
	}

	/**
	 * Get the heading level
	 * @return int
	 */
	public function getLevel() {
	    return $this->getValue(self::FIELD_LEVEL);
	}

	/**
	 * Get the style class for the heading
	 * @return boolean
	 */
	public function getStyleClass() {
	    return $this->getValue(self::FIELD_STYLE_CLASS);
	}

	/**
	 * Get the style id for the heading
	 * @return boolean
	 */
	public function getStyleId() {
	    return $this->getValue(self::FIELD_STYLE_ID);
	}

}