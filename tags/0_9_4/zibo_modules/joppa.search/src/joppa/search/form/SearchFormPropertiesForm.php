<?php

namespace joppa\search\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\orm\ModelManager;

/**
 * Form to manage the properties of the search form widget
 */
class SearchFormPropertiesForm extends Form {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formSearchFormProperties';

	/**
	 * Name of the node field
	 * @var string
	 */
	const FIELD_NODE = 'node';

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
     * @param int $nodeId id of the node which contains a search result widget
     * @return null
	 */
	public function __construct($action, $nodeId = null) {
		parent::__construct($action, self::NAME);

		$factory = FieldFactory::getInstance();

		$nodeField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_NODE, $nodeId);
		$nodeField->setOptions($this->getNodeOptions());

		$this->addField($nodeField);
		$this->addField($factory->createSubmitField(self::FIELD_SAVE, self::TRANSLATION_SAVE));
		$this->addField($factory->createSubmitField(self::FIELD_CANCEL, self::TRANSLATION_CANCEL));
	}

	/**
	 * Get the node id for the results
	 * @return array
	 */
	public function getNodeId() {
	    return $this->getValue(self::FIELD_NODE);
	}

	/**
	 * Get the options for the node field
	 * @return array Array with nodes which contain a search result widget. The array has the node id as key and the node name as value.
	 */
	private function getNodeOptions() {
        $nodeModel = ModelManager::getInstance()->getModel('Node');
        $nodes = $nodeModel->getNodesForWidget('joppa', 'searchResult');

        $options = array('0' => '---');
        foreach ($nodes as $node) {
            $options[$node->id] = $node->name;
        }

        return $options;
	}

}