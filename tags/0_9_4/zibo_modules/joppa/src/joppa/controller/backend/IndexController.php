<?php

namespace joppa\controller\backend;

use joppa\form\SiteSelectForm;

use joppa\model\SiteModel;

use joppa\view\backend\BaseView;

/**
 * Controller to handler the site selection and show an empty page of the Joppa backend
 */
class IndexController extends BackendController {

	/**
	 * Handles the current site selection and will set a BaseView to the response
	 * @return null
	 */
    public function indexAction() {
        $arguments = func_get_args();
        if ($arguments) {
            $this->setError404();
            return;
        }

        $siteSelectForm = $this->getSiteSelectForm();
        if ($siteSelectForm->isSubmitted()) {
            $this->site = $siteSelectForm->getSite();
        }

        $this->node = null;

        $siteList = $siteSelectForm->getSiteList();
        if ($this->site == null && count($siteList) == 1) {
        	$siteIds = array_keys($siteList);
            $siteId = array_pop($siteIds);

            $this->site = $this->models[SiteModel::NAME]->createSite();
            $this->site->id = $siteId;

            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $view = new BaseView($siteSelectForm, $this->site, $this->node);
        $this->response->setView($view);
    }

}