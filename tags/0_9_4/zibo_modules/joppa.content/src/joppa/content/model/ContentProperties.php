<?php

namespace joppa\content\model;

use zibo\library\orm\query\ModelQuery;
use zibo\library\widget\model\WidgetProperties;

/**
 * Data container for the properties of a content widget
 */
class ContentProperties {

	/**
	 * Name of the model name setting
	 * @var string
	 */
	const PROPERTY_MODEL_NAME = 'model';

	/**
	 * Name of the model fields setting
	 * @var string
	 */
	const PROPERTY_MODEL_FIELDS = 'fields';

	/**
	 * Name of the recursive depth setting
	 * @var string
	 */
	const PROPERTY_RECURSIVE_DEPTH = 'depth';

	/**
	 * Name of the include unlocalized setting
	 * @var string
	 */
	const PROPERTY_INCLUDE_UNLOCALIZED = 'include.unlocalized';

    /**
     * Name of the condition setting
     * @var string
     */
    const PROPERTY_CONDITION = 'condition';

    /**
     * Name of the order setting
     * @var string
     */
    const PROPERTY_ORDER = 'order';

	/**
	 * Name of the pagination enabled setting
	 * @var string
	 */
	const PROPERTY_PAGINATION_ENABLE = 'pagination.enable';

	/**
	 * Name of the pagination rows setting
	 * @var string
	 */
	const PROPERTY_PAGINATION_ROWS = 'pagination.rows';

	/**
	 * Name of the pagination offset setting
	 * @var string
	 */
	const PROPERTY_PAGINATION_OFFSET = 'pagination.offset';

	/**
	 * Name of the pagination show setting
	 * @var string
	 */
	const PROPERTY_PAGINATION_SHOW = 'pagination.show';

	/**
	 * Name of the pagination ajax setting
	 * @var string
	 */
	const PROPERTY_PAGINATION_AJAX = 'pagination.ajax';

	/**
	 * Name of the more enabled setting
	 * @var string
	 */
	const PROPERTY_MORE_SHOW = 'more.show.';

	/**
	 * Name of the more node setting
	 * @var string
	 */
	const PROPERTY_MORE_NODE = 'more.node.';

	/**
	 * Name of the more label setting
	 * @var string
	 */
	const PROPERTY_MORE_LABEL = 'more.label.';

	/**
	 * Name of the parameters type setting
	 * @var string
	 */
	const PROPERTY_PARAMETERS_TYPE = 'parameters.type';

	/**
	 * Name of the id field setting
	 * @var string
	 */
	const PROPERTY_PARAMETER_ID = 'parameter.id';

	/**
	 * Name of the view class setting
	 * @var string
	 */
	const PROPERTY_VIEW = 'view';

	/**
	 * Name of the title format setting
	 * @var string
	 */
	const PROPERTY_FORMAT_TITLE = 'format.title';

	/**
	 * Name of the teaser format setting
	 * @var string
	 */
	const PROPERTY_FORMAT_TEASER = 'format.teaser';

	/**
	 * Name of the title setting
	 * @var string
	 */
	const PROPERTY_TITLE = 'title.';

	/**
	 * Name of the empty result message setting
	 * @var string
	 */
	const PROPERTY_EMPTY_RESULT_MESSAGE = 'message.result.empty.';

	/**
	 * Separator for the model fields setting
	 * @var string
	 */
	const SEPARATOR_MODEL_FIELDS = ',';

	/**
	 * Numeric parameters type
	 * @var string
	 */
	const PARAMETERS_TYPE_NUMERIC = 'numeric';

	/**
	 * Named parameters type
	 * @var string
	 */
	const PARAMETERS_TYPE_NAMED = 'named';

	/**
	 * Predefined view type
	 * @var string
	 */
	const VIEW_TYPE_PREDEFINED = 'predefined';

	/**
	 * Custom view type
	 * @var string
	 */
	const VIEW_TYPE_CUSTOM = 'custom';

	/**
	 * Name of the model to query
	 * @var string
	 */
	private $modelName;

	/**
	 * Array with the fields to select, null to select all
	 * @var array
	 */
	private $modelFields;

	/**
	 * Recursive depth for the relations of the model
	 * @var integer
	 */
	private $recursiveDepth;

	/**
	 * Include unlocalized data flag for the query
	 * @var string
	 */
	private $includeUnlocalized;

    /**
     * Expression for the condition
     * @var string
     */
    private $condition;

    /**
     * Expression for the order by
     * @var string
     */
    private $order;

	/**
	 * Flag to see if pagination is enabled
	 * @var boolean
	 */
	private $isPaginationEnabled;

	/**
	 * Number of rows per page
	 * @var integer
	 */
	private $paginationRows;

	/**
	 * Offset for the pagination
	 * @var integer
	 */
	private $paginationOffset;

	/**
	 * Flag to see if the pagination should be showed
	 * @var boolean
	 */
	private $showPagination;

	/**
	 * Flag to see if the pagination should be done with ajax
	 * @var boolean
	 */
	private $useAjaxForPagination;

	/**
	 * Flag to see if the more link shouls be showed
	 * @var boolean
	 */
	private $showMore;

	/**
	 * Node to link to for the more link
	 * @var integer
	 */
	private $moreNode;

	/**
	 * Label for the more link
	 * @var string
	 */
	private $moreLabel;

	/**
	 * Name of the parameters type
	 * @var string
	 */
	private $parametersType;

	/**
	 * Name of the view
	 * @var string
	 */
	private $view;

	/**
	 * Format of the title
	 * @var string
	 */
	private $contentTitleFormat;

	/**
	 * Format of the teaser
	 * @var string
	 */
	private $contentTeaserFormat;

	/**
	 * Title for the view
	 * @var string
	 */
	private $title;

	/**
	 * Message when the result is empty
	 * @var string
	 */
	private $emptyResultMessage;

	/**
	 * Name of the id field for the detail
	 * @var string
	 */
	private $parameterId;

	/**
	 * Sets the model name
	 * @param string $modelName
	 * @return null
	 */
	public function setModelName($modelName) {
		$this->modelName = $modelName;
	}

	/**
	 * Gets the model name
	 * @return string
	 */
	public function getModelName() {
		return $this->modelName;
	}

	/**
	 * Sets the model fields
	 * @param array $fields Array with the name of the field as value;
	 * @return null
	 */
	public function setModelFields(array $fields = null) {
		$this->modelFields = $fields;
	}

	/**
	 * Gets the model fields
	 * @return array Array with the name of the field as value;
	 */
	public function getModelFields() {
		return $this->modelFields;
	}

	/**
	 * Sets the recursive depth for the query
	 * @param integer $recursiveDepth
	 * @return null
	 */
	public function setRecursiveDepth($recursiveDepth) {
		$this->recursiveDepth = $recursiveDepth;
	}

	/**
	 * Gets the recursive depth for the query
	 * @return integer
	 */
	public function getRecursiveDepth() {
		return $this->recursiveDepth;
	}

	/**
	 * Sets the include unlocalized flag for the query
	 * @param string $includeUnlocalized
	 * @return null
	 */
	public function setIncludeUnlocalized($includeUnlocalized) {
		$this->includeUnlocalized = $includeUnlocalized;
	}

	/**
	 * Gets the include unlocalized flag for the query
	 * @return string
	 */
	public function getIncludeUnlocalized() {
		if (!$this->includeUnlocalized) {
			return false;
		}

		return ModelQuery::INCLUDE_UNLOCALIZED_FETCH;
	}

    /**
     * Sets the expression for the condition part of the query
     * @param string $expression Condition expression
     * @return null
     */
    public function setCondition($expression) {
        $this->condition = $expression;
    }

    /**
     * Gets the expression for the condition part of the query
     * @return string Condition expression
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * Sets the expression for the order part of the query
     * @param string $expression Order expression
     * @return null
     */
    public function setOrder($expression) {
        $this->order = $expression;
    }

    /**
     * Gets the expression for the order part of the query
     * @return string Order expression
     */
    public function getOrder() {
        return $this->order;
    }

	/**
	 * Sets if the pagination is enabled
	 * @param boolean $flag True to enable the pagination, false otherwise
	 * @return null
	 */
	public function setIsPaginationEnabled($flag) {
		$this->isPaginationEnabled = $flag;
	}

	/**
	 * Gets if the pagination is enabled
	 * @return boolean True to enable the pagination, false otherwise
	 */
	public function isPaginationEnabled() {
		return $this->isPaginationEnabled ? true : false;
	}

	/**
	 * Sets the number of rows per page
	 * @param integer $rows
	 * @return null
	 */
	public function setPaginationRows($rows) {
		$this->paginationRows = $rows;
	}

	/**
	 * Gets the number of rows per page
	 * @return integer
	 */
	public function getPaginationRows() {
		return $this->paginationRows;
	}

	/**
	 * Sets the offset for the pagination
	 * @param integer $offset
	 * @return null
	 */
	public function setPaginationOffset($offset) {
		$this->paginationOffset = $offset;
	}

	/**
	 * Gets the offset of the pagination
	 * @return integer
	 */
	public function getPaginationOffset() {
		return $this->paginationOffset;
	}

	/**
	 * Sets the flag to show the pagination
	 * @param boolean $flag
	 * @return null
	 */
	public function setWillShowPagination($flag) {
		$this->showPagination = $flag;
	}

	/**
	 * Gets the flag to show the pagination
	 * @return boolean
	 */
	public function willShowPagination() {
		return $this->showPagination ? true : false;
	}

	/**
	 * Sets the flag to use ajax for the pagination
	 * @param boolean $flag
	 * @return null
	 */
	public function setUseAjaxForPagination($flag) {
		$this->useAjaxForPagination = $flag;
	}

	/**
	 * Gets the flag to show the pagination
	 * @return boolean
	 */
	public function useAjaxForPagination() {
		return $this->useAjaxForPagination ? true : false;
	}

    /**
     * Sets the flag to show the more link
     * @param boolean $flag
     * @return null
     */
    public function setWillShowMoreLink($flag) {
        $this->showMore = $flag;
    }

    /**
     * Gets the flag to show the more link
     * @return boolean
     */
    public function willShowMoreLink() {
        return $this->showMore ? true : false;
    }

    /**
     * Sets the id of the node for the more link
     * @param integer $node
     * @return null
     */
    public function setMoreNode($node) {
    	$this->moreNode = $node;
    }

    /**
     * Gets the id of the node for the more link
     * @return integer
     */
    public function getMoreNode() {
    	return $this->moreNode;
    }

    /**
     * Sets the label for the more link
     * @param string $label
     * @return null
     */
    public function setMoreLabel($label) {
    	$this->moreLabel = $label;
    }

    /**
     * Gets the label for the more link
     * @return string
     */
    public function getMoreLabel() {
    	return !$this->moreLabel ? 'more ...' : $this->moreLabel;
    }

    /**
     * Sets the parameters type
     * @param string $type
     * @return null
     */
    public function setParametersType($type) {
    	$this->parametersType = $type;
    }

    /**
     * Gets the parameters type
     * @return string
     */
    public function getParametersType() {
    	return $this->parametersType;
    }

	/**
	 * Sets the name of the view
	 * @param string $view Internal name of the view to use
	 * @return null
	 */
	public function setView($view) {
		$this->view = $view;
	}

	/**
	 * Gets the name of the view
	 * @return string
	 */
	public function getView() {
		return $this->view;
	}

	/**
	 * Sets the format for the teaser of the content
	 * @param string $format
	 * @return null
	 */
	public function setContentTeaserFormat($format) {
		$this->contentTeaserFormat = $format;
	}

	/**
	 * Gets the format for the title of the content
	 * @return string
	 */
	public function getContentTeaserFormat() {
		return $this->contentTeaserFormat;
	}

	/**
	 * Sets the format for the title of the content
	 * @param string $format
	 * @return null
	 */
	public function setContentTitleFormat($format) {
		$this->contentTitleFormat = $format;
	}

	/**
	 * Gets the format for the title of the content
	 * @return string
	 */
	public function getContentTitleFormat() {
		return $this->contentTitleFormat;
	}

    /**
     * Sets the title for the view
     * @param string $title
     * @return null
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Gets the title for the view
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the message for an empty result
     * @param string $message
     * @return null
     */
    public function setEmptyResultMessage($message) {
        $this->emptyResultMessage = $message;
    }

    /**
     * Gets the message for an empty result
     * @return string
     */
    public function getEmptyResultMessage() {
        return $this->emptyResultMessage;
    }

    /**
     * Set the name of the id field
     * @param string $id
     * @return null
     */
    public function setParameterId($id) {
    	$this->parameterId = $id;
    }

    /**
     * Gets the name of the id field
     * @return string
     */
    public function getParameterId() {
    	return $this->parameterId;
    }

	/**
	 * Read the properties of the content from the widget properties
	 * @param zibo\library\widget\model\WidgetProperties $properties
	 * @return null
	 */
	public function getFromWidgetProperties(WidgetProperties $properties, $locale) {
		$this->modelName = $properties->getWidgetProperty(self::PROPERTY_MODEL_NAME);
		$this->recursiveDepth = $properties->getWidgetProperty(self::PROPERTY_RECURSIVE_DEPTH);
		$this->includeUnlocalized = $properties->getWidgetProperty(self::PROPERTY_INCLUDE_UNLOCALIZED);
		$this->isPaginationEnabled = $properties->getWidgetProperty(self::PROPERTY_PAGINATION_ENABLE);
		$this->paginationRows = $properties->getWidgetProperty(self::PROPERTY_PAGINATION_ROWS);
		$this->paginationOffset = $properties->getWidgetProperty(self::PROPERTY_PAGINATION_OFFSET);
		$this->condition = $properties->getWidgetProperty(self::PROPERTY_CONDITION);
		$this->order = $properties->getWidgetProperty(self::PROPERTY_ORDER);
		$this->parametersType = $properties->getWidgetProperty(self::PROPERTY_PARAMETERS_TYPE);
		$this->view = $properties->getWidgetProperty(self::PROPERTY_VIEW);
		$this->contentTitleFormat = $properties->getWidgetProperty(self::PROPERTY_FORMAT_TITLE);
		$this->contentTeaserFormat = $properties->getWidgetProperty(self::PROPERTY_FORMAT_TEASER);
		$this->title = $properties->getWidgetProperty(self::PROPERTY_TITLE . $locale);
		$this->emptyResultMessage = $properties->getWidgetProperty(self::PROPERTY_EMPTY_RESULT_MESSAGE . $locale);
		$this->showPagination = $properties->getWidgetProperty(self::PROPERTY_PAGINATION_SHOW);
		$this->useAjaxForPagination = $properties->getWidgetProperty(self::PROPERTY_PAGINATION_AJAX);
		$this->showMore = $properties->getWidgetProperty(self::PROPERTY_MORE_SHOW . $locale);
		$this->moreLabel = $properties->getWidgetProperty(self::PROPERTY_MORE_LABEL . $locale);
		$this->moreNode = $properties->getWidgetProperty(self::PROPERTY_MORE_NODE . $locale);
		$this->parameterId = $properties->getWidgetProperty(self::PROPERTY_PARAMETER_ID);

		$fieldsString = $properties->getWidgetProperty(self::PROPERTY_MODEL_FIELDS);
		if (!$fieldsString) {
			$this->modelFields = null;
			return;
		}

		$this->modelFields = array();

		$tokens = explode(self::SEPARATOR_MODEL_FIELDS, $fieldsString);
		foreach ($tokens as $token) {
			$fieldName = trim($token);

			$this->modelFields[$fieldName] = $fieldName;
		}
	}

	/**
	 * Write the properties of the content to the widget properties
	 * @param zibo\library\widget\model\WidgetSettings $properties
	 * @return null
	 */
	public function setToWidgetProperties(WidgetProperties $properties, $locale) {
        $fields = null;
        if ($this->modelFields) {
        	$fields = implode(self::SEPARATOR_MODEL_FIELDS, $this->modelFields);
        }

        $properties->setWidgetProperty(self::PROPERTY_MODEL_NAME, $this->modelName);
        $properties->setWidgetProperty(self::PROPERTY_MODEL_FIELDS, $fields);
        $properties->setWidgetProperty(self::PROPERTY_RECURSIVE_DEPTH, $this->recursiveDepth);
        $properties->setWidgetProperty(self::PROPERTY_INCLUDE_UNLOCALIZED, $this->includeUnlocalized);
        $properties->setWidgetProperty(self::PROPERTY_CONDITION, $this->condition);
        $properties->setWidgetProperty(self::PROPERTY_ORDER, $this->order);
        $properties->setWidgetProperty(self::PROPERTY_PAGINATION_ENABLE, $this->isPaginationEnabled);
        if ($this->isPaginationEnabled) {
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_ROWS, $this->paginationRows);
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_OFFSET, $this->paginationOffset);
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_SHOW, $this->showPagination);
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_AJAX, $this->useAjaxForPagination);
            if ($this->showMore) {
	            $properties->setWidgetProperty(self::PROPERTY_MORE_SHOW . $locale, $this->showMore);
	            $properties->setWidgetProperty(self::PROPERTY_MORE_LABEL . $locale, $this->moreLabel);
	            $properties->setWidgetProperty(self::PROPERTY_MORE_NODE . $locale, $this->moreNode);
            } else {
	            $properties->setWidgetProperty(self::PROPERTY_MORE_SHOW . $locale, null);
	            $properties->setWidgetProperty(self::PROPERTY_MORE_LABEL . $locale, null);
	            $properties->setWidgetProperty(self::PROPERTY_MORE_NODE . $locale, null);
            }
        } else {
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_ROWS, null);
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_OFFSET, null);
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_SHOW, null);
            $properties->setWidgetProperty(self::PROPERTY_PAGINATION_AJAX, null);
            $properties->setWidgetProperty(self::PROPERTY_MORE_SHOW . $locale, null);
            $properties->setWidgetProperty(self::PROPERTY_MORE_LABEL . $locale, null);
            $properties->setWidgetProperty(self::PROPERTY_MORE_NODE . $locale, null);
        }
        $properties->setWidgetProperty(self::PROPERTY_PARAMETER_ID, $this->parameterId);
        $properties->setWidgetProperty(self::PROPERTY_PARAMETERS_TYPE, $this->parametersType);
        $properties->setWidgetProperty(self::PROPERTY_VIEW, $this->view);
        $properties->setWidgetProperty(self::PROPERTY_FORMAT_TITLE, $this->contentTitleFormat);
        $properties->setWidgetProperty(self::PROPERTY_FORMAT_TEASER, $this->contentTeaserFormat);
        $properties->setWidgetProperty(self::PROPERTY_TITLE . $locale, $this->title);
        $properties->setWidgetProperty(self::PROPERTY_EMPTY_RESULT_MESSAGE . $locale, $this->emptyResultMessage);
	}

}