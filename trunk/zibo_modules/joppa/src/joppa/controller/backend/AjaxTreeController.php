<?php

namespace joppa\controller\backend;

use zibo\admin\controller\AbstractController;

use zibo\library\Session;

use zibo\ZiboException;

/**
 * Controller to get and set the locale of the localized content
 */
class AjaxTreeController extends AbstractController {

    /**
     * Name of the session key for the locale of the localized content
     * @var string
     */
    const SESSION_HIDDEN_NODES = 'joppa.tree.nodes';

    /**
     * Action to change the locale of the localized content
     * @param string $locale Code of the locale, if not specified, the LocalizePanelForm should be submitted
     * @return null
     */
    public function indexAction($nodeId) {
        self::toggleNode($nodeId);
    }

    /**
     * Toggle the collapse state of the provided node
     * @param integer $nodeId Id of the node
     * @return null
     */
    public static function toggleNode($nodeId) {
    	$session = Session::getInstance();

    	$nodes = $session->get(self::SESSION_HIDDEN_NODES, array());

    	if (array_key_exists($nodeId, $nodes)) {
    		unset($nodes[$nodeId]);
    	} else {
    		$nodes[$nodeId] = true;
    	}

        $session->set(self::SESSION_HIDDEN_NODES, $nodes);
    }

    /**
     * Checks if the provided node is collapsed in the tree
     * @param integer $nodeId Id of the node
     * @return boolean True if the node is collapsed, false if the node is expanded
     */
    public static function isNodeCollapsed($nodeId) {
    	$session = Session::getInstance();

    	$nodes = $session->get(self::SESSION_HIDDEN_NODES, array());

    	if (array_key_exists($nodeId, $nodes)) {
    		return true;
    	}

    	return false;
    }

}