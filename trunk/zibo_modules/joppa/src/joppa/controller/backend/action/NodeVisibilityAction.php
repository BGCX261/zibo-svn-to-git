<?php

namespace joppa\controller\backend\action;

use joppa\form\backend\NodeVisibilityForm;

use joppa\model\NodeSettingModel;

use joppa\view\backend\NodeVisibilityView;

use zibo\library\validation\exception\ValidationException;

/**
 * Controller of the visibility node action
 */
class NodeVisibilityAction extends AbstractNodeAction {

    /**
     * Route of this action
     * @var string
     */
    const ROUTE = 'visibility';

    /**
     * Translation key of the label
     * @var string
     */
    const TRANSLATION_LABEL = 'joppa.button.visibility';

    /**
     * Translation key for the saved message
     * @var string
     */
    const TRANSLATION_INFORMATION_SAVED = 'joppa.message.visibility.saved';

    /**
     * Construct this node action
     * @return null
     */
    public function __construct() {
        parent::__construct(self::ROUTE, self::TRANSLATION_LABEL, true);
    }

    /**
     * Perform the visibility node action
     */
    public function indexAction() {
        $nodeSettings = $this->models[NodeSettingModel::NAME]->getNodeSettings($this->node->id);

        $form = new NodeVisibilityForm($this->request->getBasePath(), $nodeSettings);
        if (!$form->isSubmitted()) {
            $this->setVisibilityView($form);
            return;
        }

        if (!$form->getValue(NodeVisibilityForm::FIELD_SAVE)) {
        	$this->response->setRedirect($this->request->getBasePath());
        	return;
        }

        try {
            $nodeSettings = $form->getNodeSettings(true);
            $this->models[NodeSettingModel::NAME]->setNodeSettings($nodeSettings);

            $this->clearCache();

            $this->addInformation(self::TRANSLATION_INFORMATION_SAVED, array('name' => $this->node->name));
            $this->response->setRedirect($this->request->getBasePath());
        } catch (ValidationException $saveException) {
            $validationException = new ValidationException();

            $errors = $saveException->getAllErrors();
            foreach ($errors as $field => $fieldErrors) {
                if ($field == NodeSettingModel::SETTING_PUBLISH) {
                    $validationException->addErrors(NodeVisibilityForm::FIELD_PUBLISH, $fieldErrors);
                } elseif ($field == NodeSettingModel::SETTING_PUBLISH_START) {
                    $validationException->addErrors(NodeVisibilityForm::FIELD_PUBLISH_START, $fieldErrors);
                } elseif ($field == NodeSettingModel::SETTING_PUBLISH_STOP) {
                    $validationException->addErrors(NodeVisibilityForm::FIELD_PUBLISH_STOP, $fieldErrors);
                } else {
                    $validationException->addErrors($field, $fieldErrors);
                }
            }

            $form->setValidationException($validationException);

        	$this->setVisibilityView($form);
        }
    }

    /**
     * Set the node visibility view to the response
     * @param joppa\form\backend\NodeVisibilityForm $form
     * @return null
     */
    private function setVisibilityView(NodeVisibilityForm $form) {
        $view = new NodeVisibilityView($this->getSiteSelectForm(), $form, $this->site, $this->node);
        $this->response->setView($view);
    }

}