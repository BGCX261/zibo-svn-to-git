<?php

namespace joppa\content\controller;

use joppa\content\form\ContentOverviewPropertiesForm;
use joppa\content\model\ContentProperties;
use joppa\content\model\ContentViewFactory;
use joppa\content\model\PaginationProperties;
use joppa\content\view\ContentOverviewPropertiesView;

use joppa\controller\JoppaWidget;

use joppa\model\content\Content;
use joppa\model\content\ContentFacade;
use joppa\model\NodeModel;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\orm\ModelManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * Widget to show a overview of a content type
 */
class ContentOverviewWidget extends JoppaWidget {

    /**
     * Relative path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/content.overview.png';

    /**
     * Translation key of the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.content.overview';

    /**
     * Translation key for the model label
     * @var string
     */
    const TRANSLATION_MODEL = 'joppa.content.label.model';

    /**
     * Translation key for the fields label
     * @var string
     */
    const TRANSLATION_FIELDS = 'joppa.content.label.fields';

    /**
     * Translation key for the fields label
     * @var string
     */
    const TRANSLATION_RECURSIVE_DEPTH = 'joppa.content.label.depth';

    /**
     * Translation key for the include unlocalized label
     * @var string
     */
    const TRANSLATION_INCLUDE_UNLOCALIZED = 'joppa.content.label.unlocalized';

    /**
     * Translation key for the condition label
     * @var string
     */
    const TRANSLATION_CONDITION = 'joppa.content.label.condition';

    /**
     * Translation key for the order label
     * @var string
     */
    const TRANSLATION_ORDER = 'joppa.content.label.order';

    /**
     * Translation key for the view label
     * @var string
     */
    const TRANSLATION_VIEW = 'joppa.content.label.view';

    /**
     * Translation key for the model label
     * @var string
     */
    const TRANSLATION_PROPERTIES_UNSET = 'joppa.content.label.properties.unset';

    /**
     * Name of the page action
     * @var string
     */
    const ACTION_PAGE = 'page';

    /**
     * Hook with the ORM module
     * @var string
     */
    public $useModels = NodeModel::NAME;

    /**
     * Construct this widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Gets the names of the possible request parameters of this widget
     * @return array
     */
    public function getRequestParameters() {
        return array(self::ACTION_PAGE);
    }

    /**
     * Action to display the widget
     * @return null
     */
    public function indexAction() {
    	$contentProperties = $this->getContentProperties();
        $modelName = $contentProperties->getModelName();

        if (!$modelName) {
            return;
        }

        $functionArguments = func_get_args();

        $arguments = null;
        $page = 1;
        $pages = 1;
        if ($contentProperties->willShowPagination()) {
        	$arguments = $this->parseArguments($functionArguments);
        	if (array_key_exists(self::ACTION_PAGE, $arguments)) {
        		$page = $arguments[self::ACTION_PAGE];
        		if (!is_numeric($page) || $page <= 0) {
        			$page = 1;
        		}
        	}
        }

        $model = ModelManager::getInstance()->getModel($modelName);

        $query = $this->getModelQuery($model, $contentProperties, $this->locale, $page);

        if ($contentProperties->willShowPagination()) {
        	$rows = max(0, $query->count() - $contentProperties->getPaginationOffset());
        	$pages = ceil($rows / $contentProperties->getPaginationRows());

        	if ($arguments && $contentProperties->useAjaxForPagination() && $this->request->isXmlHttpRequest()) {
        		$this->setIsContent(true);
        	}
        }

        $result = $this->getResult($model, $query, $contentProperties);

        $view = $this->getView($result, $contentProperties, $pages, $page);
        if ($view) {
            $this->response->setView($view);
        }
    }

    /**
     * Gets a preview of the properties of this widget
     * @return string
     */
    public function getPropertiesPreview() {
    	$translator = $this->getTranslator();
    	$contentProperties = $this->getContentProperties();

    	$modelName = $contentProperties->getModelName();
    	if (!$modelName) {
    		return $translator->translate(self::TRANSLATION_PROPERTIES_UNSET);
    	}

    	$preview = $translator->translate(self::TRANSLATION_MODEL) . ': ' . $modelName . '<br />';

    	$fields = $contentProperties->getModelFields();
    	if ($fields) {
            $preview .= $translator->translate(self::TRANSLATION_FIELDS) . ': ' . implode(', ', $fields) . '<br />';
    	}

        $preview .= $translator->translate(self::TRANSLATION_RECURSIVE_DEPTH) . ': ' . $contentProperties->getRecursiveDepth() . '<br />';

    	$includeUnlocalized = $contentProperties->getIncludeUnlocalized();
    	if ($includeUnlocalized) {
	        $preview .= $translator->translate(self::TRANSLATION_INCLUDE_UNLOCALIZED) . ': ' . $translator->translate(ContentOverviewPropertiesForm::TRANSLATION_YES) . '<br />';
    	} else {
	        $preview .= $translator->translate(self::TRANSLATION_INCLUDE_UNLOCALIZED) . ': ' . $translator->translate(ContentOverviewPropertiesForm::TRANSLATION_NO) . '<br />';
    	}

    	$condition = $contentProperties->getCondition();
    	if ($condition) {
            $preview .= $translator->translate(self::TRANSLATION_CONDITION) . ': ' . $condition . '<br />';
    	}

    	$order = $contentProperties->getOrder();
    	if ($order) {
            $preview .= $translator->translate(self::TRANSLATION_ORDER) . ': ' . $order . '<br />';
    	}

    	$view = $contentProperties->getView();
    	if ($view) {
            $preview .= $translator->translate(self::TRANSLATION_VIEW) . ': ' . $view . '<br />';
    	}

    	return $preview;
    }

    /**
     * Action to show and edit the properties of this widget
     * @return null
     */
    public function propertiesAction() {
    	$contentProperties = $this->getContentProperties();

    	$form = new ContentOverviewPropertiesForm($this->request->getBasePath(), $this->properties->getNode(), $contentProperties);
    	if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
            	$this->response->setRedirect($this->request->getBaseUrl());
            	return false;
            }

            try {
	            $form->validate();

	            $contentProperties = $form->getContentProperties();
	            $contentProperties->setToWidgetProperties($this->properties, $this->locale);

	            $this->response->setRedirect($this->request->getBaseUrl());
	            return true;
            } catch (ValidationException $exception) {

            }
    	}

    	$ajaxUrl = Zibo::getInstance()->getRequest()->getBaseUrl() . Request::QUERY_SEPARATOR . AjaxController::ROUTE . Request::QUERY_SEPARATOR;
    	$fieldsAction = $ajaxUrl . AjaxController::ACTION_FIELDS . Request::QUERY_SEPARATOR;
    	$orderFieldsAction = $ajaxUrl . AjaxController::ACTION_ORDER_FIELDS . Request::QUERY_SEPARATOR;

    	$view = new ContentOverviewPropertiesView($form, $fieldsAction, $orderFieldsAction);
    	$this->response->setView($view);

    	return false;
    }

    /**
     * Gets the view
     * @param array $result
     * @param joppa\content\model\ContentProperties $properties
     * @param integer $pages
     * @param integer $page
     * @return joppa\content\view\ContentView
     */
    private function getView(array $result, ContentProperties $contentProperties, $pages = 1, $page = 1) {
    	$view = $contentProperties->getView();

    	$listViews = ContentViewFactory::getInstance()->getOverviewViews();
        if (!array_key_exists($view, $listViews)) {
            return null;
        }

        $viewClass = $listViews[$view];

    	$paginationProperties = null;
    	if ($contentProperties->willShowPagination() && $pages > 1) {
    		$paginationUrl = $this->request->getBasePath() . Request::QUERY_SEPARATOR . self::ACTION_PAGE . Request::QUERY_SEPARATOR . '%page%';
    		$paginationProperties = new PaginationProperties($paginationUrl, $pages, $page);
    	}

    	$moreUrl = null;
    	if ($contentProperties->willShowMoreLink()) {
            $node = $this->models[NodeModel::NAME]->getNode($contentProperties->getMoreNode(), 0, $this->locale);
            $moreUrl = $this->request->getBaseUrl() . Request::QUERY_SEPARATOR . $node->getRoute();
    	}

    	$objectFactory = new ObjectFactory();
    	$view = $objectFactory->create($viewClass, ContentViewFactory::INTERFACE_OVERVIEW);
    	$view->setContent($this->properties->getWidgetId(), $result, $contentProperties, $paginationProperties, $moreUrl);

    	return $view;
    }

    /**
     * Gets the result from the query
     * @param zibo\library\orm\model\Model $model
     * @param zibo\library\orm\query\ModelQuery $query
     * @param joppa\content\model\ContentProperties $properties
     * @return array Array with Content objects
     */
    private function getResult($model, $query, ContentProperties $contentProperties) {
    	$result = $query->query();
    	if (!$result) {
    		return $result;
    	}

    	$meta = $model->getMeta();

    	$modelTable = $meta->getModelTable();
    	$dataFormatter = $meta->getDataFormatter();

    	$titleFormat = $contentProperties->getContentTitleFormat();
    	if (!$titleFormat && $modelTable->hasDataFormat(DataFormatter::FORMAT_TITLE)) {
    		$titleFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_TITLE)->getFormat();
    	}

    	$teaserFormat = $contentProperties->getContentTeaserFormat();
    	if (!$teaserFormat && $modelTable->hasDataFormat(DataFormatter::FORMAT_TEASER)) {
    		$teaserFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_TEASER)->getFormat();
    	}

    	$imageFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_IMAGE, false);
    	if ($imageFormat) {
    		$imageFormat = $imageFormat->getFormat();
    	}

    	$dateFormat = $modelTable->getDataFormat(DataFormatter::FORMAT_DATE, false);
    	if ($dateFormat) {
    		$dateFormat = $dateFormat->getFormat();
    	}

    	try {
            $mapper = ContentFacade::getInstance()->getMapper($model->getName());
    	} catch (ZiboException $e) {
    		$mapper = null;
    	}

    	foreach ($result as $index => $data) {
    		$title = $dataFormatter->formatData($data, $titleFormat);
    		$url = null;
            $teaser = null;
            $image = null;
            $date = null;

    		if ($teaserFormat) {
    			$teaser = $dataFormatter->formatData($data, $teaserFormat);
    		}

    		if ($imageFormat) {
    			$image = $dataFormatter->formatData($data, $imageFormat);
    		}

    		if ($dateFormat) {
    			$date = $dataFormatter->formatData($data, $dateFormat);
    		}

    		if ($mapper) {
    			$url = $mapper->getUrl($data);
    		}

    		$content = new Content($title, $url, $teaser, $image, $date, $data);

    		$result[$index] = $content;
    	}

    	return $result;
    }

    /**
     * Gets the model query
     * @param zibo\library\orm\model\Model $model
     * @param joppa\content\model\ContentProperties $contentProperties
     * @param string $locale Code of the locale
     * @param integer $page Page number
     * @return zibo\library\orm\query\ModelQuery
     */
    public function getModelQuery($model, ContentProperties $contentProperties, $locale, $page = 1) {
    	$includeUnlocalizedData = $contentProperties->getIncludeUnlocalized();

        $query = $model->createQuery($contentProperties->getRecursiveDepth(), $locale, $includeUnlocalizedData);

        $modelFields = $contentProperties->getModelFields();
        if ($modelFields) {
            foreach ($modelFields as $fieldName) {
                $query->addFields('{' . $fieldName . '}');
            }
        }

        $condition = $contentProperties->getCondition();
        if ($condition) {
        	$query->addCondition($condition);
        }

        $order = $contentProperties->getOrder();
        if ($order) {
            $query->addOrderBy($order);
        }

        if ($contentProperties->isPaginationEnabled()) {
        	$paginationOffset = $contentProperties->getPaginationOffset();

        	$rows = $contentProperties->getPaginationRows();
            $offset = ($page - 1) * $rows;

            if ($paginationOffset) {
                $offset += $paginationOffset;
            }

            $query->setLimit($rows, $offset);
        }

        return $query;
    }

    /**
     * Gets the properties
     * @return joppa\content\model\ContentProperties
     */
    private function getContentProperties() {
        $contentProperties = new ContentProperties();
        $contentProperties->getFromWidgetProperties($this->properties, $this->locale);

        return $contentProperties;
    }

}