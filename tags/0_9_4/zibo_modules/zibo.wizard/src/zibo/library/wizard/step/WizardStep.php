<?php

namespace zibo\library\wizard\step;

use zibo\library\wizard\Wizard;

/**
 * Interface of a step for a wizard form
 * @see zibo\library\wizard\Wizard
 */
interface WizardStep {

    /**
     * Sets the wizard to the step
     * @param zibo\library\wizard\Wizard $wizard
     * @return null
     */
    public function setWizard(Wizard $wizard);

    /**
     * Prepares the form of the wizard, hook to add fields for your step
     * @return null
     */
    public function prepareForm();

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView();

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious();

    /**
     * Performs the previous action of this step
     * @return string Name of the step to go to after this action
     */
    public function previous();

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasNext();

    /**
     * Performs the next action of this step
     * @return string Name of the step to go to after this action
     */
    public function next();

    /**
     * Gets whether this step has a finish step
     * @return boolean
     */
    public function hasFinish();

    /**
     * Performs the finish action of this step
     * @return string Name of the step to go to after this action
     */
    public function finish();

    /**
     * Gets whether this step can be cancelled
     * @return boolean
     */
    public function hasCancel();

}