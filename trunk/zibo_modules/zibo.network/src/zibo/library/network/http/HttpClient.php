<?php

namespace zibo\library\network\http;

use zibo\core\Zibo;

use zibo\library\network\Connection;

use zibo\library\String;
use zibo\library\Url;

/**
 * A simple HTTP client
 */
class HttpClient {

    /**
     * The open connections
     * @var array
     */
    private $connections;

    /**
     * The string used as a line break
     * @var string
     */
    private $lineBreak;

    /**
     * Constructs a new HTTP client
     * @return null
     */
    public function __construct() {
        $this->connections = array();
        $this->lineBreak = "\r\n";
    }

    /**
     * Performs a method
     * @param string $method
     * @param HttpRequest $request
     * @param boolean|null $keepAlive
     * @return HttpResponse
     */
    public function performRequest($method, HttpRequest $request, $keepAlive = null, $attempts = 5, $receiveBytes = 4096) {
        $url = $request->getUrl();

        $requestString = $method . ' ' . $url->getPath() . $url->getQuery() . ' HTTP/' . $request->getHttpVersion() . $this->lineBreak;
        $requestString .= 'Host: ' . $url->getHost() . $this->lineBreak;

        $headers = $request->getHeaders();
        foreach ($headers as $key => $value) {
            $requestString .= $key . ': ' . $value . $this->lineBreak;
        }

        if ($keepAlive == false) {
            $requestString .= 'Connection: Close' . $this->lineBreak;
        } else {
            $requestString .= 'Connection: Keep-alive' . $this->lineBreak;
        }

        $body = $request->getBody();
        if ($body) {
            $requestString .= $this->lineBreak . $body . $this->lineBreak;
        }

        $requestString .= $this->lineBreak;

        $connection = $this->getConnection($url, $keepAlive);
        $connection->sendRequest($requestString);

        $responseString = $connection->receiveResponse();

        $attempt = 1;
        while (!$responseString && $attempt < $attempts) {
            sleep($attempt + 1);

            $connection->sendRequest($requestString);

            $responseString = $connection->receiveResponse();
            $attempt++;
        }

        $response = new HttpResponse($responseString);

        $connectionHeader = $response->getHeader('Connection');
        if ($keepAlive == false || (!$keepAlive && $connectionHeader && $connectionHeader == 'close')) {
            $connection->disconnect();
        }

        return $response;
    }

    /**
     * Gets the redirect url of the provided response
     * @param HttpRequest $request The performed request
     * @param HttpResponse $response The received response
     * @return zibo\library\Url The URL of the redirect location
     * @throws zibo\library\network\http\exception\HttpException when the provided response has no Location header
     */
    public function getRedirectUrl(HttpRequest $request, HttpResponse $response) {
        $location = $response->getHeader('Location');
        if (!$location) {
            throw new HttpException('Could not get the redirect URL from the response: no Location header set');
        }

        if (!String::looksLikeUrl($location)) {
            if ($location[0] == '/') {
                $base = $request->getUrl()->getBaseUrl();
            } else {
                $base = $request->getUrl()->getBasePath();
            }

            $location = rtrim($base, '/') . '/' . ltrim($location, '/');
        }

        return new Url($location);
    }

    /**
     * Closes the connection of the provided URL
     * @param zibo\library\Url $url The URL of the connection
     * @return null
     */
    public function closeConnection(Url $url) {
        $id = $this->getConnectionId($url);

        if (!array_key_exists($id, $this->connections)) {
            return;
        }

        $this->connections[$id]->disconnect();

        unset($this->connections[$id]);
    }

    /**
     * Gets the connection with the server of the provided URL
     * @param zibo\library\Url $url The URL to get a connection for
     * @return zibo\library\network\Connection A network connection with the server of the provided URL
     */
    protected function getConnection(Url $url) {
        $id = $this->getConnectionId($url);

        if (array_key_exists($id, $this->connections)) {
            $connection = $this->connections[$id];
        } else {
            $host = $url->getHost();
            $port = $url->getPort();
            $isSecured = $url->getProtocol() == Url::PROTOCOL_HTTPS ? true : false;

            $connection = new Connection($host, $port, $isSecured);

            $this->connections[$id] = $connection;
        }

        $connection->connect();

        return $connection;
    }

    /**
     * Gets a connection id for the provided url
     * @return string
     */
    protected function getConnectionId(Url $url) {
        if ($url->getProtocol() == Url::PROTOCOL_HTTPS) {
            $id = 'ssl://';
        } else {
            $id = 'tcp://';
        }

        $id .= $url->getHost() . ':' . $url->getPort();

        return $id;
    }

}