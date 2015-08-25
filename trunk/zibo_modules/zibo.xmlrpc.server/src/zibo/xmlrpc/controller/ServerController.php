<?php

namespace zibo\xmlrpc\controller;

use zibo\core\controller\AbstractController;
use zibo\core\Zibo;

use zibo\library\xmlrpc\ExtendedServer;

use zibo\xmlrpc\view\ResponseView;
use zibo\xmlrpc\view\ServerView;

/**
 * Controller for the XML-RPC server
 */
class ServerController extends AbstractController {

    /**
     * Event to be run before the server acts
     * @var string
     */
    const EVENT_PRE_SERVICE = 'xmlrpc.service.pre';

    /**
     * The XML-RPC server
     * @var zibo\library\xmlrpc\ExtendedServer
     */
    private $server;

    /**
     * Create a new server controller
     * @return null
     */
    public function __construct() {
        $this->server = new ExtendedServer();
    }

    /**
     * Run the pre service event to give other modules a chance to hook their methods into the server
     * @return null
     */
    public function preAction() {
        Zibo::getInstance()->runEvent(self::EVENT_PRE_SERVICE, $this->server);
    }

    /**
     * Action to service the XML-RPC server
     * @return null
     */
    public function indexAction() {
        $this->service();
    }

    /**
     * Action to get an overview of the hosted XML-RPC methods
     * @return null
     */
    public function serverAction() {
        $view = new ServerView($this->request->getBaseUrl() . '/xmlrpc', $this->getMethods());
        $this->response->setView($view);
    }

    /**
     * Get an array with the hosted XML-RPC methods
     * @return array Array with containing arrays with signature, return and description key
     */
    private function getMethods() {
        $methods = array();

        $methodNames = $this->server->listMethods();
        foreach ($methodNames as $methodName) {
            $methodSignature = $this->server->methodSignature($methodName);
            $description = $this->server->methodHelp($methodName);
            $returnType = array_shift($methodSignature);
            $signature = $methodName . '(' . implode(', ', $methodSignature) . ')';
            $method = array(
                'signature' => $signature,
                'return' => $returnType,
                'description' => $description,
            );
            $methods[] = $method;
        }
        return $methods;
    }

    /**
     * Service the XML-RPC server
     * @return null
     */
    private function service() {
        $rawRequestData = file_get_contents('php://input');
        if (isset($_SERVER['HTTP_HMAC'])) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'hmac send to server: ' . $_SERVER['HTTP_HMAC']);
        }

        $requestXml = $this->stripHTTPHeader($rawRequestData);

        $response = $this->server->service($requestXml);

        $this->response->setHeader('HMAC', hash_hmac('md5', $response->getXmlString(), 'test'));
        $this->response->setHeader('Content-Type', 'text/xml');
        $this->response->setView(new ResponseView($response));
    }

    /**
     * Strip the HTTP headers from the request string
     * @param string $rawRequestData request string
     * @return string request string without the HTTP headers
     */
    private function stripHTTPHeader($rawRequestData) {
        return substr($rawRequestData, strpos($rawRequestData, "\r\n\r\n"));
    }

}