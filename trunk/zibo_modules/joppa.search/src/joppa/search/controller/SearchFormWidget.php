<?php

namespace joppa\search\controller;

use joppa\controller\JoppaWidget;

use joppa\model\NodeModel;

use joppa\search\form\SearchForm;
use joppa\search\form\SearchFormPropertiesForm;
use joppa\search\view\SearchFormView;
use joppa\search\view\SearchFormPropertiesView;

/**
 * Widget to show a search form
 */
class SearchFormWidget extends JoppaWidget {

    /**
     * Relative path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/search.form.png';

    /**
     * Setting key of the node where the search form will point to
     * @var string
     */
    const PROPERTY_NODE = 'node';

    /**
     * Translation key of the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.search.form';

    /**
     * Translation key of the properties preview
     * @var string
     */
    const TRANSLATION_PROPERTIES = 'joppa.search.label.form.properties';

    /**
     * Translation key of the warning when the properties are not set
     * @var string
     */
    const TRANSLATION_WARNING = 'joppa.search.warning.form.properties.unset';

    /**
     * Hook with the orm module
     * @var string
     */
    public $useModels = NodeModel::NAME;

    /**
     * Construct this widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
        $this->setIsCacheable(true);
    }

    /**
     * Action to set the search form to the response
     * @return null
     */
    public function indexAction() {
        $form = null;

        $node = $this->getResultNode();
        if ($node) {
            $form = new SearchForm($this->request->getBaseUrl() . '/' . $node->getRoute());
            $form->isSubmitted();
        }

        $view = new SearchFormView($form);
        $this->response->setView($view);
    }

    /**
     * Get a preview of the properties of this widget
     * @return string
     */
    public function getPropertiesPreview() {
        $translator = $this->getTranslator();

        $node = $this->getResultNode();
        if (!$node) {
            return $translator->translate(self::TRANSLATION_WARNING);
        }

        return $translator->translate(self::TRANSLATION_PROPERTIES, array('node' => $node->name));
    }

    /**
     * Action to show and handle the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $nodeId = $this->properties->getWidgetProperty(self::PROPERTY_NODE);

        $form = new SearchFormPropertiesForm($this->request->getBasePath(), $nodeId);
        if ($form->isSubmitted()) {
            if (!$form->getValue(SearchFormPropertiesForm::FIELD_SAVE)) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $this->properties->setWidgetProperty(self::PROPERTY_NODE, $form->getNodeId());
                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new SearchFormPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Get the node where the search form has to point to
     * @return Node
     */
    private function getResultNode() {
        $nodeId = $this->properties->getWidgetProperty(self::PROPERTY_NODE);
        if (!$nodeId) {
            return null;
        }

        return $this->models[NodeModel::NAME]->getNode($nodeId, 0);
    }

}