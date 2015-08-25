<?php

namespace joppa\controller\backend\action;

use joppa\form\backend\NodeSettingsForm;

use joppa\view\backend\NodeAdvancedView;

use zibo\library\validation\exception\ValidationException;

/**
 * Controller of the advanced node action
 */
class NodeAdvancedAction extends AbstractNodeAction {

    /**
     * Route of this action
     * @var string
     */
    const ROUTE = 'advanced';

    /**
     * Translation key of the label
     * @var string
     */
    const TRANSLATION_LABEL = 'joppa.button.advanced';

    /**
     * Translation key for the saved message
     * @var string
     */
    const TRANSLATION_INFORMATION_SAVED = 'joppa.information.advanced.saved';

    /**
     * Construct this node action
     * @return null
     */
    public function __construct() {
        parent::__construct(self::ROUTE, self::TRANSLATION_LABEL, true);
    }

    /**
     * Perform the advanced node action
     */
    public function indexAction() {
        $nodeSettings = $this->models['NodeSetting']->getNodeSettings($this->node->id);

        $form = new NodeSettingsForm($this->request->getBasePath(), $nodeSettings);
        if (!$form->isSubmitted()) {
            $this->setAdvancedView($form);
            return;
        }

        if ($form->isCancelled()) {
        	$this->response->setRedirect($this->request->getBasePath());
        	return;
        }

        try {
            $nodeSettings = $form->getNodeSettings(true);
            $this->models['NodeSetting']->setNodeSettings($nodeSettings);

            $this->clearCache();

            $this->addInformation(self::TRANSLATION_INFORMATION_SAVED, array('name' => $this->node->name));
            $this->response->setRedirect($this->request->getBasePath());
        } catch (ValidationException $saveException) {
            $validationException = new ValidationException();

            $errors = $saveException->getAllErrors();
            foreach ($errors as $fieldErrors) {
                $validationException->addErrors(NodeSettingsForm::FIELD_SETTINGS, $fieldErrors);
            }

        	$form->setValidationException($validationException);

        	$this->setAdvancedView($form);
        }
    }

    /**
     * Set the node advanced view to the response
     * @param joppa\form\NodeSettingsForm $form
     * @return null
     */
    private function setAdvancedView(NodeSettingsForm $form) {
        $view = new NodeAdvancedView($this->getSiteSelectForm(), $form, $this->site, $this->node);
        $this->response->setView($view);
    }

}