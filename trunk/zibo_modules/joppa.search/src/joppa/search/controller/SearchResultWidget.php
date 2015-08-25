<?php

namespace joppa\search\controller;

use joppa\controller\JoppaWidget;

use joppa\search\form\SearchForm;
use joppa\search\form\SearchResultPropertiesForm;
use joppa\search\model\SearchFacade;
use joppa\search\view\SearchResultMoreView;
use joppa\search\view\SearchResultPropertiesView;
use joppa\search\view\SearchResultView;

/**
 * Widget to show the results of a search query
 */
class SearchResultWidget extends JoppaWidget {

    /**
     * Name of the more action
     * @var string
     */
    const ACTION_MORE = 'more';

    /**
     * Name of the page action parameter
     * @var string
     */
    const ACTION_PAGE = 'page';

    /**
     * Default number of results for a types search
     * @var int
     */
    const DEFAULT_ITEMS_PAGE = 15;

    /**
     * Default number of results for a type search
     * @var int
     */
    const DEFAULT_ITEMS_PREVIEW = 5;

    /**
     * Relative path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/search.result.png';

    /**
     * Setting key for the searchable content types
     * @var string
     */
    const PROPERTY_CONTENT_TYPES = 'types';

    /**
     * Setting key for the number of items per page
     * @var string
     */
    const PROPERTY_ITEMS_PAGE = 'items.page';

    /**
     * Setting key for the number of items in the preview
     * @var string
     */
    const PROPERTY_ITEMS_PREVIEW = 'items.preview';

    /**
     * Separator between the content types in the searchable content types setting
     * @var string
     */
    const SEPARATOR_CONTENT_TYPES = ',';

    /**
     * Translation key of the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.search.results';


    /**
     * Constructs this widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Gets the allowed parameters for this widget
     * @return string
     */
    public function getRequestParameters() {
    	return '*';
    }

    /**
     * Action to display the search results for a submitted search form
     * @param string $query url encoded search query, if not specified the search form will be checked for a query
     * @return null
     */
    public function indexAction($query = null) {
        $result = null;

    	if (!$query) {
            $action = $this->request->getBasePath();
            $form = new SearchForm($action);
            if ($form->isSubmitted()) {
                $query = $form->getQuery();
                $this->response->setRedirect($action . '/' . urlencode($query));
                return;
            }
    	} else {
            $query = urldecode($query);
            $numItems = $this->getNumberItemsPreview();
            $contentTypes = $this->getSearchableContentTypes();

            $result = SearchFacade::getInstance()->search($query, $numItems, $contentTypes);
    	}

        $urlMore = $this->request->getBasePath() . '/' . self::ACTION_MORE . '/';

        $view = new SearchResultView($query, $result, $urlMore);
        $this->response->setView($view);
    }

    /**
     * Action to perform a search on a specified type
     * @param string $type name of the content type
     * @param string $query url encoded search query
     * @param string $action action for pagination
     * @param string $actionParameter action parameter for pagination
     * @return null
     */
    public function moreAction($type, $query, $action = null, $actionParameter = null) {
    	$pageUrl = $this->request->getBasePath();
    	$pageUrl .= '/' . self::ACTION_MORE . '/' . $type . '/' . $query . '/' . self::ACTION_PAGE . '/%page%';

        $query = urldecode($query);

        $numItems = $this->getNumberItemsPage();

        $page = 1;
        if ($action == self::ACTION_PAGE && $actionParameter) {
        	$page = $actionParameter;
        }

        $result = SearchFacade::getInstance()->searchContent($type, $query, $numItems, $page);
        $pages = ceil($result->getTotalNumResults() / $numItems);

        $view = new SearchResultMoreView($type, $query, $result, $pageUrl, $page, $pages);
        $this->response->setView($view);
    }

    /**
     * Action to show and handle the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $contentTypes = $this->getSearchableContentTypes();

        $form = new SearchResultPropertiesForm($this->request->getBasePath(), $contentTypes);
        if ($form->isSubmitted()) {
            if (!$form->getValue(SearchResultPropertiesForm::FIELD_SAVE)) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
            	$form->validate();

	            $contentTypes = $form->getSearchableContentTypes();

	            $this->setSearchableContentTypes($contentTypes);

                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new SearchResultPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Gets the searchable content types of this widget
     * @return array Array with the name of a searchable content type as value
     */
    private function getSearchableContentTypes() {
    	$contentTypes = $this->properties->getWidgetProperty(self::PROPERTY_CONTENT_TYPES);

    	if (!$contentTypes) {
    		return null;
    	}

    	return explode(self::SEPARATOR_CONTENT_TYPES, $contentTypes);
    }

    /**
     * Sets the searchable content types for this widget
     * @param null|array Array with the name of a searchable content type as value, null for all content types
     * @return null
     */
    private function setSearchableContentTypes(array $contentTypes) {
    	if ($contentTypes) {
	    	$setting = implode(self::SEPARATOR_CONTENT_TYPES, $contentTypes);
    	} else {
    		$setting = null;
    	}

        $this->properties->getWidgetProperty(self::PROPERTY_CONTENT_TYPES, $setting);
    }

    /**
     * Gets the number of items to show in a preview
     * @return int
     */
    private function getNumberItemsPreview() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_ITEMS_PREVIEW, self::DEFAULT_ITEMS_PREVIEW);
    }

    /**
     * Gets the number of items to show in a more-view
     * @return int
     */
    private function getNumberItemsPage() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_ITEMS_PAGE, self::DEFAULT_ITEMS_PAGE);
    }

}