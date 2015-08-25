<?php

namespace joppa\controller\backend\action;

use joppa\model\Node;

use zibo\core\Controller;

use zibo\library\i18n\translation\Translator;

/**
 * Interface of a node action
 */
interface NodeAction extends Controller {

    /**
     * Get the route of this action
     * @return string
     */
    public function getRoute();

    /**
     * Get the label of this action
     * @param zibo\library\i18n\translation\Translator $translator
     * @return string
     */
    public function getLabel(Translator $translator);

    /**
     * Checks if this action is available for the node
     * @param joppa\model\Node $node
     * @return boolean true if available
     */
    public function isAvailableForNode(Node $node);

    /**
     * Action method for the dispatcher
     * @return null
     */
    public function indexAction();

}