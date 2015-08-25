<?php

namespace joppa\controller\backend;

use joppa\form\backend\SiteSelectForm;

use joppa\model\NodeModel;
use joppa\model\NodeSettingModel;
use joppa\model\SiteModel;
use joppa\model\WidgetModel;

use joppa\view\backend\BaseView;

use joppa\Module;

use zibo\admin\controller\AbstractController;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\Session;

use zibo\ZiboException;

/**
 * Abstract controller for the Joppa backend
 */
class BackendController extends AbstractController {

    /**
     * Session key for the selected node, the value is the node id
     * @var int
     */
	const SESSION_NODE = 'joppa.node';

	/**
     * Session key for the selected site, the value is the site id
     * @var int
     */
	const SESSION_SITE = 'joppa.site';

    /**
     * Session key for the last action type
     * @var string
     */
    const SESSION_LAST_ACTION = 'joppa.action.last';

	/**
	 * Hook variable for the orm module to set the defined models to this controller
	 * @var array
	 */
	public $useModels = array(
        SiteModel::NAME,
        NodeModel::NAME,
        NodeSettingModel::NAME,
        WidgetModel::NAME
    );

	/**
	 * The selected node
	 * @var joppa\model\Node
	 */
	protected $node;

	/**
	 * The selected site
	 * @var joppa\model\Site
	 */
	protected $site;

	/**
	 * The session instance for easy access
	 * @var zibo\library\Session
	 */
	protected $session;

	/**
	 * Sets the Session instance to this controller, reads the current site and node from the session.
	 * @return null
	 */
	public function preAction() {
		$this->session = $this->getSession();

		$siteId = $this->session->get(self::SESSION_SITE);
		if ($siteId) {
            try {
                $this->site = $this->models[SiteModel::NAME]->getSite($siteId);
            } catch (ZiboException $e) {
            	$this->site = null;
            }
		}

		$nodeId = $this->session->get(self::SESSION_NODE);
		if ($nodeId) {
			try {
                $this->node = $this->models[NodeModel::NAME]->getNode($nodeId);
			} catch (ZiboException $e) {
				$this->node = null;
			}
		}
	}

    /**
     * Stores the current site and node to the session
     * @return null
     */
	public function postAction() {
        if ($this->site) {
            $this->session->set(self::SESSION_SITE, $this->site->id);
        } else {
            $this->session->set(self::SESSION_SITE);
        }

        if ($this->node) {
            $this->session->set(self::SESSION_NODE, $this->node->id);
        } else {
            $this->session->set(self::SESSION_NODE);
        }
	}

	/**
	 * Sets an Error404View to the response
	 * @return null
	 */
    public function indexAction() {
        $this->response->setRedirect($this->getJoppaBaseUrl());
    }

    /**
     * Get the form to select the current site
     * @return joppa\form\SiteSelectForm
     */
    protected function getSiteSelectForm() {
        return new SiteSelectForm($this->getJoppaBaseUrl(), $this->site);
    }

    /**
     * Get the base url of the Joppa backend
     * @return string
     */
    protected function getJoppaBaseUrl() {
        return $this->request->getBaseUrl() . Request::QUERY_SEPARATOR . Module::ROUTE_JOPPA;
    }

    /**
     * Run the event to clear the Joppa cache
     * @return null
     */
    protected function clearCache() {
        Zibo::getInstance()->runEvent(Module::EVENT_CLEAR_JOPPA_CACHE);
    }

}