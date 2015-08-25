<?php

namespace zibo\xmlrpc\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\validation\validator\RegexValidator;
use zibo\library\validation\validator\RequiredValidator;
use zibo\library\validation\validator\WebsiteValidator;

/**
 * Form for a XML-RPC client to ask for a request
 */
class ClientForm extends Form {

    /**
     * Name of this form
     * @var string
     */
    const NAME = 'formXmlrpcClient';

    /**
     * Name of the server field
     * @var string
     */
    const FIELD_SERVER = 'server';

    /**
     * Name of the method field
     * @var string
     */
    const FIELD_METHOD = 'method';

    /**
     * Name of the parameters field
     * @var string
     */
    const FIELD_PARAMETERS = 'parameters';

    /**
     * Name of the invoke button
     * @var string
     */
    const BUTTON_INVOKE = 'invoke';

    /**
     * Translation key for the label of the submit button
     * @var string
     */
    const TRANSLATION_INVOKE = 'xmlrpc.button.invoke';

    /**
     * Constructs a new XML-RPC client form
     * @param string $action URL where this form will point to
     * @param string $server Hostname or IP address of the XML-RPC server
     * @param string $method The method or service to invoke
     * @param string $parameters String of the parameters
     * @return null
     * @see zibo\xmlrpc\parser\ParameterParser
     */
    public function __construct($action, $server = null, $method = null, $parameters = null) {
        parent::__construct($action, self::NAME);

        $fieldFactory = FieldFactory::getInstance();

        $serverField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_SERVER, $server);
        $serverField->addValidator(new WebsiteValidator());

        $methodField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_METHOD, $method);
        $methodField->addValidator(new RequiredValidator());
        $methodField->addValidator(new RegexValidator(array(RegexValidator::OPTION_REGEX => '/^([a-zA-Z0-9\\.]*)$/')));

        $parametersField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_PARAMETERS, $parameters);

        $invokeButton = $fieldFactory->createSubmitField(self::BUTTON_INVOKE, self::TRANSLATION_INVOKE);

        $this->addField($serverField);
        $this->addField($methodField);
        $this->addField($parametersField);
        $this->addField($invokeButton);
    }

    /**
     * Gets the server of the form
     * @return string
     */
    public function getServer() {
        return $this->getValue(self::FIELD_SERVER);
    }

    /**
     * Gets the method of the form
     * @return string
     */
    public function getMethod() {
        return $this->getValue(self::FIELD_METHOD);
    }

    /**
     * Gets the parameters of the form
     * @return string
     */
    public function getParameters() {
        return $this->getValue(self::FIELD_PARAMETERS);
    }

}