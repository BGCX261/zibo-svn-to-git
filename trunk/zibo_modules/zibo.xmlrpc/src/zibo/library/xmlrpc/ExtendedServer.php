<?php

namespace zibo\library\xmlrpc;

use zibo\library\exception\XmlRpcException;
use zibo\library\String;

/**
 * Xmlrpc server which implements the Introspection protocol
 * http://xmlrpc-c.sourceforge.net/introspection.html
 */
class ExtendedServer extends Server {

    /**
     * Description for the listMethods service
     * @var string
     */
    const DESCRIPTION_LIST_METHODS = 'This method returns a list of the methods the server has, ordered by name.';

    /**
     * Description for the methodSignature service
     * @var string
     */
    const DESCRIPTION_METHOD_SIGNATURE = 'This method returns a description of the argument format a particular method expects. (methodName)';

    /**
     * Description for the methodHelp service
     * @var string
     */
    const DESCRIPTION_METHOD_HELP = 'This method returns a text description of a particular method. (methodName)';

    /**
     * Description for the multicall service
     * @var string
     */
    const DESCRIPTION_MULTICALL = 'This method takes one parameter, an array of \'request\' struct types, and calls multiple methods in one call. Each request struct must contain a methodName member of type string and a params member of type array, and corresponds to the invocation of the corresponding method. (requests)';

    /**
     * String for a undefined description of a method
     * @var string
     */
    const METHOD_UNDEFINED = 'undef';

    /**
     * Key for the name of a method in a multicall array
     * @var string
     */
    const MULTICALL_METHOD_NAME = 'methodName';

    /**
     * Key for the parameters of a method in a multicall array
     * @var string
     */
    const MULTICALL_PARAMS = 'params';

    /**
     * Prefix for the system methods
     * @var string
     */
    const SERVICE_PREFIX = 'system.';

    /**
     * Name of the listMethods service
     * @var string
     */
    const SERVICE_LIST_METHODS = 'listMethods';

    /**
     * Name of the methodSignature service
     * @var string
     */
    const SERVICE_METHOD_SIGNATURE = 'methodSignature';

    /**
     * Name of the methodHelp service
     * @var string
     */
    const SERVICE_METHOD_HELP = 'methodHelp';

    /**
     * Name of the multicall service
     * @var string
     */
    const SERVICE_MULTICALL = 'multicall';

    /**
     * Constructs a new extended XML-RPC server
     * @return null
     */
    public function __construct() {
        parent::__construct();
        $this->registerIntrospectionServices();
    }

    /**
     * Registers the services of the Introspection protocol
     * @return null
     */
    private function registerIntrospectionServices() {
        $this->registerIntrospectionService(self::SERVICE_LIST_METHODS, Value::TYPE_ARRAY, null, self::DESCRIPTION_LIST_METHODS);
        $this->registerIntrospectionService(self::SERVICE_METHOD_SIGNATURE, Value::TYPE_ARRAY, array(Value::TYPE_STRING), self::DESCRIPTION_METHOD_SIGNATURE);
        $this->registerIntrospectionService(self::SERVICE_METHOD_HELP, Value::TYPE_STRING, array(Value::TYPE_STRING), self::DESCRIPTION_METHOD_HELP);
        $this->registerIntrospectionService(self::SERVICE_MULTICALL, Value::TYPE_ARRAY, array(Value::TYPE_ARRAY), self::DESCRIPTION_MULTICALL);
    }

    /**
     * Registers a method of the Introspection protocol
     * @param string $name Name of the method
     * @param string $returnType Type of the return value
     * @param array $parameterTypes Array with the types of the parameter values
     * @param string $description The description of the method
     * @return null
     */
    private function registerIntrospectionService($name, $returnType, $parameterTypes = null, $description = null) {
        $callback = array($this, $name);
        $name = self::SERVICE_PREFIX . $name;
        $this->registerService($name, $callback, $returnType, $parameterTypes, $description);
    }

    /**
     * Gets all the methods of this XML-RPC server
     * @return array Array with method names
     */
    public function listMethods() {
        $methods = array_keys($this->services);
        sort($methods);
        return $methods;
    }

    /**
     * Gets the signature of the provided method
     * @param string $methodName Name of the method
     * @return string The signature of the method
     */
    public function methodSignature($methodName) {
        if (!isset($this->services[$methodName])) {
            return self::METHOD_UNDEFINED;
        }

        $service = $this->services[$methodName];
        $signature = $service[self::SERVICE_PARAMETERS_TYPES];
        array_unshift($signature, $service[self::SERVICE_RETURN_TYPE]);

        return $signature;
    }

    /**
     * Gets the help of the provided method
     * @param string $methodName Name of the method
     * @return string
     */
    public function methodHelp($methodName) {
        if (!isset($this->services[$methodName])) {
            return self::METHOD_UNDEFINED;
        }

        $service = $this->services[$methodName];
        if ($service[self::SERVICE_DESCRIPTION]) {
            return $service[self::SERVICE_DESCRIPTION];
        }

        return '';
    }

    /**
     * Performs a multicall on this XML-RPC server
     * @param array $requests Array containing multiple requests. A request is an array with the methodname
     * @return array Array with the result values of the provided requests
     */
    public function multicall(array $requests) {
        $responses = array();

        foreach ($requests as $requestKey => $requestStruct) {
            try {
                $request = $this->getRequestFromStruct($requestStruct);
            } catch (XmlRpcException $e) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Invalid request', $e->getMessage(), 1, self::LOG_NAME);
                $responses[$requestKey] = new Response(null, 100 + $e->getCode(), $e->getMessage());
                continue;
            }

            $responses[$requestKey] = $this->invokeRequest($request);
        }

        foreach ($responses as $responseKey => $response) {
            $responses[$responseKey] = $this->getValueFromResponse($response);
        }

        return $responses;
    }

    /**
     * Gets the request from the provided request structure
     * @param array $requestStruct Array with a key methodName and a key params with their respective values
     * @return Request
     */
    protected function getRequestFromStruct(array $requestStruct) {
        if (!isset($requestStruct[self::MULTICALL_METHOD_NAME])) {
            throw new XmlRpcException('Could not find element ' . self::MULTICALL_METHOD_NAME . ' in the request struct.');
        }

        $methodName = $requestStruct[self::MULTICALL_METHOD_NAME];
        if (empty($methodName) || !is_string($methodName)) {
            throw new XmlRpcException('Provided method name is empty or invalid.');
        }

        $parameters = array();
        if (isset($requestStruct[self::MULTICALL_PARAMS])) {
            if (!is_array($requestStruct[self::MULTICALL_PARAMS])) {
                throw new XmlRpcException('Provided parameters are invalid, array expected.');
            }
            $parameters = $requestStruct[self::MULTICALL_PARAMS];
        }

        $request = new Request($methodName);
        foreach ($parameters as $parameter) {
            $request->addParameter($parameter);
        }

        return $request;
    }

    /**
     * Gets the result value from the provided response
     * @param Response $response Response to get the value from
     * @return mixed The value of the provided response
     */
    protected function getValueFromResponse(Response $response) {
        if (!$response->getErrorCode()) {
            return array($response->getValue());
        }

        $error = array(
            'faultCode' => $response->getErrorCode(),
            'faultString' => $response->getErrorMessage(),
        );

        return $error;
    }

}