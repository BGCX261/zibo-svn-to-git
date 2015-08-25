<?php

namespace joppa\controller\widget;

use joppa\controller\JoppaWidget;

use joppa\form\widget\RedirectWidgetPropertiesForm;

use joppa\model\NodeModel;

use joppa\view\widget\RedirectWidgetPropertiesView;

use zibo\core\Request;

use zibo\library\validation\exception\ValidationException;

/**
 * Widget to perform a redirect
 */
class RedirectWidget extends JoppaWidget {

    /**
     * Relative path to the icon of this widget
     * @var string
     */
	const ICON = 'web/images/joppa/widget/redirect.png';

	/**
	 * Setting key for the redirect node
	 * @var string
	 */
    const PROPERTY_NODE = 'node';

    /**
	 * Setting key for the redirect url
	 * @var string
	 */
    const PROPERTY_URL = 'url';

    /**
     * Translation key for the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.widget.redirect.name';

    /**
     * Translation key for the properties preview. This translation requires variable target
     * @var string
     */
    const TRANSLATION_PREVIEW = 'joppa.widget.redirect.label.preview';

    /**
     * Translation key for the unset properties preview.
     * @var string
     */
    const TRANSLATION_PREVIEW_UNSET = 'joppa.widget.redirect.label.preview.unset';

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
     * Action to redirect to the stored redirect url if it's set
     * @return null
     */
    public function indexAction() {
		$url = $this->properties->getWidgetProperty(self::PROPERTY_URL);
		if ($url) {
			$this->response->setRedirect($url);
			return;
		}

		$nodeId = $this->properties->getWidgetProperty(self::PROPERTY_NODE);
		if ($nodeId) {
			$node = $this->models[NodeModel::NAME]->getNode($nodeId, 0, $this->locale);

			$this->response->setRedirect($this->request->getBaseUrl() . Request::QUERY_SEPARATOR . $node->getRoute());
		}
    }

    /**
     * Get a preview of the properties of this widget
     * @return string
     */
    public function getPropertiesPreview() {
    	$target = null;

        $url = $this->properties->getWidgetProperty(self::PROPERTY_URL);
        if ($url) {
        	$target = $url;
        } else {
	        $nodeId = $this->properties->getWidgetProperty(self::PROPERTY_NODE);
	        if ($nodeId) {
	            $node = $this->models[NodeModel::NAME]->getNode($nodeId, 0, $this->locale);
	            $target = $node->name;
	        }
        }

        $translator = $this->getTranslator();

        if ($target) {
            return $translator->translate(self::TRANSLATION_PREVIEW, array('target' => $target));
        }

        return $translator->translate(self::TRANSLATION_PREVIEW_UNSET);
    }

    /**
     * Action to show and handle the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $url = $this->properties->getWidgetProperty(self::PROPERTY_URL);
        $nodeId = $this->properties->getWidgetProperty(self::PROPERTY_NODE);

        $node = $this->properties->getNode();

        $form = new RedirectWidgetPropertiesForm($this->request->getBasePath(), $node, $nodeId, $url);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $form->validate();

                $this->properties->setWidgetProperty(self::PROPERTY_URL, $form->getUrl());
                $this->properties->setWidgetProperty(self::PROPERTY_NODE, $form->getNode());
                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new RedirectWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

}