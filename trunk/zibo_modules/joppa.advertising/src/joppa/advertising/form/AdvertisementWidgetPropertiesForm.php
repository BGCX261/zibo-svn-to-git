<?php

namespace joppa\advertising\form;

use joppa\advertising\model\data\AdvertisementBlockData;
use joppa\advertising\model\AdvertisementBlockModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\orm\ModelManager;
use zibo\library\validation\ValidationFactory;

/**
 * Form to manage the properties of an advertisement block
 */
class AdvertisementWidgetPropertiesForm extends SubmitCancelForm {

	/**
	 * Name of the form
	 * @var string
	 */
	const NAME = 'formAdvertisementWidgetProperties';

	/**
	 * Name of the block id field
	 * @var string
	 */
	const FIELD_ID = 'id';

	/**
	 * Name of the block version field
	 * @var string
	 */
	const FIELD_VERSION = 'version';

	/**
	 * Name of the block name field
	 * @var string
	 */
	const FIELD_NAME = 'name';

	/**
	 * Name of the width field
	 * @var string
	 */
	const FIELD_WIDTH = 'width';

	/**
	 * Name of the height field
	 * @var string
	 */
	const FIELD_HEIGHT = 'height';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
     * Constructs a new properties form for a AdvertisementWidget
     * @param string $action URL where this form will point to
     * @param joppa\advertising\model\data\AdvertisementBlockData $block
     * @param int $width
     * @param int $height
     * @return null
	 */
	public function __construct($action, AdvertisementBlockData $block = null, $width = 0, $height = 0) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$factory = FieldFactory::getInstance();

		$id = null;
		$name = null;
		$version = null;
		if ($block) {
		    $id = $block->id;
		    $name = $block->name;
		    $version = $block->version;
		}

		$this->addField($factory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID, $id));
		$this->addField($factory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_VERSION, $version));
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME, $name));
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_WIDTH, $width));
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_HEIGHT, $height));

        $validationFactory = ValidationFactory::getInstance();
        $requiredValidator = $validationFactory->createValidator('required');
        $dimensionValidator = $validationFactory->createValidator('minmax', array('minimum' => 0, 'maximum' => 1000));

        $this->addValidator(self::FIELD_NAME, $requiredValidator);
        $this->addValidator(self::FIELD_WIDTH, $dimensionValidator);
        $this->addValidator(self::FIELD_HEIGHT, $dimensionValidator);
	}

	/**
	 * Gets the advertisement block from this form
	 * @return joppa\advertising\model\data\AdvertisementBlockData
	 */
	public function getAdvertisementBlock() {
	    $model = ModelManager::getInstance()->getModel(AdvertisementBlockModel::NAME);

	    $block = $model->createData(false);
	    $block->id = $this->getValue(self::FIELD_ID);
	    $block->version = $this->getValue(self::FIELD_VERSION);
	    $block->name = $this->getValue(self::FIELD_NAME);

	    return $block;
	}

	/**
	 * Gets the width from this form
	 * @return int
	 */
	public function getWidth() {
	    return $this->getValue(self::FIELD_WIDTH);
	}

	/**
	 * Gets the height from this form
	 * @return int
	 */
	public function getHeight() {
	    return $this->getValue(self::FIELD_HEIGHT);
	}

}