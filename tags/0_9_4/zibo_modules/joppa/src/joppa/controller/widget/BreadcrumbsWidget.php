<?php

namespace joppa\controller\widget;

use joppa\controller\JoppaWidget;

use joppa\form\widget\BreadcrumbsWidgetPropertiesForm;

use joppa\model\NodeModel;

use joppa\view\widget\BreadcrumbsWidgetPropertiesView;
use joppa\view\widget\BreadcrumbsWidgetView;

use zibo\admin\controller\LocalizeController;

use zibo\library\html\Breadcrumbs;

/**
 * Widget to display the breadcrumbs of the current node
 */
class BreadcrumbsWidget extends JoppaWidget {

    /**
     * Relative path to the icon of this widget
     * @var string
     */
	const ICON = 'web/images/joppa/widget/breadcrumbs.png';

	/**
	 * Setting key for the filter value
	 * @var string
	 */
	const SETTING_LABEL = 'label.';

	/**
	 * Setting key for the filter value
	 * @var string
	 */
	const SETTING_FILTER = 'filter';

	/**
	 * Setting key for the style id value
	 * @var string
	 */
	const SETTING_STYLE_ID = 'style.id';

	/**
	 * Translation key for the name of the widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.widget.breadcrumbs.name';

    /**
     * Translation key for the label label
     * @var string
     */
    const TRANSLATION_LABEL = 'joppa.widget.breadcrumbs.label.label';

    /**
     * Translation key for the filter label
     * @var string
     */
    const TRANSLATION_FILTER = 'joppa.widget.breadcrumbs.label.filter';

    /**
     * Translation key for the style id label
     * @var unknown_type
     */
    const TRANSLATION_STYLE_ID = 'joppa.widget.breadcrumbs.label.style.id';

    /**
     * Hook with the orm
     * @var string
     */
    public $useModels = NodeModel::NAME;

    /**
     * Construct this widget
     * @return string
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Action to display the a breadcrumbs view to the response
     * @return null
     */
    public function indexAction() {
    	$label = $this->getLabel();
    	$styleId = $this->getStyleId();

    	$breadcrumbs = $this->getBreadcrumbs();
    	$breadcrumbs = $this->applyFilter($breadcrumbs);
    	if ($label) {
            $breadcrumbs->setLabel($label);
    	}
    	if ($styleId) {
            $breadcrumbs->setId($styleId);
    	}

    	$view = new BreadcrumbsWidgetView($breadcrumbs);
    	$this->response->setView($view);
    }

    /**
     * Gets a preview for the properties of this widget
     * @return string
     */
    public function getPropertiesPreview() {
    	$translator = $this->getTranslator();

    	$label = $this->getLabel();
    	$filter = $this->getFilter();
    	$styleId = $this->getStyleId();

    	$preview = '';
    	if ($label) {
    		$preview .= $translator->translate(self::TRANSLATION_LABEL) . ': ' . $label . '<br />';
    	}
    	if ($filter) {
    		$preview .= $translator->translate(self::TRANSLATION_FILTER) . ': ';
    		$filterPreview = '';
    		foreach ($filter as $node) {
    			$filterPreview .= ($filterPreview ? ', ' : '') . $node->name;
    		}
    		$preview .= $filterPreview . '<br />';
    	}
    	if ($styleId) {
    		$preview .= $translator->translate(self::TRANSLATION_STYLE_ID) . ': ' . $styleId . '<br />';
    	}

    	return $preview;
    }

    /**
     * Action to handle and show the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $label = $this->getLabel();
        $filter = $this->getFilter();
        $styleId = $this->getStyleId();

        $rootNodeId = $this->properties->getNode()->getRootNodeId();

        $form = new BreadcrumbsWidgetPropertiesForm($this->request->getBasePath(), $rootNodeId, $label, $filter, $styleId);
        if ($form->isSubmitted()) {
            if (!$form->getValue(BreadcrumbsWidgetPropertiesForm::BUTTON_SUBMIT)) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $label = $form->getLabel();
                $filter = $form->getFilter();
                $styleId = $form->getStyleId();

                $this->setLabel($label);
                $this->setFilter($filter);
                $this->setStyleId($styleId);

                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new BreadcrumbsWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Get the label value from the settings
     * @return string
     */
    private function getLabel() {
        return $this->properties->getWidgetProperty(self::SETTING_LABEL . $this->locale);
    }

    /**
     * Set the label value to the settings
     * @param string $label
     * @return null
     */
    private function setLabel($label) {
        $this->properties->setWidgetProperty(self::SETTING_LABEL . $this->locale, $label);
    }

    /**
     * Get the filter value from the settings
     * @return array Array with node ids as key and the node as value
     */
    private function getFilter() {
        $filter = $this->properties->getWidgetProperty(self::SETTING_FILTER);
        if (!$filter) {
        	return array();
        }

        $filters = explode(',', $filter);
        $filter = array();
        foreach ($filters as $id) {
        	$node = $this->models[NodeModel::NAME]->getNode($id, 0);
        	if ($node) {
                $filter[$id] = $node;
        	}
        }

        return $filter;
    }

    /**
     * Set the filter value to the settings
     * @param array $filter Array with node ids as key
     * @return null
     */
    private function setFilter(array $filter) {
        $filter = implode(',', array_keys($filter));
        $this->properties->setWidgetProperty(self::SETTING_FILTER, $filter);
    }

    /**
     * Get the style id from the settings
     * @return string
     */
    private function getStyleId() {
        return $this->properties->getWidgetProperty(self::SETTING_STYLE_ID);
    }

    /**
     * Set the style id to the settings
     * @param string $styleId
     * @return null
     */
    private function setStyleId($styleId) {
        $this->properties->setWidgetProperty(self::SETTING_STYLE_ID, $styleId);
    }

    /**
     * Apply the set filter to a breadcrumbs object, removes all the crumbs with the name of a filtered node
     * @return zibo\library\html\Breadcrumbs
     */
    private function applyFilter(Breadcrumbs $breadcrumbs) {
        $filters = $this->getFilter();
        if (!$filters) {
            return $breadcrumbs;
        }

        foreach ($filters as $filter) {
            $breadcrumbs->removeBreadcrumb($filter->name);
        }

        return $breadcrumbs;
    }

}