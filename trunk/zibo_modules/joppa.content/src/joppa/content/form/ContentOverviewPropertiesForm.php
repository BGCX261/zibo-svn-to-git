<?php

namespace joppa\content\form;

use joppa\content\model\ContentProperties;
use joppa\content\model\ContentViewFactory;

use joppa\model\Node;
use joppa\model\NodeModel;
use joppa\model\SiteModel;

use zibo\library\database\manipulation\expression\OrderExpression;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\i18n\translation\Translator;
use zibo\library\i18n\I18n;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasField;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\query\ModelQuery;
use zibo\library\orm\ModelManager;

/**
 * Form to edit the properties of a content overview widget
 */
class ContentOverviewPropertiesForm extends AbstractContentPropertiesForm {

    /**
     * Name of the condition expression field
     * @var string
     */
    const FIELD_CONDITION_EXPRESSION = 'conditionExpression';

    /**
     * Name of the order field field
     * @var string
     */
    const FIELD_ORDER_FIELD = 'orderField';

    /**
     * Name of the order direction field
     * @var string
     */
    const FIELD_ORDER_DIRECTION = 'orderDirection';

    /**
     * Name of the order expression field
     * @var string
     */
    const FIELD_ORDER_EXPRESSION = 'orderExpression';

    /**
     * Name of the order add button
     * @var string
     */
    const FIELD_ORDER_ADD = 'orderAdd';

    /**
     * Name of the enable pagination field
     * @var string
     */
    const FIELD_PAGINATION_ENABLE = 'paginationEnable';

    /**
     * Name of the rows per page field
     * @var string
     */
    const FIELD_PAGINATION_ROWS = 'paginationRows';

    /**
     * Name of the offset field
     * @var string
     */
    const FIELD_PAGINATION_OFFSET = 'paginationOffset';

    /**
     * Name of the pagination show field
     * @var string
     */
    const FIELD_PAGINATION_SHOW = 'paginationShow';

    /**
     * Name of the pagination ajax field
     * @var string
     */
    const FIELD_PAGINATION_AJAX = 'paginationAjax';

    /**
     * Name of the parameters type field
     * @var string
     */
    const FIELD_PARAMETERS_TYPE = 'parametersType';

    /**
     * Name of the title format field
     * @var string
     */
    const FIELD_FORMAT_TITLE = 'contentTitleFormat';

    /**
     * Name of the teaser format field
     * @var string
     */
    const FIELD_FORMAT_TEASER = 'contentTeaserFormat';

    /**
     * Name of the title field
     * @var string
     */
    const FIELD_TITLE = 'title';

    /**
     * Name of the empty result message field
     * @var string
     */
    const FIELD_EMPTY_RESULT_MESSAGE = 'emptyResultMessage';

    /**
     * Name of the show more link field
     * @var string
     */
    const FIELD_MORE_SHOW = 'moreShow';

    /**
     * Name of the field for the label of the more link
     * @var string
     */
    const FIELD_MORE_LABEL = 'moreLabel';

    /**
     * Name of the field for the node of the more link
     * @var string
     */
    const FIELD_MORE_NODE = 'moreNode';

    /**
     * Translation key for the asc order direction
     * @var string
     */
    const TRANSLATION_ORDER_DIRECTION_ASC = 'joppa.content.label.order.direction.asc';

    /**
     * Translation key for the desc order direction
     * @var string
     */
    const TRANSLATION_ORDER_DIRECTION_DESC = 'joppa.content.label.order.direction.desc';

    /**
     * Translation key for the none parameters type
     * @var string
     */
    const TRANSLATION_PARAMETERS_TYPE_NONE = 'joppa.content.label.parameters.none';

    /**
     * Translation key for the numeric parameters type
     * @var string
     */
    const TRANSLATION_PARAMETERS_TYPE_NUMERIC = 'joppa.content.label.parameters.numeric';

    /**
     * Translation key for the named parameters type
     * @var string
     */
    const TRANSLATION_PARAMETERS_TYPE_NAMED = 'joppa.content.label.parameters.named';

	/**
	 * Translation key for the yes label
	 * @var string
	 */
	const TRANSLATION_YES = 'label.yes';

	/**
	 * Translation key for the no label
	 * @var string
	 */
	const TRANSLATION_NO = 'label.no';

	/**
	 * Translation key for the add button
	 * @var string
	 */
	const TRANSLATION_ADD = 'button.add';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Constructs a new properties form
	 * @param string $action URL where this form will point to
	 * @param joppa\model\Node $node
	 * @param joppa\content\model\ContentProperties $properties
	 * @return null
	 */
	public function __construct($action, Node $node, ContentProperties $properties) {
		parent::__construct($action, $node, $properties);

		$translator = I18n::getInstance()->getTranslator();
		$modelManager = ModelManager::getInstance();
		$fieldFactory = FieldFactory::getInstance();

		$model = $properties->getModelName();
		$fields = $properties->getModelFields();
		$recursiveDepth = $properties->getRecursiveDepth();
		$includeUnlocalized = $properties->getIncludeUnlocalized();
		$conditionExpression = $properties->getCondition();
		$orderExpression = $properties->getOrder();
		$paginationEnable = $properties->isPaginationEnabled();
		$paginationRows = $properties->getPaginationRows();
		$paginationOffset = $properties->getPaginationOffset();
		$parametersType = $properties->getParametersType();
		$contentTitleFormat = $properties->getContentTitleFormat();
		$contentTeaserFormat = $properties->getContentTeaserFormat();
		$title = $properties->getTitle();
		$emptyResultMessage = $properties->getEmptyResultMessage();
		$paginationShow = $properties->willShowPagination();
		$paginationAjax = $properties->useAjaxForPagination();
		$moreShow = $properties->willShowMoreLink();
		$moreLabel = $properties->getMoreLabel();
		$moreNode = $properties->getMoreNode();

        $conditionExpressionField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_CONDITION_EXPRESSION, $conditionExpression);

        $orderFieldField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_ORDER_FIELD);
        $orderFieldField->setOptions(self::getModelFieldOptions($modelManager, $model, true, false, $recursiveDepth));

        $orderDirectionField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_ORDER_DIRECTION);
        $orderDirectionField->setOptions($this->getOrderDirectionOptions($translator));

        $orderExpressionField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_ORDER_EXPRESSION, $orderExpression);

        $orderAddButton = $fieldFactory->createSubmitField(self::FIELD_ORDER_ADD, self::TRANSLATION_ADD);

		$paginationEnableField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_PAGINATION_ENABLE, $paginationEnable);

		$paginationRowsField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_PAGINATION_ROWS, $paginationRows);
		$paginationRowsField->setOptions($this->getNumericOptions(1, 50));

		$paginationOffsetField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_PAGINATION_OFFSET, $paginationOffset);
		$paginationOffsetField->setOptions($this->getNumericOptions(0, 50));

		$parametersTypeField = $fieldFactory->createField(FieldFactory::TYPE_OPTION, self::FIELD_PARAMETERS_TYPE, $parametersType);
		$parametersTypeField->setOptions($this->getParametersTypeOptions($translator));

		$contentTitleFormatField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_FORMAT_TITLE, $contentTitleFormat);

		$contentTeaserFormatField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_FORMAT_TEASER, $contentTeaserFormat);

		$titleField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_TITLE, $title);

		$emptyResultMessageField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_EMPTY_RESULT_MESSAGE, $emptyResultMessage);

		$paginationShowField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_PAGINATION_SHOW, $paginationShow);

		$paginationAjaxField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_PAGINATION_AJAX, $paginationAjax);

		$moreShowField = $fieldFactory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_MORE_SHOW, $moreShow);

		$moreNodeField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_MORE_NODE, $moreNode);
		$moreNodeField->setOptions($this->getMoreNodeOptions($modelManager, $node));

		$moreLabelField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_MORE_LABEL, $moreLabel);

		$this->addField($conditionExpressionField);
		$this->addField($orderFieldField);
		$this->addField($orderDirectionField);
		$this->addField($orderAddButton);
		$this->addField($orderExpressionField);
		$this->addField($paginationEnableField);
		$this->addField($paginationRowsField);
		$this->addField($paginationOffsetField);
		$this->addField($parametersTypeField);
		$this->addField($contentTitleFormatField);
		$this->addField($contentTeaserFormatField);
		$this->addField($titleField);
		$this->addField($emptyResultMessageField);
		$this->addField($paginationShowField);
		$this->addField($paginationAjaxField);
		$this->addField($moreShowField);
		$this->addField($moreLabelField);
		$this->addField($moreNodeField);
	}

	/**
	 * Gets a content properties object for the submitted form
	 * @return joppa\content\model\ContentProperties
	 */
    public function getContentProperties() {
    	$properties = parent::getContentProperties();
    	$properties->setCondition($this->getValue(self::FIELD_CONDITION_EXPRESSION));
    	$properties->setOrder($this->getValue(self::FIELD_ORDER_EXPRESSION));
    	$properties->setIsPaginationEnabled($this->getValue(self::FIELD_PAGINATION_ENABLE));
    	$properties->setPaginationRows($this->getValue(self::FIELD_PAGINATION_ROWS));
    	$properties->setPaginationOffset($this->getValue(self::FIELD_PAGINATION_OFFSET));
    	$properties->setContentTitleFormat($this->getValue(self::FIELD_FORMAT_TITLE));
    	$properties->setContentTeaserFormat($this->getValue(self::FIELD_FORMAT_TEASER));
    	$properties->setTitle($this->getValue(self::FIELD_TITLE));
    	$properties->setEmptyResultMessage($this->getValue(self::FIELD_EMPTY_RESULT_MESSAGE));
    	$properties->setWillShowPagination($this->getValue(self::FIELD_PAGINATION_SHOW));
    	$properties->setUseAjaxForPagination($this->getValue(self::FIELD_PAGINATION_AJAX));
    	$properties->setWillShowMoreLink($this->getValue(self::FIELD_MORE_SHOW));
    	$properties->setMoreLabel($this->getValue(self::FIELD_MORE_LABEL));
    	$properties->setMoreNode($this->getValue(self::FIELD_MORE_NODE));
    	$properties->setParametersType($this->getValue(self::FIELD_PARAMETERS_TYPE));

    	return $properties;
    }

	/**
	 * Gets the options for the order direction
	 * @param zibo\library\i18n\translation\Translator $translator
	 * @return array
	 */
	private function getOrderDirectionOptions($translator) {
		return array(
            OrderExpression::DIRECTION_ASC => $translator->translate(self::TRANSLATION_ORDER_DIRECTION_ASC),
            OrderExpression::DIRECTION_DESC => $translator->translate(self::TRANSLATION_ORDER_DIRECTION_DESC),
		);
	}

	/**
	 * Gets the options for the parameters type
	 * @param zibo\library\i18n\translation\Translator $translator
	 * @return array
	 */
	private function getParametersTypeOptions($translator) {
		return array(
            'none' => $translator->translate(self::TRANSLATION_PARAMETERS_TYPE_NONE),
            ContentProperties::PARAMETERS_TYPE_NUMERIC => $translator->translate(self::TRANSLATION_PARAMETERS_TYPE_NUMERIC),
            ContentProperties::PARAMETERS_TYPE_NAMED => $translator->translate(self::TRANSLATION_PARAMETERS_TYPE_NAMED),
		);
	}

	/**
	 * Gets the options for the view type
	 * @param zibo\library\i18n\translation\Translator $translator
	 * @return array
	 */
	protected function getViewOptions(Translator $translator) {
		$views = ContentViewFactory::getInstance()->getOverviewViews();

		foreach ($views as $name => $class) {
			$views[$name] = $translator->translate(self::TRANSLATION_VIEW . $name);
		}

		return $views;
	}

	/**
	 * Gets the options for the more node field
	 * @param zibo\library\orm\ModelManager $modelManager
	 * @param Node $node
	 * @return arrau
	 */
	private function getMoreNodeOptions(ModelManager $modelManager, Node $node) {
		$siteModel = $modelManager->getModel(SiteModel::NAME);
		$nodeModel = $modelManager->getModel(NodeModel::NAME);

		$nodeTree = $siteModel->getNodeTreeForNode($node, null, null, null, $loadSettings = true, $isFrontend = true);

		return $nodeModel->createListFromNodeTree($nodeTree);
	}

}