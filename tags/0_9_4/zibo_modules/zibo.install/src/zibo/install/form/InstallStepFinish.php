<?php

namespace zibo\install\form;

use zibo\core\Zibo;

use zibo\install\view\InstallStepFinishView;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\validation\exception\ValidationException;
use zibo\library\wizard\step\AbstractWizardStep;

/**
 * Step 2 of the Zibo installation
 */
class InstallStepFinish extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'stepFinish';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        return new InstallStepFinishView();
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {

    }

    /**
     * Finished the installation, remove the installation module and redirect
     * @return null
     */
    public function finish() {
        $installModule = new File(__DIR__ . '/../../../../');
        $installModule->delete();

        $installScript = new File($installModule->getParent()->getParent(), 'install.php');
        if ($installScript->exists() && $installScript->isWritable()) {
            $installScript->delete();
        }

        $zibo = Zibo::getInstance();
        $request = $zibo->getRequest();
        $response = $zibo->getResponse();

        $response->setRedirect($request->getBaseUrl());
    }

    /**
     * Checks if this step has a next step
     * @return boolean
     */
    public function hasNext() {
        return false;
    }

    /**
     * Checks if this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        return false;
    }

    /**
     * Checks if this step has a previous step
     * @return boolean
     */
    public function hasFinish() {
        return true;
    }

    /**
     * Checks if this step has a previous step
     * @return boolean
     */
    public function hasCancel() {
        return false;
    }

}