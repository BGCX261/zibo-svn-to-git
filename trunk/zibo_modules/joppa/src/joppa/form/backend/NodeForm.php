<?php

namespace joppa\form\backend;

use joppa\model\Node;
use joppa\model\NodeModel;
use joppa\model\NodeSettings;
use joppa\model\NodeSettingModel;
use joppa\model\NodeTypeFacade;
use joppa\model\Theme;

use zibo\admin\controller\LocalizeController;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;
use zibo\library\validation\validator\RequiredValidator;

use zibo\ZiboException;

/**
 * Form to manage the properties of a node
 */
class NodeForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formNode';

	/**
	 * Name of the id field
	 * @var string
	 */
	const FIELD_ID = 'id';

	/**
	 * Name of the version field
	 * @var string
	 */
	const FIELD_VERSION = 'version';

	/**
	 * Name of the name field
	 * @var string
	 */
	const FIELD_NAME = 'name';

	/**
	 * Name of the theme field
	 * @var string
	 */
	const FIELD_THEME = 'theme';

	/**
	 * Name of the route field
	 * @var string
	 */
	const FIELD_ROUTE = 'route';

	/**
	 * Name of the parent field
	 * @var string
	 */
	const FIELD_PARENT = 'parent';

	/**
	 * Name of the locales field
	 * @var string
	 */
	const FIELD_LOCALES = 'locales';

	/**
	 * Name of the meta description field
	 * @var string
	 */
	const FIELD_META_DESCRIPTION = 'metaDescription';

	/**
	 * Name of the meta keywords field
	 * @var string
	 */
	const FIELD_META_KEYWORDS = 'metaKeywords';

	/**
	 * Translation key of the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Translation key of the inherit label
	 * @var string
	 */
	const TRANSLATION_INHERIT = 'joppa.label.inherit';

	/**
	 * Translation key of the all locales label
	 * @var string
	 */
	const TRANSLATION_ALL_LOCALES = 'joppa.label.locales.all';

	/**
	 * Type of the node
	 * @var string
	 */
	private $nodeType;

    /**
     * Construct this form
     * @param string $action url where this form will point to
     * @param joppa\model\Node $site node of the site
     * @param joppa\model\Node $node node to edit
     * @return null
     */
	public function __construct($action, Node $site, Node $node) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		$this->nodeType = $node->type;

		$factory = FieldFactory::getInstance();

        $parentField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_PARENT);
        $parentField->setOptions($this->getParentOptions($node, $site));

        $themesField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_THEME);
        $themesField->setOptions($this->getThemeOptions($node));
        $themesField->addValidator(new RequiredValidator());

        $localesField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_LOCALES);
        $localesField->setOptions($this->getLocalesOptions($node));
        $localesField->setIsMultiple(true);

		$this->addField($factory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_ID));
		$this->addField($factory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_VERSION));
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_NAME));
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_META_DESCRIPTION));
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_META_KEYWORDS));
		$this->addField($parentField);
		$this->addField($themesField);
		$this->addField($localesField);
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_ROUTE));

		$this->setValues($node);
	}

    /**
     * Get the name of the node type
     * @return string
     */
	public function getNodeType() {
	    return $this->nodeType;
	}

    /**
     * Get the node which is set to this form
     * @param joppa\model\Node $node update this node with the form values (optional)
     * @return joppa\model\Node
     */
    public function getNode(Node $node = null) {
		$id = $this->getValue(self::FIELD_ID);

		if (!$node) {
			$model = ModelManager::getInstance()->getModel(NodeModel::NAME);
    		try {
    		    $node = $model->getNode($id);
                $node->version = $this->getValue(self::FIELD_VERSION);
    		} catch (ZiboException $e) {
    		    $node = $model->createNode($this->nodeType);
    		}
		} else {
		    $node->id = $id;
            $node->version = $this->getValue(self::FIELD_VERSION);
		}

		$node->name = $this->getValue(self::FIELD_NAME);
		$node->route = $this->getValue(self::FIELD_ROUTE);
		$node->parent = $this->getValue(self::FIELD_PARENT);

		$theme = $this->getValue(self::FIELD_THEME);
		if (!$theme) {
			$theme = null;
		}

		$availableLocales = '';

		$locales = $this->getValue(self::FIELD_LOCALES);

		foreach ($locales as $locale => $null) {
			if (!$locale) {
				$availableLocales = null;
				break;
			}

			if ($locale == NodeSettingModel::LOCALES_ALL) {
				$availableLocales = $locale;
				break;
			}

			$availableLocales .= ($availableLocales ? NodeSettingModel::LOCALES_SEPARATOR : '') . $locale;
		}

		$localeSuffix = '.' . LocalizeController::getLocale();

		$node->settings->set(NodeSettingModel::SETTING_THEME, $theme);
		$node->settings->set(NodeSettingModel::SETTING_LOCALES, $availableLocales);
		$node->settings->set(NodeSettingModel::SETTING_META_DESCRIPTION . $localeSuffix, $this->getValue(self::FIELD_META_DESCRIPTION));
		$node->settings->set(NodeSettingModel::SETTING_META_KEYWORDS . $localeSuffix, $this->getValue(self::FIELD_META_KEYWORDS));

		return $node;
	}

	/**
     * Set the values of a node to the form
     * @param joppa\model\Node $node
     * @return null
	 */
	protected function setValues(Node $node) {
		$localeSuffix = '.' . LocalizeController::getLocale();

        $this->setValue(self::FIELD_ID, $node->id);
        $this->setValue(self::FIELD_VERSION, $node->version);
        $this->setValue(self::FIELD_NAME, $node->name);
        $this->setValue(self::FIELD_ROUTE, $node->route);
        $this->setValue(self::FIELD_THEME, $node->settings->get(NodeSettingModel::SETTING_THEME, null, false));
        $this->setValue(self::FIELD_META_DESCRIPTION, $node->settings->get(NodeSettingModel::SETTING_META_DESCRIPTION . $localeSuffix, null, false));
        $this->setValue(self::FIELD_META_KEYWORDS, $node->settings->get(NodeSettingModel::SETTING_META_KEYWORDS. $localeSuffix, null, false));
        $this->setValue(self::FIELD_PARENT, $node->parent);

        $availableLocales = $node->settings->get(NodeSettingModel::SETTING_LOCALES, '', false);

        if (!$availableLocales && !$node->settings->getInheritedNodeSettings()) {
        	$availableLocales = NodeSettingModel::LOCALES_ALL;
        } elseif ($availableLocales && $availableLocales != NodeSettingModel::LOCALES_ALL) {
        	$locales = explode(NodeSettingModel::LOCALES_SEPARATOR, $availableLocales);

        	$availableLocales = array();
        	foreach ($locales as $locale) {
        		$locale = trim($locale);
        		$availableLocales[$locale] = $locale;
        	}
        }

        $this->setValue(self::FIELD_LOCALES, $availableLocales);
	}

	/**
     * Get the options for the parent field
     * @param joppa\model\Node $node
     * @param joppa\model\Node $site
     * @return array array with a list of the node tree
	 */
	protected function getParentOptions(Node $node, Node $site = null) {
	    if (!$site || !$site->id) {
	        return array(0 => '---');
	    }

	    $nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);

	    $separator = '/';

	    $parents = array($site->id => $separator);

        $nodes = $nodeModel->getNodeTree($site->id, $node->id);
        $this->addNodesToParentOptions($parents, $nodes, $separator);

        return $parents;
	}

	/**
     * Adds the provided nodes to the parents array
     * @param array $parents by reference
     * @param array $nodes
     * @param string $separator
     * @param string $prefix
     * @return null
	 */
	protected function addNodesToParentOptions(array &$parents, $nodes, $separator = '/', $prefix = '') {
		foreach ($nodes as $node) {
			$newPrefix = $prefix . $separator . $node->name;

			$parents[$node->getPath()] = $newPrefix;

			if ($node->children) {
				$this->addNodesToParentOptions($parents, $node->children, $separator, $newPrefix);
			}
		}
	}

	/**
     * Get the options for the theme field
     * @param joppa\model\Node $node
     * @return array array with a list of themes
	 */
    protected function getThemeOptions(Node $node) {
        $translator = I18n::getInstance()->getTranslator();

        $themes = Theme::getThemes();

	    $inheritedNodeSettings = $node->settings->getInheritedNodeSettings();
	    if (!$inheritedNodeSettings) {
	        return $themes;
	    }

        $inheritThemeSuffix = $this->getThemeInheritSuffix($inheritedNodeSettings);
        $inheritTheme = array('' => $translator->translate(self::TRANSLATION_INHERIT) . $inheritThemeSuffix);

        return $inheritTheme + $themes;
    }

	/**
	 * Get a suffix for the theme inherit label based on the inherited settings
	 * @param joppa\model\NodeSettings $nodeSettings
	 * @return string if a theme is found the suffix will be " ($theme)"
	 */
	private function getThemeInheritSuffix(NodeSettings $nodeSettings) {
	    $theme = $nodeSettings->get(NodeSettingModel::SETTING_THEME, null, true, true);
        if ($theme !== null) {
            return ' (' . $theme . ')';
        }

	    return null;
	}

	/**
	 * Get the options for the locales field
	 * @param joppa\model\Node $node
	 * @return array
	 */
	private function getLocalesOptions(Node $node) {
		$i18n = I18n::getInstance();
		$translator = $i18n->getTranslator();
		$locales = $i18n->getLocaleList();

		$options = array();

        $inheritedNodeSettings = $node->settings->getInheritedNodeSettings();
        if ($inheritedNodeSettings) {
	        $inheritLocalesSuffix = $this->getLocalesInheritSuffix($inheritedNodeSettings);
            $options[''] = $translator->translate(self::TRANSLATION_INHERIT) . $inheritLocalesSuffix;
        }

		$options[NodeSettingModel::LOCALES_ALL] = $translator->translate(self::TRANSLATION_ALL_LOCALES);
		foreach ($locales as $code => $name) {
			$options[$code] = $name;
		}

		return $options;
	}

    /**
     * Get a suffix for the theme inherit label based on the inherited settings
     * @param joppa\model\NodeSettings $nodeSettings
     * @return string if a theme is found the suffix will be " ($theme)"
     */
    private function getLocalesInheritSuffix(NodeSettings $nodeSettings) {
        $locales = $nodeSettings->get(NodeSettingModel::SETTING_LOCALES, null, true, true);
        if ($locales !== null) {
            return ' (' . $locales . ')';
        }

        return null;
    }

}