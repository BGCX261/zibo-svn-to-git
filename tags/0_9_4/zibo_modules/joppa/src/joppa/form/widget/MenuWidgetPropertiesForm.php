<?php

namespace joppa\form\widget;

use joppa\controller\widget\MenuWidget;
use joppa\model\Node;
use joppa\model\NodeModel;
use joppa\model\SiteModel;

use zibo\admin\controller\LocalizeController;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;

/**
 * Form to manage the properties of the menu widget
 */
class MenuWidgetPropertiesForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formMenuWidgetProperties';

	/**
	 * Name of the parent field
	 * @var string
	 */
	const FIELD_PARENT = 'parent';

	/**
	 * Name of the depth field
	 * @var string
	 */
	const FIELD_DEPTH = 'depth';

	/**
	 * Name of the show title field
	 * @var string
	 */
	const FIELD_SHOW_TITLE = 'showTitle';

	/**
	 * Translation key for a absolute parent
	 * @var string
	 */
	const TRANSLATION_PARENT_ABSOLUTE = 'joppa.widget.menu.label.parent.absolute';

	/**
	 * Translation key for the current node
	 * @var string
	 */
	const TRANSLATION_PARENT_CURRENT = 'joppa.widget.menu.label.parent.current';

	/**
	 * Translation key for a relative parent
	 * @var string
	 */
	const TRANSLATION_PARENT_RELATIVE = 'joppa.widget.menu.label.parent.relative';

	/**
	 * Translation key of the submit button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
     * Construct this form
     * @param string $action action where this form will point to
     * @param int $rootNodeId id of the root node for the parent field
     * @param int $parent id of the root node for this menu
     * @param int $depth number of levels this menu will go
     * @param boolean $showTitle flag to determine whether to show a title or not
     * @return null
	 */
	public function __construct($action, Node $node, $parent, $depth, $showTitle) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$factory = FieldFactory::getInstance();

		$parentField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_PARENT, $parent);
		$parentField->setOptions($this->getParentOptions($node));

		$depths = array();
		for ($i = 0; $i <= 10; $i++) {
		    $depths[$i] = $i;
		}
		$depthField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_DEPTH, $depth);
		$depthField->setOptions($depths);

		$this->addField($parentField);
		$this->addField($depthField);
		$this->addField($factory->createField(FieldFactory::TYPE_BOOLEAN, self::FIELD_SHOW_TITLE, $showTitle));
	}

	/**
	 * Gets the options for the parent field
	 * @param joppa\model\Node $node
	 * @return array Array with the parent options
	 */
	private function getParentOptions(Node $node) {
		$translator = I18n::getInstance()->getTranslator();
        $nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
        $siteModel = ModelManager::getInstance()->getModel(SiteModel::NAME);

        $nodeTree = $siteModel->getNodeTreeForNode($node);

        $parents = $nodeModel->createListFromNodeTree($nodeTree);

        if ($node->parent) {
	        $tokens = explode(NodeModel::PATH_SEPARATOR, $node->parent);
	        $rootNodeId = array_shift($tokens);

	        $rootNode = $nodeModel->getNode($rootNodeId, 0);
        } else {
        	$rootNodeId = $node->id;
        	$rootNode = $node;
        }

        $parents = array($rootNodeId => '/') + $parents;
        $parents[MenuWidget::PARENT_CURRENT] = $translator->translate(self::TRANSLATION_PARENT_CURRENT);

        $levels = $nodeModel->getChildrenLevelsForNode($rootNode) - 1;

        for ($i = 0; $i <= $levels; $i++) {
        	$parents[MenuWidget::PARENT_ABSOLUTE . $i] = $translator->translate(self::TRANSLATION_PARENT_ABSOLUTE, array('level' => $i));
        }

        for ($i = 0; $i <= $levels; $i++) {
            $level = $i + 1;
        	$parents[MenuWidget::PARENT_RELATIVE . $level] = $translator->translate(self::TRANSLATION_PARENT_RELATIVE, array('level' => '-' . $level));
        }

        return $parents;
	}

	/**
	 * Get the node id of the parent which is currently set to this form
	 * @return int
	 */
	public function getParent() {
	    return $this->getValue(self::FIELD_PARENT);
	}

	/**
	 * Get the depth value which is currently set to this form
	 * @return int
	 */
	public function getDepth() {
	    return $this->getValue(self::FIELD_DEPTH);
	}

	/**
	 * Get the show title flag which is currently set to this form
	 * @return boolean
	 */
	public function getShowTitle() {
	    return $this->getValue(self::FIELD_SHOW_TITLE);
	}

}