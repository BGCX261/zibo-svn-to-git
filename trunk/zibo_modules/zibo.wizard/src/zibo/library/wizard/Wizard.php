<?php

namespace zibo\library\wizard;

use zibo\admin\message\Message;

use zibo\core\view\HtmlView;
use zibo\core\Request;
use zibo\core\Response;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\wizard\step\WizardStep;
use zibo\library\wizard\view\WizardView;
use zibo\library\String;
use zibo\library\Session;

use zibo\ZiboException;

/**
 * Wizard form
 */
abstract class Wizard extends Form {

    /**
     * Name of the previous button
     * @var string
     */
    const BUTTON_PREVIOUS = 'previous';

    /**
     * Name of the next button
     * @var string
     */
    const BUTTON_NEXT = 'next';

    /**
     * Name of the cancel button
     * @var string
     */
    const BUTTON_CANCEL = 'cancel';

    /**
     * Name of the finish button
     * @var string
     */
    const BUTTON_FINISH = 'finish';

    /**
     * Translation key for the previous button
     * @var string
     */
    const TRANSLATION_PREVIOUS = 'button.previous';

    /**
     * Translation key for the next button
     * @var string
     */
    const TRANSLATION_NEXT = 'button.next';

    /**
     * Translation key for the cancel button
     * @var string
     */
    const TRANSLATION_CANCEL = 'button.cancel';

    /**
     * Translation key for the finish button
     * @var string
     */
    const TRANSLATION_FINISH = 'button.finish';

    /**
     * Style class for a wizard form
     * @var string
     */
    const STYLE_WIZARD = 'wizard';

    /**
     * Name of the current step variable
     * @var string
     */
    const VARIABLE_CURRENT_STEP = 'step';

    /**
     * Array with the steps of this wizard
     * @var array
     */
    private $steps;

    /**
     * Name of the default step
     * @var string
     */
    private $defaultStep;

    /**
     * Name of the current step
     * @var string
     */
    private $currentStep;

    /**
     * Instance of the session
     * @var zibo\library\Session
     */
    private $session;

    /**
     * The request of the wizard
     * @var zibo\core\Request
     */
    private $request;

    /**
     * The response of the wizard
     * @var zibo\core\Response
     */
    private $response;

    /**
     * URL to redirect to when the cancel button has been clicked
     * @var string
     */
    private $cancelUrl;

    /**
     * Construct a new wizard form
     * @param string $action URL where the form will point to
     * @param string $name Name of the form
     * @return null
     */
    public function __construct($action, $name) {
        parent::__construct($action, $name);

        $this->appendToClass(self::STYLE_WIZARD);

        $fieldFactory = FieldFactory::getInstance();

        $previousButton = $fieldFactory->createSubmitField(self::BUTTON_PREVIOUS, self::TRANSLATION_PREVIOUS);
        $nextButton = $fieldFactory->createSubmitField(self::BUTTON_NEXT, self::TRANSLATION_NEXT);
        $cancelButton = $fieldFactory->createSubmitField(self::BUTTON_CANCEL, self::TRANSLATION_CANCEL);
        $finishButton = $fieldFactory->createSubmitField(self::BUTTON_FINISH, self::TRANSLATION_FINISH);

        $this->addField($previousButton);
        $this->addField($nextButton);
        $this->addField($cancelButton);
        $this->addField($finishButton);

        $this->steps = array();
        $this->session = Session::getInstance();
    }

    /**
     * Adds a step to this wizard
     * @param string $name Name of the step
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or not a string
     */
    protected function addStep($name, WizardStep $step) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        $step->setWizard($this);

        $this->steps[$name] = $step;

        if (!$this->defaultStep) {
            $this->defaultStep = $name;
            $this->currentStep = $name;
        }
    }

    /**
     * Removes a step from this wizard
     * @param string $name Name of the step to remove
     * @return null
     * @throws zibo\ZiboException when the step could not be removed
     */
    protected function removeStep($name) {
        if (!$this->hasStep($name)) {
            throw new ZiboException('Cannot remove the step, provided name is empty');
        }

        unset($this->steps[$name]);
    }

    /**
     * Checks if a step exists
     * @return boolean True when the step exists, false otherwise
     * @throws zibo\ZiboException when the provided name is empty or not a string
     */
    protected function hasStep($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Provided name is empty');
        }

        return array_key_exists($name, $this->steps);
    }

    /**
     * Gets the instance of a step
     * @param string $name Name of the step
     * @return zibo\library\wizard\step\WizardStep
     * @throws zibo\ZiboException when the provided step does not exist in this wizard
     */
    protected function getStep($name) {
        if (!$this->hasStep($name)) {
            throw new ZiboException('Step ' . $name . ' does not exist');
        }

        return $this->steps[$name];
    }

    /**
     * Sets the name of the default step
     * @param string $name Name of the default step
     * @return null
     * @throws zibo\ZiboException when the provided step does not exist in this wizard
     */
    protected function setDefaultStep($name) {
        if (!$this->hasStep($name)) {
            throw new ZiboException('Cannot set ' . $name . ' as default. Step does not exist');
        }

        $this->defaultStep = $name;
    }

    /**
     * Gets the name of the default step
     * @return string
     */
    public function getDefaultStep() {
        return $this->defaultStep;
    }

    /**
     * Sets the current step
     * @param string $name Name of the current step
     * @return null
     * @throws zibo\ZiboException when the provided step does not exist in this wizard
     */
    protected function setCurrentStep($name) {
        if (!$this->hasStep($name)) {
            throw new ZiboException('Cannot set ' . $name . ' as the current step. Step does not exist');
        }

        $this->currentStep = $name;
        $this->setVariable(self::VARIABLE_CURRENT_STEP, $name);
    }

    /**
     * Gets the the current step
     * @return string Name of the current step
     */
    public function getCurrentStep() {
        return $this->currentStep;
    }

    /**
     * Sets the URL to redirect to when the cancel button has been pressed
     * @param string $cancelUrl URL
     * @return null
     */
    public function setCancelUrl($cancelUrl) {
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * Gets the URL to redirect to when the cancel button has been pressed
     * @return string $cancelUrl URL
     */
    public function getCancelUrl() {
        return $this->cancelUrl;
    }

    /**
     * Gets the request of the wizard
     * @return zibo\core\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Gets the response of the wizard
     * @return zibo\core\Response
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Invokes the wizard
     * @param zibo\core\Request $request The request
     * @param zibo\core\Response $response The response
     * @return null
     * @throws zibo\ZiboException when there are no steps in this wizard
     */
    public function invoke(Request $request, Response $response) {
        if (!$this->steps) {
            throw new ZiboException('This wizard does not contain any steps');
        }

        $this->request = $request;
        $this->response = $response;
        $this->currentStep = $this->getVariable(self::VARIABLE_CURRENT_STEP, $this->defaultStep);

        $step = $this->steps[$this->currentStep];

        $step->prepareForm();

        if ($this->isSubmitted()) {
            if ($this->getValue(self::BUTTON_CANCEL)) {
                $this->reset();
                if ($this->cancelUrl) {
                    $this->response->setRedirect($this->cancelUrl);
                } else {
                    $this->response->setRedirect($this->request->getBaseUrl());
                }
                return;
            }

            $nextStep = null;

            if ($this->getValue(self::BUTTON_PREVIOUS)) {
                $nextStep = $step->previous();
            } elseif ($this->getValue(self::BUTTON_NEXT)) {
                $nextStep = $step->next();
            } elseif ($this->getValue(self::BUTTON_FINISH)) {
                $nextStep = $step->finish();
            }

            if ($nextStep) {
                if (!$this->hasStep($nextStep)) {
                    throw new ZiboException('Cannot set the next step, invalid return value of step ' . $this->currentStep);
                }

                $this->setVariable(self::VARIABLE_CURRENT_STEP, $nextStep);
                $response->setRedirect($this->action);
            }

            if ($response->willRedirect()) {
                return;
            }
        }

        if (!$step->hasPrevious()) {
            $this->setIsDisabled(true, self::BUTTON_PREVIOUS);
        }
        if (!$step->hasNext()) {
            $this->setIsDisabled(true, self::BUTTON_NEXT);
        }
        if (!$step->hasFinish()) {
            $this->setIsDisabled(true, self::BUTTON_FINISH);
        }
        if (!$step->hasCancel()) {
            $this->setIsDisabled(true, self::BUTTON_CANCEL);
        }

        $view = $this->getView($step);
        if ($response->willRedirect()) {
            return;
        }

        $response->setView($view);
    }

    /**
     * Gets the view for the wizard
     * @param zibo\library\wizard\step\WizardStep $step The current step
     * @return zibo\core\View
     */
    protected function getView(WizardStep $step) {
        $stepView = $step->getView();

        if ($stepView === null || $stepView instanceof HtmlView) {
            return new WizardView($this, $stepView);
        } else {
            return $stepView;
        }
    }

    /**
     * Sets a variable for this wizard
     * @param string $name Name of the variable
     * @param mixed $value Value of the variable, null to clear the variable
     * @return null
     */
    public function setVariable($name, $value = null) {
        $variables = $this->session->get($this->name, array());

        $variables[$name] = $value;

        $this->session->set($this->name, $variables);
    }

    /**
     * Gets a variable of this wizard
     * @param string $name Name of the variable
     * @param mixed $default Default value for the variable
     * @return mixed Value of the variable if it's set, the default value otherwise
     */
    public function getVariable($name, $default = null) {
        $variables = $this->session->get($this->name, array());

        if (array_key_exists($name, $variables)) {
            return $variables[$name];
        }

        return $default;
    }

    /**
     * Resets this wizard
     * @return null
     */
    public function reset() {
        $this->session->set($this->name, array());
    }

    /**
     * Add a localized information message to the response
     * @param string $translationKey translation key of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    public function addInformation($translationKey, array $vars = null) {
        $this->addMessage($translationKey, Message::TYPE_INFORMATION, $vars);
    }

    /**
     * Add a localized error message to the response
     * @param string $translationKey translation key of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    public function addError($translationKey, array $vars = null) {
        $this->addMessage($translationKey, Message::TYPE_ERROR, $vars);
    }

    /**
     * Add a localized warning message to the response
     * @param string $translationKey translation key of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    public function addWarning($translationKey, array $vars = null) {
        $this->addMessage($translationKey, Message::TYPE_WARNING, $vars);
    }

    /**
     * Add a localized message to the response
     * @param string $translationKey translation key of the message
     * @param string $type type of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    private function addMessage($translationKey, $type, $vars) {
        $message = new Message($translationKey, $type, $vars);
        $this->response->addMessage($message);
    }

}