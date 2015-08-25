<?php

namespace zibo\library\wizard\step;

use zibo\library\wizard\Wizard;

/**
 * Abstract implementation of a wizard step
 */
abstract class AbstractWizardStep implements WizardStep {

    /**
     * The wizard form which contains this step
     * @var zibo\library\html\form\wizard\WizardForm
     */
    protected $wizard;

    /**
     * Sets the wizard to the step
     * @param zibo\library\wizard\Wizard $wizard
     * @return null
     */
    public function setWizard(Wizard $wizard) {
        $this->wizard = $wizard;
    }

    /**
     * Prepares the form of the wizard, hook to add field for your step
     * @return null
     */
    public function prepareForm() {

    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        return true;
    }

    /**
     * Performs the previous action of this step
     * @return string Name of the step to go to after this action
     */
    public function previous() {
        return null;
    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasNext() {
        return true;
    }

    /**
     * Performs the next action of this step
     * @return string Name of the step to go to after this action
     */
    public function next() {
        return null;
    }

    /**
     * Gets whether this step has a finish step
     * @return boolean
     */
    public function hasFinish() {
        return false;
    }

    /**
     * Performs the finish action of this step
     * @return string Name of the step to go to after this action
     */
    public function finish() {
        return null;
    }

    /**
     * Gets whether this step can be cancelled
     * @return boolean
     */
    public function hasCancel() {
        return true;
    }

}