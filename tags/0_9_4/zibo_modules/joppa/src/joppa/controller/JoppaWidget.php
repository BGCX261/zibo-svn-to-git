<?php

namespace joppa\controller;

use joppa\model\Node;

use zibo\library\html\Breadcrumbs;
use zibo\library\widget\controller\AbstractWidget;

/**
 * AbstractWidget extended with Joppa functionality
 */
class JoppaWidget extends AbstractWidget {

	/**
	 * Translation key for the properties saved information message
	 * @var string
	 */
    const TRANSLATION_PROPERTIES_SAVED = 'joppa.information.properties.saved';

    /**
     * breadcrumbs of the page holding this widget
     * @var zibo\library\html\Breadcrumbs
     */
	private $breadcrumbs;

	/**
	 * flag to set whether the request to this widget returns cacheable content
	 * @var boolean
	 */
	private $isCacheable = false;

	/**
	 * flag to set whether this is the only widget to be displayed in the containing region
	 * @var boolean
	 */
	private $isContent = false;

	/**
	 * The node which is invoking this widget
	 * @var joppa\model\Node
	 */
	protected $node;

	/**
     * Hook for Joppa to set the breadcrumbs of the page to this widget
     * @param zibo\library\html\Breadcrumbs $breadcrumbs
     * @return null
	 */
	public function setBreadcrumbs(Breadcrumbs $breadcrumbs) {
		$this->breadcrumbs = $breadcrumbs;
	}

	/**
	 * Get the breadcrumbs of the page
     * @return zibo\library\html\Breadcrumbs
	 */
	public function getBreadcrumbs() {
		return $this->breadcrumbs;
	}

	/**
     * Add a breadcrumb to the page
     * @param string $url url for the breadcrumb
     * @param string $label label for the breadcrumb
     * @return null
	 */
	public function addBreadcrumb($url, $label) {
		$this->breadcrumbs->addBreadcrumb($url, $label);
	}

	/**
     * Add a translated breadcrumb to the page
     * @param string $url url for the breadcrumb
     * @param string $translationKey translation key for the label of the breadcrumb
     * @param array $vars optional translation variables for the translator
     * @return null
	 */
	public function addTranslatedBreadcrumb($url, $translationKey, array $vars = null) {
		$translator = $this->getTranslator();
		$label = $translator->translate($translationKey, $vars);

		$this->addBreadcrumb($url, $label);
	}

	/**
     * Sets whether the current request to this widget is cacheable
     * @param boolean $isCacheable true if the request is cacheable
     * @return null
	 */
	protected function setIsCacheable($isCacheable) {
		$this->isCacheable = $isCacheable;
	}

	/**
     * Gets whether the current request to this widget is cacheable
     * @return boolean true if the request is cacheable
	 */
	public function isCacheable() {
		return $this->isCacheable;
	}

	/**
     * Sets if this is the only widget to be displayed in the containing region
     * @param boolean $isContent true to only display this widget
     * @return null
	 */
	protected function setIsContent($isContent) {
		$this->isContent = $isContent;
	}

	/**
     * Gets whether this is the only widget to be displayed in the containing region
     * @return boolean true to only display this widget
	 */
	public function isContent() {
		return $this->isContent;
	}

	/**
	 * Sets the node which is invoking this widget
	 * @param joppa\model\Node $node The node which is invoking this widget
	 * @return null
	 */
	public function setNode(Node $node) {
		$this->node = $node;
	}

	/**
	 * Gets the node which is invoking this widget
	 * @return joppa\model\Node
	 */
	public function getNode() {
		return $this->node;
	}

}