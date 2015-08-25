<?php

namespace joppa\controller\backend\action;

use joppa\model\Node;

use joppa\controller\backend\BackendController;

use zibo\library\i18n\translation\Translator;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Abstract controller of a node action
 */
class AbstractNodeAction extends BackendController implements NodeAction {

    /**
     * Route of this action
     * @var string
     */
    private $route;

    /**
     * Translation key for the label of this action
     * @var string
     */
    private $labelTranslationKey;

    /**
     * @var boolean
     */
    private $isAvailableForNode;

    /**
     * Construct the this action
     * @param string $route route of this action
     * @param string $labelTranslationKey translation key for the label of this action
     * @return null
     */
    public function __construct($route, $labelTranslationKey, $isAvailableForNode = true) {
        $this->setRoute($route);
        $this->setLabelTranslationKey($labelTranslationKey);
        $this->isAvailableForNode = $isAvailableForNode;
    }

    /**
     * Set the route of this action
     * @param string $route
     * @return null
     * @throws zibo\ZiboException when invalid route provided
     */
    private function setRoute($route) {
        if (String::isEmpty($route)) {
            throw new ZiboException('Provided route is empty');
        }
        $this->route = $route;
    }

    /**
     * Get the route of this action
     * @return string
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Set the translation key of the label
     * @param string $labelTranslationKey
     * @return null
     * @throws zibo\ZiboException when invalid label provided
     */
    private function setLabelTranslationKey($labelTranslationKey) {
        if (String::isEmpty($labelTranslationKey)) {
            throw new ZiboException('Provided label is empty');
        }
        $this->labelTranslationKey = $labelTranslationKey;
    }

    /**
     * Get the label of this action
     * @param zibo\library\i18n\translation\Translator $translator
     * @param joppa\model\Node $node
     * @return string
     */
    public function getLabel(Translator $translator) {
        return $translator->translate($this->labelTranslationKey);
    }

    /**
     * Checks if this action is available for the node
     * @param joppa\model\Node $node
     * @return boolean true if available
     */
    public function isAvailableForNode(Node $node) {
        return $this->isAvailableForNode;
    }

    /**
     * Makes sure a node is set to this controller
     * @return null
     * @throws zibo\ZiboException when no node is set to this contorller
     */

    public function preAction() {
        parent::preAction();

        if (!$this->node) {
            throw new ZiboException('Can\'t perform action: No node set to this controller');
        }
    }

}