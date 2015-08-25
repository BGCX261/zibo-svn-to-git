<?php

namespace zibo\install\form;

use zibo\database\admin\model\ConnectionModel;

use zibo\install\view\InstallStepSecurityView;

use zibo\library\database\Dsn;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RequiredValidator;
use zibo\library\wizard\step\AbstractWizardStep;

use zibo\orm\security\model\RoleModel;
use zibo\orm\security\model\UserModel;

use \Exception;

/**
 * Step 1 of the Zibo installation: security
 */
class InstallStepSecurity extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'stepSecurity';

    /**
     * Name of the variable to keep the user
     * @var string
     */
    const VAR_USER = 'user';

    /**
     * Name of the username field
     * @var string
     */
    const FIELD_USERNAME = 'username';

    /**
     * Name of the password field
     * @var string
     */
    const FIELD_PASSWORD = 'password';

    /**
     * Name of the emailaddress field
     * @var string
     */
    const FIELD_EMAIL = 'email';

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        return new InstallStepSecurityView($this->wizard);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $username = null;
        $email = null;

        $user = $this->wizard->getVariable(self::VAR_USER);
        if ($user) {
            $username = $user->getUserName();
            $email = $dsn->getUserEmail();
        }

        $fieldFactory = FieldFactory::getInstance();
        $translator = I18n::getInstance()->getTranslator();
        $requiredValidator = new RequiredValidator();

        $usernameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_USERNAME, $username);
        $usernameField->addValidator($requiredValidator);

        $passwordField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD);
        $passwordField->addValidator($requiredValidator);

        $emailField = $fieldFactory->createField(FieldFactory::TYPE_EMAIL, self::FIELD_EMAIL, $email);
        $emailField->addValidator($requiredValidator);

        $this->wizard->addField($usernameField);
        $this->wizard->addField($passwordField);
        $this->wizard->addField($emailField);
    }

    /**
     * Processes the next action of this step
     * return string Name of the next step
     */
    public function next() {
        try {
            $this->wizard->validate();
        } catch (ValidationException $validationException) {
            return null;
        }

        try {
            $username = $this->wizard->getValue(self::FIELD_USERNAME);
            $password = $this->wizard->getValue(self::FIELD_PASSWORD);
            $email = $this->wizard->getValue(self::FIELD_EMAIL);

            $modelManager = ModelManager::getInstance();

            $roleModel = $modelManager->getModel(RoleModel::NAME);
            $role = $roleModel->findById(1);
            if (!$role) {
                $role = $roleModel->createData();
                $role->name = 'Developer';
                $role->isSuperRole = true;

                $roleModel->save($role);
            }

            $userModel = $modelManager->getModel(UserModel::NAME);
            $user = $userModel->findById(1);
            if (!$user) {
                $user = $userModel->createData();
            }

            $user->setUserName($username);
            $user->setUserPassword($password);
            $user->setIsUserActive(true);

            $user->roles = array($role->id => $role->id);

            $userModel->save($user);

            $this->wizard->setVariable(self::VAR_USER, $user);

            $deniedRoutes = array(
                'admin/*',
            );

            $sm = SecurityManager::getInstance();
            $sm->setDeniedRoutes($deniedRoutes);
        } catch (ValidationException $validationException) {
            $this->wizard->setValidationException($validationException);
            return null;
        }

        return $this->wizard->getNextStep();
    }

    /**
     * Processes the previous action of this step
     * return string Name of the next step
     */
    public function previous() {
        return $this->wizard->getPreviousStep();
    }

    /**
     * Gets whether this step has a previous step
     * @return boolean
     */
    public function hasPrevious() {
        return true;
    }

}