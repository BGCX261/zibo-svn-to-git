<?php

namespace joppa\search\form;

use joppa\search\model\SearchFacade;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\orm\ModelManager;

/**
 * Form to manage the properties of the search result widget
 */
class SearchResultPropertiesForm extends Form {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formSearchResultProperties';

	/**
	 * Name of the content types field
	 * @var string
	 */
	const FIELD_CONTENT_TYPES = 'types';

	/**
	 * Name of the save button
	 * @var string
	 */
	const FIELD_SAVE = 'save';

	/**
	 * Name of the cancel button
	 * @var string
	 */
	const FIELD_CANCEL = 'cancel';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Translation key for the cancel button
	 * @var string
	 */
	const TRANSLATION_CANCEL = 'button.cancel';

	/**
     * Construct this form
     * @param string $action url where this form will point to
     * @param array $contentTypes Array with the name of searchable content types
     * @return null
	 */
	public function __construct($action, array $contentTypes = null) {
		parent::__construct($action, self::NAME);

		$factory = FieldFactory::getInstance();

		$contentTypesField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_CONTENT_TYPES, $contentTypes);
		$contentTypesField->setOptions($this->getSearchableContentTypeOptions());
		$contentTypesField->setIsMultiple(true);

		$this->addField($contentTypesField);
		$this->addField($factory->createSubmitField(self::FIELD_SAVE, self::TRANSLATION_SAVE));
		$this->addField($factory->createSubmitField(self::FIELD_CANCEL, self::TRANSLATION_CANCEL));
	}

	/**
	 * Get the node id for the results
	 * @return array
	 */
	public function getSearchableContentTypes() {
	    return array_keys($this->getValue(self::FIELD_CONTENT_TYPES));
	}

	/**
	 * Get the options for the node field
	 * @return array Array with nodes which contain a search result widget. The array has the node id as key and the node name as value.
	 */
	private function getSearchableContentTypeOptions() {
		$types = SearchFacade::getInstance()->getTypes();

		$options = array();
        foreach ($types as $type) {
            $options[$type] = $type;
        }

        return $options;
	}

}