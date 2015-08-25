<?php

namespace zibo\library\xmlrpc;

use zibo\core\Zibo;

use zibo\library\xmlrpc\exception\XmlRpcException;
use zibo\library\Callback;
use zibo\library\String;

use \DOMDocument;
use \Exception;

/**
 * XML-RPC server
 */
class Server {

    /**
     * Logname for the messages logged by this class
     * @var string
     */
    const LOG_NAME = 'xmlrpc';

    /**
     * Name of the callback property
     * @var string
     */
    const SERVICE_CALLBACK = 'callback';

    /**
     * Name of the return type property
     * @var string
     */
    const SERVICE_RETURN_TYPE = 'return';

    /**
     * Name of the parameters type property
     * @var string
     */
    const SERVICE_PARAMETERS_TYPES = 'parameters';

    /**
     * Name of the description property
     * @var string
     */
    const SERVICE_DESCRIPTION = 'description';

    /**
     * The services of this server
     * @var array
     */
    protected $services;

    /**
     * Constructs a new instance of a XML-RPC server
     * @return null
     */
    public function __construct() {
        $this->services = array();
    }

    /**
     * Registers a service to this server instance
     * @param string $name The name of the service
     * @param string|array|zibo\library\Callback $callback The callback with the logic of the service
     * @param string $returnType The type of the resulting value
     * @param array $parameterTypes The types of parameters for this service
     * @param string $description A description of this service
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the name of the service is empty
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the name of the service is already used by another service
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when a invalid return type or parameter type has been detected
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the description is not a valid string
     */
    public function registerService($name, $callback, $returnType = 'string', $parameterTypes = null, $description = null) {
        if (String::isEmpty($name)) {
            throw new XmlRpcException('Name of the service is empty');
        }
        if (isset($this->services[$name])) {
            throw new XmlRpcException($name . ' service already registered');
        }

        $callback = new Callback($callback);

        if (!Value::isValidType($returnType)) {
            throw new XmlRpcException('Return type ' . $returnType . ' is not a valid type');
        }

        if ($parameterTypes == null) {
            $parameterTypes = array();
        } elseif (!is_array($parameterTypes)) {
            $parameterTypes = array($parameterTypes);
        }
        foreach ($parameterTypes as $type) {
            if (!Value::isValidType($type)) {
                throw new XmlRpcException('Parameter type ' . $type . ' is not a valid type');
            }
        }

        if ($description != null && !is_string($description)) {
            throw new XmlRpcException('Provided description is not a string');
        }

        $service = array(
            self::SERVICE_CALLBACK => $callback,
            self::SERVICE_RETURN_TYPE => $returnType,
            self::SERVICE_PARAMETERS_TYPES => $parameterTypes,
            self::SERVICE_DESCRIPTION => $description,
        );
        $this->services[$name] = $service;
    }

    /**
     * Service the provided request
     * @param string $requestXml The XML of the request
     * @return Response The response with the resulting value or a error message
     */
    public function service($requestXml) {
        try {
            $request = Request::fromXMLString($requestXml);
        } catch (XmlRpcException $e) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Invalid request', $e->getMessage(), 1, self::LOG_NAME);
            return new Response(null, 100 + $e->getCode(), $e->getMessage());
        }

        return $this->invokeRequest($request);
    }

    /**
     * Invokes the provided request
     * @param Request $request The XML-RPC request
     * @return Response The response with the resulting value or a error message
     */
    public function invokeRequest(Request $request) {
        $zibo = Zibo::getInstance();

        $methodName = $request->getMethodName();
        if (!isset($this->services[$methodName])) {
            $error = 'Unknown method ' . $methodName;

            $zibo->runEvent(Zibo::EVENT_LOG, $error, '', 1, self::LOG_NAME);

            return new Response(null, 1, $error);
        }

        $callback = $this->services[$methodName][self::SERVICE_CALLBACK];
        $returnType = $this->services[$methodName][self::SERVICE_RETURN_TYPE];
        $parameterTypes = $this->services[$methodName][self::SERVICE_PARAMETERS_TYPES];

        try {
            $parameters = $this->getCallbackParameters($request->getParameters(), $parameterTypes);
        } catch (XmlRpcException $e) {
            $zibo->runEvent(Zibo::EVENT_LOG, 'Invalid call to ' . $methodName, $e->getMessage(), 1, self::LOG_NAME);

            return new Response(null, 3, $methodName . ': ' . $e->getMessage());
        }

        try {
            $parameterString = '';
            if ($parameters) {
                foreach ($parameters as $parameter) {
                    if (is_scalar($parameter)) {
                        $parameterString .= $parameter . ', ';
                        continue;
                    }
                    $parameterString .= gettype($parameter) . ', ';
                }
                $parameterString = substr($parameterString, 0, -2);
            }

            $zibo->runEvent(Zibo::EVENT_LOG, 'Invoking ' . $callback . '(' . $parameterString . ')', '', 0, self::LOG_NAME);

            $result = $callback->invokeWithArrayArguments($parameters);
            $response = new Response(new Value($result, $returnType));
        } catch (Exception $exception) {
            $error = $exception->getMessage();
            if (!$error) {
                $error = get_class($exception);
            }

            $error = $methodName . ': ' . $error;

            $zibo->runEvent(Zibo::EVENT_LOG, $error, $exception->getTraceAsString(), 1, self::LOG_NAME);

            $response = new Response(null, 200 + $exception->getCode(), $error);
        }

        return $response;
    }

    /**
     * Gets and validates the provided parameter values
     * @param array $parameters Array with Value objects
     * @param array $parameterTypes Array with the expected types for the provided values
     * @return array Array with the actual values
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the number of parameters is not the same as the number of expected parameters
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when one of the parameters is not of the expected type and could not be converted to it
     */
    private function getCallbackParameters($parameters, $parameterTypes) {
        $result = array();

        $countParameters = count($parameters);
        $countParameterTypes = count($parameterTypes);
        if ($countParameters != $countParameterTypes) {
            throw new XmlRpcException('Incorrect parameter count: expecting ' . $countParameterTypes . ' parameters but got ' . $countParameters . '.');
        }

        for ($i = 0; $i < $countParameters; $i++) {
            $expectedType = $parameterTypes[$i];
            $type = $parameters[$i]->getType();
            if ($expectedType != null && $type != $expectedType) {
                try {
                    $value = $parameters[$i]->getValue();
                    $value = Value::convertValue($value, $type, $expectedType);
                    $parameters[$i] = new Value($value, $type);
                } catch (Exception $e) {
                    throw new XmlRpcException('Parameter ' . ($i + 1) . ' is of type ' . $type . ', expecting type ' . $expectedType . ' (' . $e->getMessage() . ')', 0, $e);
                }
            }

            $result[] = $parameters[$i]->getValue();
        }

        return $result;
    }

}