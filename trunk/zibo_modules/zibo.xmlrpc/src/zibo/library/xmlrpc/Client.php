<?php

namespace zibo\library\xmlrpc;

use zibo\library\validation\validator\WebsiteValidator;
use zibo\library\xmlrpc\exception\ConnectionException;
use zibo\library\xmlrpc\exception\XmlRpcException;
use zibo\library\String;

use \DOMDocument;

/**
 * XML-RPC client
 */
class Client {

    /**
     * The host of the XML-RPC server
     * @var string
     */
    private $host;

    /**
     * The port of the XML-RPC server
     * @var string
     */
    private $port;

    /**
     * The path of the XML-RPC server
     * @var string
     */
    private $path;

    /**
     * Constructs a new XML-RPC client
     * @param string $url URL where the XML-RPC server is listening to
     * @return null
     * @throws zibo\library\xmlrpc\exception When the provided url is empty or invalid
     */
    public function __construct($url) {
        if (String::isEmpty($url)) {
            throw new XmlRpcException('Empty url provided');
        }
        $websiteValidator = new WebsiteValidator();
        if (!$websiteValidator->isValid($url)) {
            throw new XmlRpcException('Invalid url provided: ' . $url);
        }

        $urlParts = parse_url($url);

        if (strtolower($urlParts['scheme']) !== 'http') {
            throw new XmlRpcException("The scheme {$urlParts['scheme']} is not supported");
        }
        $this->host = $urlParts['host'];
        $this->port = isset($urlParts['port']) ? $urlParts['port'] : 80;
        $this->path = isset($urlParts['path']) ? $urlParts['path'] : '/';
        if (isset($urlParts['query'])) {
            $this->path .= '?' . $urlParts['query'];
        }
    }

    /**
     * Invokes the provided request on the XML-RPC server
     * @param Request $request XML-RPC request
     * @return Response The response from the server
     */
    public function invoke(Request $request) {
        $requestXml = $request->getXmlString();

        $connection = @fsockopen($this->host, $this->port, $errno, $errstr);
        if (!$connection) {
            throw new ConnectionException($this->host, $this->port, $errstr, $errno);
        }

        $contentLength = strlen($requestXml);

        $headers = array(
            "POST {$this->path} HTTP/1.0",
            "User-Agent: Zibo",
            "Host: {$this->host}",
            "Connection: close",
            "Content-Type: text/xml",
            "Content-Length: {$contentLength}",
        );

        fwrite($connection, implode("\r\n", $headers));
        fwrite($connection, "\r\n\r\n");
        fwrite($connection, $requestXml);

        $response = '';
        while (!feof($connection)) {
            $response .= fgets($connection, 1024);
        }
        fclose($connection);

        #strip headers off of response
        $responseXml = substr($response, strpos($response, "\r\n\r\n") + 4);

        $response = Response::fromXMLString($responseXml);

        return $response;
    }

}