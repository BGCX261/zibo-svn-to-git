<?php

namespace joppa\controller\widget;

use joppa\controller\JoppaWidget;

use joppa\form\widget\MenuWidgetPropertiesForm;

use joppa\model\NodeModel;
use joppa\model\NodeTypeFacade;
use joppa\model\SiteModel;

use joppa\view\widget\MenuWidgetPropertiesView;
use joppa\view\widget\MenuWidgetView;

use zibo\library\orm\query\ModelQuery;
use zibo\library\orm\ModelManager;
use zibo\library\validation\exception\ValidationException;

use zibo\ZiboException;

/**
 * Widget to show a menu of the node tree, or part of the node tree
 */
class MenuWidget extends JoppaWidget {

	/**
	 * Relative path to the icon of this widget
	 * @var string
	 */
	const ICON = 'web/images/joppa/widget/menu.png';

    /**
     * Default depth value of a menu widget
     * @var int
     */
	const DEFAULT_DEPTH = 3;

	/**
	 * Default parent value of a menu widget
	 * @var string
	 */
	const DEFAULT_PARENT = 'default';

	/**
	 * Default show title value of a menu widget
	 * @var boolean
	 */
	const DEFAULT_SHOW_TITLE = false;

	/**
	 * Parent prefix for a absolute parent
	 * @var string
	 */
	const PARENT_ABSOLUTE = 'absolute';

	/**
	 * Parent value for the current node
	 * @var string
	 */
	const PARENT_CURRENT = 'current';

	/**
	 * Parent prefix for a relative parent
	 * @var string
	 */
	const PARENT_RELATIVE = 'relative';

	/**
	 * Setting key for the parent value
	 * @var string
	 */
    const PROPERTY_PARENT = 'node';

    /**
     * Setting key for the depth value
     * @var string
     */
    const PROPERTY_DEPTH = 'depth';

    /**
     * Setting key for the title value
     * @var string
     */
    const PROPERTY_SHOW_TITLE = 'title';

    /**
     * Translation key for the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.widget.menu.name';

    /**
     * Translation key for parent
     * @var string
     */
    const TRANSLATION_PARENT = 'joppa.widget.menu.label.parent';

    /**
     * Translation key for a parent on a absolute level
     * @var string
     */
    const TRANSLATION_PARENT_ABSOLUTE = 'joppa.widget.menu.label.parent.absolute';

    /**
     * Translation key for a parent on a relative level
     * @var string
     */
    const TRANSLATION_PARENT_RELATIVE = 'joppa.widget.menu.label.parent.relative';

    /**
     * Translation key for depth
     * @var string
     */
    const TRANSLATION_DEPTH = 'joppa.widget.menu.label.depth';

    /**
     * Translation key for show title
     * @var string
     */
    const TRANSLATION_SHOW_TITLE = 'joppa.widget.menu.label.title.show';

    /**
     * Translation key for yes
     * @var string
     */
    const TRANSLATION_YES = 'label.yes';

    /**
     * Translation key for no
     * @var string
     */
    const TRANSLATION_NO = 'label.no';

    /**
     * Hook with the orm module
     * @var string
     */
    public $useModels = array(NodeModel::NAME, SiteModel::NAME);

    /**
     * Construct this widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
        $this->setIsCacheable(true);
    }

    /**
     * Action to set a menu view to the response
     * @return null
     */
    public function indexAction() {
        $parent = $this->getParent();
        $depth = $this->getDepth();
        $showTitle = $this->getShowTitle();

        if (!$parent) {
        	return;
        }

        $tree = $this->getTree($parent, $depth);

        $title = null;
		if ($showTitle) {
			$parentNode = $this->models[NodeModel::NAME]->getNode($parent, 0, $this->locale);
			$title = $parentNode->name;
		}

        $view = new MenuWidgetView($tree, $this->node, $title);
        $this->response->setView($view);
    }

    /**
     * Gets the tree for the menu
     * @param integer $parent Id of the parent node
     * @param integer $depth
     * @return array Array with Node obejcts
     */
    private function getTree($parent, $depth) {
        $site = $this->models[SiteModel::NAME]->getSiteForNode($parent, 0, $this->locale);

        if ($site->localizationMethod == SiteModel::LOCALIZATION_METHOD_UNIQUE) {
            $includeUnlocalized = false;
        } else {
            $includeUnlocalized = ModelQuery::INCLUDE_UNLOCALIZED_FETCH;
        }

        $tree = $this->models[NodeModel::NAME]->getNodeTree($parent, null, $depth, $this->locale, $includeUnlocalized, true, true);

        return $tree;
    }

    /**
     * Get a preview of the properties of this widget
     * @return string
     */
    public function getPropertiesPreview() {
    	$translator = $this->getTranslator();

        $parent = $this->getParent();
        $depth = $this->getDepth();
        $showTitle = $this->getShowTitle();

        if ($parent) {
            $nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
            $parentNode = $nodeModel->getNode($parent, 0);
            $parent = $parentNode->name;
        } else {
        	$parent = '---';
        }

        $preview = '';
        $preview .= $translator->translate(self::TRANSLATION_PARENT) . ': ' . $parent . '<br />';
        $preview .= $translator->translate(self::TRANSLATION_DEPTH) . ': ' . $depth . '<br />';
        $preview .= $translator->translate(self::TRANSLATION_SHOW_TITLE) . ': ' . $translator->translate($showTitle ? self::TRANSLATION_YES : self::TRANSLATION_NO);

        return $preview;
    }

    /**
     * Action to handle and show the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $parent = $this->getParent(false);
        $depth = $this->getDepth();
        $showTitle = $this->getShowTitle();

        $form = new MenuWidgetPropertiesForm($this->request->getBasePath(), $this->properties->getNode(), $parent, $depth, $showTitle);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $parent = $form->getParent();
                $depth = $form->getDepth();
                $showTitle = $form->getShowTitle();

                $this->properties->setWidgetProperty(self::PROPERTY_PARENT, $parent);
                $this->properties->setWidgetProperty(self::PROPERTY_DEPTH, $depth);
                $this->properties->setWidgetProperty(self::PROPERTY_SHOW_TITLE, $showTitle);
                $this->response->setRedirect($this->request->getBaseUrl());
                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new MenuWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Get the value for the parent node
     * @param boolean $fetchNodeId Set to false to skip the lookup of the node id
     * @return string
     */
    private function getParent($fetchNode = true) {
        $parent = $this->properties->getWidgetProperty(self::PROPERTY_PARENT, self::DEFAULT_PARENT);

        if (!$fetchNode) {
        	return $parent;
        }

        if (is_numeric($parent)) {
        	return $parent;
        }

        if ($parent === self::DEFAULT_PARENT) {
        	$rootNode = $this->models[NodeModel::NAME]->getRootNode($this->properties->getNode());
        	return $rootNode->id;
        }

        if ($parent == self::PARENT_CURRENT) {
        	return $this->properties->getNode()->id;
        }

        $path = $this->properties->getNode()->getPath();
        $tokens = explode(NodeModel::PATH_SEPARATOR, $path);

        if (strpos($parent, self::PARENT_ABSOLUTE) !== false) {
        	$level = str_replace(self::PARENT_ABSOLUTE, '', $parent);
        } elseif (strpos($parent, self::PARENT_RELATIVE) !== false) {
        	$level = str_replace(self::PARENT_RELATIVE, '', $parent);
        	$tokens = array_reverse($tokens);
        } else {
	        throw new ZiboException('Invalid parent set for this widget: ' . $parent);
        }

        if (!array_key_exists($level, $tokens)) {
            return null;
        }

        return $tokens[$level];
    }

    /**
     * Get the depth value
     * @return int
     */
    private function getDepth() {
        return $this->properties->getWidgetProperty(self::PROPERTY_DEPTH, self::DEFAULT_DEPTH);
    }

    /**
     * Get the show title value
     * @return boolean
     */
    private function getShowTitle() {
        return $this->properties->getWidgetProperty(self::PROPERTY_SHOW_TITLE, self::DEFAULT_SHOW_TITLE);
    }

}