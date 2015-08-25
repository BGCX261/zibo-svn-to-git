<?php

namespace joppa\controller\backend;

use joppa\form\backend\NodeForm;
use joppa\form\backend\SiteForm;

use joppa\model\Site;
use joppa\model\SiteModel;

use joppa\view\backend\NodeFormView;

use zibo\admin\controller\LocalizeController;

use zibo\library\validation\exception\ValidationException;

/**
 * Controller to manage the sites
 */
class SiteController extends NodeTypeController {

    /**
     * Construct this controller
     * @return null
     */
    public function __construct() {
        parent::__construct(SiteModel::NODE_TYPE);
    }

    /**
     * Action to set an empty site form to the view
     * @return null
     */
    public function addAction() {
        $this->site = null;

        parent::addAction();
    }

	/**
     * Action to set the site form of an existing site to the view
     * @param int $id id of the site
     * @param string $locale locale code to edit this site in, if not specified the locale of the
     *                       LocalizeController will be used
     * @return null
	 */
	public function editAction($id, $locale = null) {
        if ($locale !== null) {
            LocalizeController::setLocale($locale);
            $this->response->setRedirect($this->request->getBasePath() . '/edit/' . $id);
            return;
        }

        $currentSite = $this->site;
        $currentNode = $this->site->node;

        $this->site = $this->getSite($id, 1, true);
        $this->node = $this->site->node;

        $form = $this->getForm();

        $this->site = $currentSite;
        $this->node = $currentNode;

        $view = $this->getFormView($form);
        $this->response->setView($view);
	}

	/**
	 * Action to save the site submitted through the site form
	 * @return null
	 */
	public function saveAction() {
        $form = $this->getForm();
        if (!$form->isSubmitted()) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        if ($form->isCancelled()) {
            $this->response->setRedirect($this->request->getBasePath());
        	return;
        }

        try {
        	$form->validate();

        	$site = $form->getSite();
        	$site->node->dataLocale = LocalizeController::getLocale();

        	$this->models[SiteModel::NAME]->save($site);

        	$this->site = $site;
        	$this->node = null;

            $this->clearCache();

        	$this->addInformation(self::TRANSLATION_MESSAGE_SAVED, array('object' => $this->site->node->name));
            $this->response->setRedirect($this->request->getBasePath());
        } catch (ValidationException $e) {
        	$form->setValidationException($e);

        	$view = $this->getFormView($form);
            $this->response->setView($view);
        }
	}

	/**
     * Action to delete a site
     * @param int $id id of the site which has to be removed
     * @return null
	 */
    public function deleteAction($id) {
    	$site = $this->getSite($id);
    	$this->models[SiteModel::NAME]->delete($site);

    	$this->site = null;
        $this->node = null;

    	$this->clearCache();

        $this->addInformation(self::TRANSLATION_MESSAGE_DELETED, array('object' => $site->node->name));
        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Action to copy a site
     * @param integer $id id of the site node which has to be copied
     * @return null
     */
    public function copyAction($id) {
        $site = $this->getSite($id, 1);

        $this->clearCache();

        $this->models[SiteModel::NAME]->copy($site);

        $this->addInformation(self::TRANSLATION_MESSAGE_COPIED, array('node' => $site->node->name));
        $this->response->setRedirect($this->getJoppaBaseUrl());
    }

    /**
     * Action to set a site as the default site
     * @param int $id id of the site which has to be set to default
     * @return null
     */
    public function defaultAction($id) {
    	$this->models[SiteModel::NAME]->setDefaultSite($id);
        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Get a site by the node's id
     * @param int $id id of the node
     * @param int $recursiveDepth
     * @return joppa\model\Site
     */
    private function getSite($id, $recursiveDepth = 1, $includeUnlocalized = null) {
        $locale = LocalizeController::getLocale();
        return $this->models[SiteModel::NAME]->getSite($id, $recursiveDepth, $locale, $includeUnlocalized);
    }

    /**
     * Get a site form
     * @return joppa\form\backend\NodeForm
     */
	protected function getForm() {
	    if ($this->site) {
	        $site = $this->site;
	    } else {
	        $site = $this->models[SiteModel::NAME]->createSite();
	    }

        return new SiteForm($this->request->getBasePath() . '/save', $site);
	}

	/**
     * Get the site form view
     * @param joppa\form\backend\SiteForm $form
     * @return joppa\view\backend\SiteFormView
	 */
    protected function getFormView(NodeForm $form) {
        return new NodeFormView($this->getSiteSelectForm(), $form, $this->site, $this->node, 'joppa/backend/site');
    }

}