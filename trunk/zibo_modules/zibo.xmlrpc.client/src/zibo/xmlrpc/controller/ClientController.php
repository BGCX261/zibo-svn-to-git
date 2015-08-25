<?php

namespace zibo\xmlrpc\controller;

use zibo\core\controller\AbstractController;
use zibo\core\Zibo;

use zibo\library\validation\ValidationError;
use zibo\library\validation\exception\ValidationException;
use zibo\library\xmlrpc\exception\XmlRpcException;
use zibo\library\xmlrpc\exception\XmlRpcInvalidResponseException;
use zibo\library\xmlrpc\Client;
use zibo\library\xmlrpc\Request;
use zibo\library\Timer;

use zibo\xmlrpc\form\ClientForm;
use zibo\xmlrpc\parser\ParameterParser;
use zibo\xmlrpc\view\ClientView;

use zibo\ZiboException;

/**
 * Controller for the grafical XML-RPC client
 */
class ClientController extends AbstractController {

    /**
     * Translation key for a invalid response error
     * @var string
     */
    const TRANSLATION_ERROR_RESPONSE = 'xmlrpc.error.client.response';

    /**
     * Translation key for a connection error
     * @var string
     */
    const TRANSLATION_ERROR_CONNECTION = 'xmlrpc.error.client.connection';

    /**
     * Translation key for a parameter error
     * @var string
     */
    const TRANSLATION_ERROR_PARAMETERS = 'xmlrpc.error.client.parameters';

    /**
     * Action to show the request form and result of the previously submitted request
     * @return null
     */
    public function indexAction() {
        $request = null;
        $response = null;
        $responseString = null;
        $time = null;

        $form = new ClientForm($this->request->getBasePath());
        if ($form->isSubmitted()) {
            try {
                $form->validate();

                $server = $form->getServer();
                $method = $form->getMethod();
                $parameters = $form->getParameters();

                $timer = new Timer();

                $request = $this->getXmlrpcRequest($method, $parameters);

                $client = new Client($server);
                $response = $client->invoke($request);

                $time = $timer->getTime();
            } catch (XmlRpcInvalidResponseException $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);

                $responseString = $exception->getResponseString();

                $previous = $exception->getPrevious();
                if ($previous) {
                    $message = $previous->getMessage();
                } else {
                    $message = $exception->getMessage();
                }

                $error = new ValidationError(self::TRANSLATION_ERROR_RESPONSE, 'Response error: %error%', array('error' => $message));
                $validationException = new ValidationException();
                $validationException->addErrors(ClientForm::FIELD_SERVER, array($error));

                $form->setValidationException($validationException);
            } catch (XmlRpcException $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);

                $error = new ValidationError(self::TRANSLATION_ERROR_CONNECTION, 'Connection error: %error%', array('error' => $exception->getMessage()));
                $validationException = new ValidationException();
                $validationException->addErrors(ClientForm::FIELD_SERVER, array($error));

                $form->setValidationException($validationException);
            } catch (ValidationException $exception) {
                $form->setValidationException($exception);
            }
        }

        $view = new ClientView($form, $request, $response, $responseString, $time);
        $this->response->setView($view);
    }

    /**
     * Get a XML-RPC request for the provided method and parameters
     * @param string $method full name of the method
     * @param string $parameters submitted parameters in string format, ready to be parsed
     * @return zibo\library\xmlrpc\Request the request for the XML RPC server
     * @throws zibo\library\validation\exception\ValidationException when the parameters could not be parsed
     */
    private function getXmlrpcRequest($method, $parameters) {
        $request = new Request($method);

        if (!$parameters) {
            return $request;
        }

        $parser = new ParameterParser();
        try {
            $parameters = $parser->parse($parameters);
        } catch (ZiboException $exception) {
            $error = new ValidationError(self::TRANSLATION_ERROR_PARAMETERS, 'Could not parse the parameters: %error%', array('error' => $exception->getMessage()));
            $validationException = new ValidationException();
            $validationException->addErrors(ClientForm::FIELD_PARAMETERS, array($error));

            throw $validationException;
        }

        foreach ($parameters as $parameter) {
            $request->addParameter($parameter);
        }

        return $request;
    }

}