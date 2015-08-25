<?php

namespace zibo\orm\builder\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\view\HtmlView;

use zibo\orm\builder\view\wizard\BuilderWizardView;
use zibo\orm\builder\view\wizard\SidebarView;
use zibo\orm\builder\wizard\BuilderWizard;
use zibo\orm\Module;

/**
 * Controller of the model wizard
 */
class WizardController extends AbstractController {

    /**
     * Action to reset the wizard
     * @var string
     */
    const ACTION_RESET = 'reset';

    /**
     * Action to reset the wizard to a specified model
     * @var string
     */
    const ACTION_MODEL = 'model';

    /**
     * Action to limit the wizard
     * @var string
     */
    const ACTION_LIMIT = 'limit';

    /**
     * Action to limit the wizard to a field of the model
     * @var string
     */
    const ACTION_FIELD = 'field';

    /**
     * Translation key for the information message of the wizard
     * @var string
     */
    const TRANSLATION_INFORMATION = 'orm.label.wizard.description';

    /**
     * Action to invoke the model wizard
     * @param string $action Name of the model to reset the wizard with, 'reset' to restart the wizard
     * @return null
     */
    public function indexAction($action = null, $actionArgument = null, $modelAction = null, $modelActionArgument = null) {
        $modelName = null;
        $fieldName = null;
        if ($action == self::ACTION_MODEL) {
            $modelName = $actionArgument;
            if ($modelAction == self::ACTION_FIELD) {
                if (!$modelActionArgument) {
                    $fieldName = true;
                } else {
                    $fieldName = $modelActionArgument;
                }
            }
        }

        $wizard = new BuilderWizard($this->request->getBasePath(), $modelName);
        if ($fieldName !== null) {
            $wizard->limitToField($fieldName);
        } elseif ($modelAction == self::ACTION_LIMIT) {
            switch ($modelActionArgument) {
                case BuilderWizard::LIMIT_MODEL:
                    $wizard->limitToModel();
                    break;
                case BuilderWizard::LIMIT_DATA_FORMAT:
                    $wizard->limitToDataFormats();
                    break;
                case BuilderWizard::LIMIT_INDEX:
                    $wizard->limitToIndex();
                    break;
            }
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $cancelUrl = $this->request->getBaseUrl() . '/' . Module::ROUTE_ADMIN;
        if (!$wizard->isNewModel()) {
            $modelTable = $wizard->getModelTable();
            $cancelUrl .= '/' . BuilderController::ACTION_DETAIL . '/' . $modelTable->getName();
        }
        $wizard->setCancelUrl($cancelUrl);

        if ($action == self::ACTION_RESET) {
            $wizard->reset();
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $wizard->invoke($this->request, $this->response);

        $view = $this->response->getView();
        if ($view instanceof HtmlView) {
            $view = new BuilderWizardView($view);

            $sidebar = $view->getSidebar();
            $sidebar->addPanel(new SidebarView($wizard));
            $sidebar->setInformation(self::TRANSLATION_INFORMATION, true);
        }

        $this->response->setView($view);
    }

}