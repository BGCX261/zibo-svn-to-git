<?php

namespace joppa\controller\backend;

use joppa\form\backend\NodeForm;

use joppa\model\NodeSettingModel;
use joppa\model\NodeSettings;
use joppa\model\NodeModel;
use joppa\model\Node;

use joppa\view\backend\NodeFormView;

use zibo\admin\controller\LocalizeController;

use zibo\library\validation\exception\ValidationException;

/**
 * Controller to manage the de data of a node
 */
class NodeTypeController extends BackendController {

    /**
     * Translation key for a generic saved information message. Variable %object% is required for this translation
     * @var string
     */
    const TRANSLATION_MESSAGE_SAVED = 'message.object.saved';

    /**
     * Translation key for a generic deleted information message. Variable %object% is required for this translation
     * @var string
     */
    const TRANSLATION_MESSAGE_DELETED = 'message.object.deleted';

    /**
     * Translation key for a node copied information message. Variable %node% is required for this translation
     * @var string
     */
    const TRANSLATION_MESSAGE_COPIED = 'joppa.information.node.copied';

    /**
     * The name of the type for which this controller acts
     * @var string
     */
    protected $type;

    /**
     * Set the type for which this controller acts
     * @param string $type name of the type
     * @return null;
     */
    public function __construct($type) {
        $this->type = $type;
    }

    /**
     * Action to set an empty node form to the view
     * @return null
     */
	public function addAction() {
	    $this->node = null;

        $form = $this->getForm();

        $view = $this->getFormView($form);
        $this->response->setView($view);
	}

    /**
     * Action to set the node form of an existing node to the view
     * @param int $id id of the node
     * @param string $locale locale code to edit this node in, if not specified the locale of the
     *                       LocalizeController will be used
     * @return null
     */
	public function editAction($id, $locale = null) {
        if ($locale !== null) {
            LocalizeController::setLocale($locale);
            $this->response->setRedirect($this->request->getBasePath() . '/edit/' . $id);
            return;
        }

        $currentNode = $this->node;

        $this->node = $this->getNode($id, true);

        $this->getSession()->set(self::SESSION_LAST_ACTION, 'edit');

        $form = $this->getForm();

        $this->node = $currentNode;

        $view = $this->getFormView($form);
        $this->response->setView($view);
	}

    /**
     * Action to save the node submitted through the node form
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
        	$node = $form->getNode();
        	$node->type = $this->type;
        	$node->dataLocale = LocalizeController::getLocale();

        	$this->node = $this->models[NodeModel::NAME]->save($node);

        	$this->clearCache();

        	$this->addInformation(self::TRANSLATION_MESSAGE_SAVED, array('object' => $node->name));
            $this->response->setRedirect($this->getJoppaBaseUrl());
        } catch (ValidationException $e) {
        	$form->setValidationException($e);

        	$view = $this->getFormView($form);
            $this->response->setView($view);
        }
	}

    /**
     * Action to delete a node
     * @param int $id id of the node which has to be removed
     * @return null
     */
    public function deleteAction($id) {
    	$node = $this->getNode($id);

    	$this->models[NodeModel::NAME]->delete($node);

    	if ($this->node->id == $node->id) {
            $this->node = null;
    	}

        $this->clearCache();

        $this->addInformation(self::TRANSLATION_MESSAGE_DELETED, array('object' => $node->name));
        $this->response->setRedirect($this->getJoppaBaseUrl());
    }

    /**
     * Action to copy a node
     * @param integer $id id of the node which has to be copied
     * @return null
     */
    public function copyAction($id) {
    	$this->models[NodeModel::NAME]->copy($id);

    	$this->clearCache();

    	$node = $this->getNode($id);

        $this->addInformation(self::TRANSLATION_MESSAGE_COPIED, array('node' => $node->name));
        $this->response->setRedirect($this->getJoppaBaseUrl());
    }

    /**
     * Get a node by it's id
     * @param int $id id of the node
     * @return joppa\model\Node
     */
    protected function getNode($id, $includeUnlocalized = null) {
        $locale = LocalizeController::getLocale();
        return $this->models[NodeModel::NAME]->getNode($id, 1, $locale, $includeUnlocalized);
    }

    /**
     * Get a node form
     * @return joppa\form\backend\NodeForm
     */
	protected function getForm() {
		if ($this->node) {
		    $node = $this->node;
		} else {
		    $node = $this->models[NodeModel::NAME]->createNode($this->type, $this->site->node);
		}

        return new NodeForm($this->request->getBasePath() . '/save', $this->site->node, $node);
	}

    /**
     * Get the node form view
     * @param joppa\form\backend\NodeForm $form
     * @return joppa\view\backend\NodeFormView
     */
	protected function getFormView(NodeForm $form) {
        return new NodeFormView($this->getSiteSelectForm(), $form, $this->site, $this->node);
	}

}