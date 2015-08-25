<?php

namespace joppa\form\backend;

use joppa\model\Node;
use joppa\model\NodeModel;
use joppa\model\NodeSettingModel;
use joppa\model\Site;
use joppa\model\SiteModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;
use zibo\library\smarty\ThemedResourceHandler;

use zibo\ZiboException;

/**
 * Form to manage the properties of a site
 */
class SiteForm extends NodeForm {

    /**
     * Name of the form
     * @var string
     */
	const NAME = 'formSite';

	/**
	 * Name of the site id field
	 * @var string
	 */
	const FIELD_SITE_ID = 'siteId';

	/**
	 * Name of the site version field
	 * @var string
	 */
	const FIELD_SITE_VERSION = 'siteVersion';

	/**
	 * Name of the default node field
	 * @var string
	 */
	const FIELD_DEFAULT_NODE = 'defaultNode';

	/**
	 * Name of the localization method field
	 * @var string
	 */
	const FIELD_LOCALIZATION_METHOD = 'localizationMethod';

	/**
	 * Name of the base URL field
	 * @var string
	 */
	const FIELD_BASE_URL = 'baseUrl';

	/**
	 * Translation key for the copy localization method
	 * @var string
	 */
	const TRANSLATION_LOCALIZATION_METHOD_COPY = 'joppa.label.localization.method.copy';

	/**
	 * Translation key for the unique localization method
	 * @var string
	 */
	const TRANSLATION_LOCALIZATION_METHOD_UNIQUE = 'joppa.label.localization.method.unique';

	/**
	 * Construct this form
     * @param string $action url where this form will point to
     * @param joppa\model\Site $site
     * @return null
	 */
	public function __construct($action, Site $site) {
		parent::__construct($action, $site->node, $site->node);

		$defaultNode = null;
		if ($site->defaultNode) {
		    if (is_numeric($site->defaultNode)) {
		        $defaultNode = $site->defaultNode;
		    } else {
		        $defaultNode = $site->defaultNode->id;
		    }
		}

		$factory = FieldFactory::getInstance();

		$defaultNodeField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_DEFAULT_NODE, $defaultNode);
		$defaultNodeField->setOptions($this->getDefaultNodeOptions($site->node));

		$localizationMethodField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_LOCALIZATION_METHOD, $site->localizationMethod);
		$localizationMethodField->setOptions($this->getLocalizationMethodOptions());
		if ($site->id) {
			$localizationMethodField->setIsDisabled(true);
		}

		$this->addField($factory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_SITE_ID, $site->id));
		$this->addField($factory->createField(FieldFactory::TYPE_HIDDEN, self::FIELD_SITE_VERSION, $site->version));
		$this->addField($defaultNodeField);
		$this->addField($localizationMethodField);
		$this->addField($factory->createField(FieldFactory::TYPE_STRING, self::FIELD_BASE_URL, $site->baseUrl));
	}

	/**
	 * Gets the site which is set to this form
	 * @return joppa\model\Site
	 */
	public function getSite() {
		$siteModel = ModelManager::getInstance()->getModel(SiteModel::NAME);

		$id = $this->getValue(self::FIELD_SITE_ID);

		try {
		    $site = $siteModel->getSite($id, 1);
            $site->version = $this->getValue(self::FIELD_SITE_VERSION);
		} catch (ZiboException $e) {
			$site = $siteModel->createSite();
		}

		$site->node = $this->getNode($site->node);
		$site->defaultNode = $this->getValue(self::FIELD_DEFAULT_NODE);
		$site->localizationMethod = $this->getValue(self::FIELD_LOCALIZATION_METHOD);
		$site->baseUrl = $this->getValue(self::FIELD_BASE_URL);

		return $site;
	}

    /**
     * Gets the options for the default node field
     * @param joppa\model\Node $node node of the site
     * @return array array with a list of the node tree
     */
    private function getDefaultNodeOptions(Node $node) {
        $options = array(0 => '---');

        if (!$node->id) {
            return $options;
        }

        $nodeModel = ModelManager::getInstance()->getModel(NodeModel::NAME);
        $siteModel = ModelManager::getInstance()->getModel(SiteModel::NAME);

        $nodeTree = $siteModel->getNodeTreeForNode($node, null, null, null, false, false);
        $nodes = $nodeModel->createListFromNodeTree($nodeTree);

        return $options + $nodes;
    }

    /**
     * Gets the options for the localization method field
     * @return array Array with a list of the localization methods
     */
    private function getLocalizationMethodOptions() {
    	$translator = I18n::getInstance()->getTranslator();

        return array(
            SiteModel::LOCALIZATION_METHOD_COPY => $translator->translate(self::TRANSLATION_LOCALIZATION_METHOD_COPY),
            SiteModel::LOCALIZATION_METHOD_UNIQUE => $translator->translate(self::TRANSLATION_LOCALIZATION_METHOD_UNIQUE),
        );
    }

}