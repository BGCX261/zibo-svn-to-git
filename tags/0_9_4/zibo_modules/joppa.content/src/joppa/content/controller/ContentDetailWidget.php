<?php

namespace joppa\content\controller;

use joppa\content\form\ContentDetailPropertiesForm;
use joppa\content\model\ContentProperties;
use joppa\content\model\ContentViewFactory;
use joppa\content\view\ContentDetailPropertiesView;

use joppa\controller\JoppaWidget;
use joppa\model\content\Content;
use joppa\model\content\ContentFacade;
//use joppa\model\NodeModel;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\orm\ModelManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\ObjectFactory;

use zibo\ZiboException;

/**
 * Widget to show the detail of a content type
 */
class ContentDetailWidget extends JoppaWidget {

    /**
     * Relative path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/content.detail.png';

    /**
     * Translation key of the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.content.detail';

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
     * Translation key for the name of the id field
     * @var string
     */
    const TRANSLATION_PARAMETER_ID = 'joppa.content.label.parameter.id';

    /**
     * Translation key for the view label
     * @var string
     */
    const TRANSLATION_VIEW = 'joppa.content.label.view';

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
        return '*';
    }

    /**
     * Action to display the widget
     * @return null
     */
    public function indexAction() {
        $params = func_get_args();
        if (!$params || count($params) != 1) {
        	$this->setError404();
        	return;
        }

        $id = array_pop($params);

        $contentProperties = $this->getContentProperties();

        $modelName = $contentProperties->getModelName();
        if (!$modelName) {
            return;
        }

        $model = ModelManager::getInstance()->getModel($modelName);

        $query = $this->getModelQuery($contentProperties, $model, $this->locale, $id);

        $content = $this->getResult($contentProperties, $model, $query);

        $view = $this->getView($contentProperties, $content);
        if ($view) {
            $this->response->setView($view);
        }
    }

    /**
     * Gets the model query
     * @param joppa\content\model\ContentProperties $contentProperties
     * @param zibo\library\orm\model\Model $model
     * @param string $locale Code of the locale
     * @param string $id The id of the record to fetch
     * @return zibo\library\orm\query\ModelQuery
     */
    private function getModelQuery(ContentProperties $contentProperties, $model, $locale, $id) {
        $includeUnlocalizedData = $contentProperties->getIncludeUnlocalized();

        $query = $model->createQuery($contentProperties->getRecursiveDepth(), $locale, $includeUnlocalizedData);

        $modelFields = $contentProperties->getModelFields();
        if ($modelFields) {
            foreach ($modelFields as $fieldName) {
                $query->addFields('{' . $fieldName . '}');
            }
        }

        $idField = $contentProperties->getParameterId();
        $query->addCondition('{' . $idField . '} = %1%', $id);

        $condition = $contentProperties->getCondition();
        if ($condition) {
            $query->addCondition($condition);
        }

        $order = $contentProperties->getOrder();
        if ($order) {
            $query->addOrderBy($order);
        }

        return $query;
    }

    /**
     * Gets the result from the query
     * @param zibo\library\orm\model\Model $model
     * @param zibo\library\orm\query\ModelQuery $query
     * @param joppa\content\model\ContentProperties $properties
     * @return array Array with Content objects
     */
    private function getResult(ContentProperties $contentProperties, $model, $query) {
        $data = $query->queryFirst();
        if (!$data) {
            return $data;
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

        return $content;
    }

    /**
     * Gets the view
     * @param joppa\content\model\ContentProperties $properties
     * @param joppa\model\content\Content $content
     * @return joppa\content\view\ContentView
     */
    private function getView(ContentProperties $contentProperties, $content) {
        $view = $contentProperties->getView();

        $detailViews = ContentViewFactory::getInstance()->getDetailViews();
        if (!array_key_exists($view, $detailViews)) {
            return null;
        }

        $viewClass = $detailViews[$view];

        $objectFactory = new ObjectFactory();
        $view = $objectFactory->create($viewClass, ContentViewFactory::INTERFACE_DETAIL);
        $view->setContent($this->properties->getWidgetId(), $content, $contentProperties);

        return $view;
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
            $preview .= $translator->translate(self::TRANSLATION_INCLUDE_UNLOCALIZED) . ': ' . $translator->translate(ContentDetailPropertiesForm::TRANSLATION_YES) . '<br />';
        } else {
            $preview .= $translator->translate(self::TRANSLATION_INCLUDE_UNLOCALIZED) . ': ' . $translator->translate(ContentDetailPropertiesForm::TRANSLATION_NO) . '<br />';
        }

        $parameterId = $contentProperties->getParameterId();
        if ($parameterId) {
            $preview .= $translator->translate(self::TRANSLATION_PARAMETER_ID) . ': ' . $parameterId. '<br />';
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
    	$node = $this->properties->getNode();
    	$properties = $this->getContentProperties();

        $form = new ContentDetailPropertiesForm($this->request->getBasePath(), $node, $properties);
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

        $view = new ContentDetailPropertiesView($form, $fieldsAction);
        $this->response->setView($view);
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