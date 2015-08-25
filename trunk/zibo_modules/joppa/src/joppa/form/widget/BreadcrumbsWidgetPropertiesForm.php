<?php

namespace joppa\form\widget;

use joppa\model\NodeModel;
use joppa\model\SiteModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\orm\ModelManager;

/**
 * Form to manage the properties of the breadcrumbs widget
 */
class BreadcrumbsWidgetPropertiesForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formBreadcrumbsWidgetProperties';

	/**
	 * Name of the style id field
	 * @var string
	 */
	const FIELD_STYLE_ID = 'styleId';

	/**
	 * Name of the label field
	 * @var string
	 */
	const FIELD_LABEL = 'label';

	/**
	 * Name of the filter field
	 * @var string
	 */
	const FIELD_FILTER = 'filter';

	/**
     * Construct this form
     * @param string $action url where this form will point to
     * @param int $rootNodeId id of the root node for the filter field
     * @param string $label Label for the breadcrumbs
     * @param mixed $filter value for the filter field
     * @param string $styleId value for the style id field
     * @return null
	 */
	public function __construct($action, $rootNodeId, $label, $filter, $styleId = null) {
		parent::__construct($action, self::NAME);

		$factory = FieldFactory::getInstance();

		$siteModel = ModelManager::getInstance()->getModel(SiteModel::NAME);
		$nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);

        $nodeTree = $siteModel->getNodeTreeForNode($rootNodeId, null, null, null, true, false);
        $nodeList = $nodeModel->createListFromNodeTree($nodeTree);

		$labelField = $factory->createField(FieldFactory::TYPE_STRING, self::FIELD_LABEL, $label);

		$filterField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_FILTER, $filter);
		$filterField->setOptions($nodeList);
		$filterField->setIsMultiple(true);

		$styleIdField = $factory->createField(FieldFactory::TYPE_STRING, self::FIELD_STYLE_ID, $styleId);

		$this->addField($labelField);
		$this->addField($filterField);
		$this->addField($styleIdField);
	}

	/**
	 * Get the value of the label field
	 * @return string
	 */
	public function getLabel() {
	    return $this->getValue(self::FIELD_LABEL);
	}

	/**
	 * Get the value of the filter field
	 * @return array
	 */
	public function getFilter() {
	    return $this->getValue(self::FIELD_FILTER);
	}

	/**
	 * Get the value of the style id field
	 * @return string
	 */
	public function getStyleId() {
	    return $this->getValue(self::FIELD_STYLE_ID);
	}

}