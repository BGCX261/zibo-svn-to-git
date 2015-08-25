<?php

namespace zibo\install\form;

use zibo\database\admin\model\ConnectionModel;

use zibo\install\view\InstallStepDatabaseView;

use zibo\library\database\Dsn;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\i18n\I18n;
use zibo\library\orm\ModelManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\RequiredValidator;
use zibo\library\validation\ValidationError;
use zibo\library\wizard\step\AbstractWizardStep;

use \Exception;

/**
 * Step 1 of the Zibo installation: database information
 */
class InstallStepDatabase extends AbstractWizardStep {

    /**
     * Name of this step
     * @var string
     */
    const NAME = 'stepDatabase';

    /**
     * Name of the variable for the DSN
     * @var string
     */
    const VAR_DSN = 'dsn';

    /**
     * Name of the profile field
     * @var string
     */
    const FIELD_PROTOCOL = 'protocol';

    /**
     * Name of the server field
     * @var string
     */
    const FIELD_SERVER = 'server';

    /**
     * Name of the port field
     * @var string
     */
    const FIELD_PORT = 'port';

    /**
     * Name of the database field
     * @var string
     */
    const FIELD_DATABASE = 'database';

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
     * Translation key for the error when the database connection could not be made
     * @var string
     */
    const TRANSLATION_DATABASE_ERROR = 'install.error.database.connect';

    /**
     * If an exception occured when processing the form, it will be stored in this variable
     * @var Exception
     */
    private $exception;

    /**
     * Gets the view of this step
     * @return zibo\core\View
     */
    public function getView() {
        return new InstallStepDatabaseView($this->wizard);
    }

    /**
     * Prepares the wizard form for this step
     * @return null
     */
    public function prepareForm() {
        $protocol = null;
        $server = null;
        $port = null;
        $database = null;
        $username = null;
        $password = null;

        $dsn = $this->wizard->getVariable(self::VAR_DSN);
        if ($dsn) {
            $protocol = $dsn->getProtocol();
            $server = $dsn->getServer();
            $port = $dsn->getPort();
            $database = $dsn->getDatabase();
            $username = $dsn->getUsername();
        }

        $fieldFactory = FieldFactory::getInstance();
        $translator = I18n::getInstance()->getTranslator();
        $requiredValidator = new RequiredValidator();

        $connectionModel = new ConnectionModel();
        $protocols = $connectionModel->getProtocols();

        $protocolField = $fieldFactory->createField(FieldFactory::TYPE_LIST, self::FIELD_PROTOCOL, $protocol);
        $protocolField->setOptions($protocols);

        $serverField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_SERVER, $server);
        $serverField->addValidator($requiredValidator);

        $portField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_PORT, $port);

        $databaseField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_DATABASE, $database);
        $databaseField->addValidator($requiredValidator);

        $usernameField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_USERNAME, $username);

        $passwordField = $fieldFactory->createField(FieldFactory::TYPE_PASSWORD, self::FIELD_PASSWORD);

        $this->wizard->addField($protocolField);
        $this->wizard->addField($serverField);
        $this->wizard->addField($portField);
        $this->wizard->addField($databaseField);
        $this->wizard->addField($usernameField);
        $this->wizard->addField($passwordField);
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
            $protocol = $this->wizard->getValue(self::FIELD_PROTOCOL);
            $server = $this->wizard->getValue(self::FIELD_SERVER);
            $port = $this->wizard->getValue(self::FIELD_PORT);
            $database = $this->wizard->getValue(self::FIELD_DATABASE);
            $username = $this->wizard->getValue(self::FIELD_USERNAME);
            $password = $this->wizard->getValue(self::FIELD_PASSWORD);

            $authentication = null;
            if ($username) {
                $authentication = $username;

                if ($password) {
                    $authentication .= ':' . $password;
                }

                $authentication .= '@';
            }

            if ($port) {
                $port = ':' . $port;
            }

            $connectionName = 'default';
            $dsn = $protocol . '://' . $authentication . $server . $port . '/' . $database;

            $connectionModel = new ConnectionModel();
            $connectionModel->saveConnection($connectionName, $dsn);

            $connection = $connectionModel->getConnection($connectionName);
            if (!$connection->isConnectable()) {
                $validationException = new ValidationException();
                $validationException->addErrors('dsn', array(new ValidationError(self::TRANSLATION_DATABASE_ERROR, 'Could not connect with your database')));

                throw $validationException;
            }

            $dsn = new Dsn($dsn);
            $this->wizard->setVariable(self::VAR_DSN, $dsn);

            if (class_exists('zibo\\library\\orm\\ModelManager')) {
                ModelManager::getInstance()->defineModels();
            }
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