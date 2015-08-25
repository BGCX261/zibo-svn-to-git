<?php

namespace joppa\form\widget;

use joppa\model\NodeModel;
use joppa\model\SiteModel;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;
use zibo\library\validation\ValidationFactory;

/**
 * Form to manage the properties of the redirect widget
 */
class RedirectWidgetPropertiesForm extends SubmitCancelForm {

    /**
     * Name of this form
     * @var string
     */
	const NAME = 'formRedirectWidgetProperties';

	/**
	 * Name of the redirect type field
	 * @var string
	 */
	const FIELD_REDIRECT_TYPE = 'redirectType';

	/**
	 * Name of the url field
	 * @var string
	 */
	const FIELD_URL = 'url';

	/**
	 * Name of the url field
	 * @var string
	 */
	const FIELD_NODE = 'node';

	/**
	 * Translation key for the save button
	 * @var string
	 */
	const TRANSLATION_SAVE = 'button.save';

	/**
	 * Translation key for the node redirect type
	 * @var string
	 */
	const TRANSLATION_REDIRECT_TYPE_NODE = 'joppa.widget.redirect.label.redirect.type.node';

	/**
	 * Translation key for the url redirect type
	 * @var string
	 */
	const TRANSLATION_REDIRECT_TYPE_URL = 'joppa.widget.redirect.label.redirect.type.url';

	/**
	 * Redirect to a node
	 * @var string
	 */
	const REDIRECT_TYPE_NODE = 'node';

	/**
	 * Redirect to a url
	 * @var string
	 */
	const REDIRECT_TYPE_URL = 'url';

	/**
     * Construct this form
     * @param string $action url where this form will point to
     * @param joppa\model\Node $node Node to retrieve the node list
     * @param string $nodeId node id to set to the form
     * @param string $url url to set to the form
     * @return null
	 */
	public function __construct($action, $node, $nodeId = null, $url = null) {
		parent::__construct($action, self::NAME, self::TRANSLATION_SAVE);

		if ($url) {
			$redirectType = self::REDIRECT_TYPE_URL;
		} else {
			$redirectType = self::REDIRECT_TYPE_NODE;
		}

		$modelManager = ModelManager::getInstance();
		$factory = FieldFactory::getInstance();

        $siteModel = $modelManager->getModel(SiteModel::NAME);
        $nodeModel = $modelManager->getModel(NodeModel::NAME);

        $nodeTree = $siteModel->getNodeTreeForNode($node->id, null, null, null, true, false);
        $nodeList = $nodeModel->createListFromNodeTree($nodeTree);
        if (isset($nodeList[$node->id])) {
        	unset($nodeList[$node->id]);
        }

        $redirectTypeField = $factory->createField(FieldFactory::TYPE_OPTION, self::FIELD_REDIRECT_TYPE, $redirectType);
        $redirectTypeField->setOptions($this->getRedirectTypeOptions());

        $nodeField = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_NODE, $nodeId);
        $nodeField->setOptions($nodeList);

        $urlField = $factory->createField(FieldFactory::TYPE_STRING, self::FIELD_URL, $url);

		$this->addField($redirectTypeField);
		$this->addField($nodeField);
		$this->addField($urlField);
	}

	/**
	 * Validates this form
	 * @return null
	 */
	public function validate() {
		if ($this->getRedirectType() == self::REDIRECT_TYPE_URL) {
	        $validationFactory = ValidationFactory::getInstance();
	        $this->addValidator(self::FIELD_URL, $validationFactory->createValidator('required'));
	        $this->addValidator(self::FIELD_URL, $validationFactory->createValidator('website'));
		}

		parent::validate();
	}

	/**
	 * Gets the redirect type
	 * @return string
	 */
	public function getRedirectType() {
		return $this->getValue(self::FIELD_REDIRECT_TYPE);
	}

	/**
	 * Get the url set on this form
	 * @return string
	 */
	public function getUrl() {
		if ($this->getRedirectType() == self::REDIRECT_TYPE_URL) {
            return $this->getValue(self::FIELD_URL);
		}

		return null;
	}

	/**
	 * Get the node set on this form
	 * @return string
	 */
	public function getNode() {
		if ($this->getValue(self::FIELD_REDIRECT_TYPE) == self::REDIRECT_TYPE_NODE) {
            return $this->getValue(self::FIELD_NODE);
		}

		return null;
	}

	/**
	 * Get the redirect type options
	 * @return array
	 */
	private function getRedirectTypeOptions() {
		$translator = I18n::getInstance()->getTranslator();

		return array(
            self::REDIRECT_TYPE_NODE => $translator->translate(self::TRANSLATION_REDIRECT_TYPE_NODE),
            self::REDIRECT_TYPE_URL => $translator->translate(self::TRANSLATION_REDIRECT_TYPE_URL),
		);
	}

}