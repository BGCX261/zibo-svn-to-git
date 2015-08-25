<?php

namespace zibo\library\network;

use zibo\library\network\exception\ConnectionException;
use zibo\library\Number;
use zibo\library\String;

use \Exception;

/**
 * Connection with a server
 */
class Connection {

    /**
     * The hostname from the URL
     * @var string
     */
    protected $host;

    /**
     * The port of the host to connect to
     * @var integer
     */
    protected $port;

    /**
     * Flag to see if a secured connection should be made
     * @var boolean
     */
    protected $isSecured;

    /**
     * The socket of the connection
     * @var resource
     */
    protected $socket;

    /**
     * Constructs a new connection
     * @param string $host The hostname or ip address to connect to
     * @param integer $port The port to connect to
     * @param boolean $isSecured Set to true to create a secured connection (SSL)
     * @return null
     */
    public function __construct($host, $port = 80, $isSecured = false) {
        $this->setHost($host);
        $this->setPort($port);
        $this->setIsSecured($isSecured);
        $this->socket = null;
    }

    /**
     * Sets the host of this connection
     * @param string $host
     * @return null
     * @throws zibo\library\network\exception\ConnectionException when the host is invalid
     */
    protected function setHost($host) {
        if (String::isEmpty($host)) {
            throw new ConnectionException('Could not set the host: invalid host provided');
        }

        $this->host = $host;
    }

    /**
     * Gets the host of this connection
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Sets the port of this connection
     * @param integer $port The port of this connection
     * @return null
     * @throws zibo\library\network\exception\ConnectionException when the port is invalid
     */
    protected function setPort($port) {
        if (Number::isNegative($port)) {
            throw new ConnectionException('Could not set the port: invalid port provided');
        }

        $this->port = $port;
    }

    /**
     * Gets the port of this connection
     * @return integer
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Sets whether this is a secured connection (SSL)
     * @param boolean $isSecured
     * @return null
     */
    protected function setIsSecured($isSecured) {
        $this->isSecured = $isSecured;
    }

    /**
     * Gets whether this is a secured connection (SSL)
     * @return boolean
     */
    public function isSecured() {
        return $this->isSecured;
    }

    /**
     * Creates a connection with the host
     * @param integer $timeoutSeconds The timeout for the connection in seconds
     * @param integer $timeoutMicroseconds The timeout for the connection in microseconds
     * @return null
     */
    public function connect($timeoutSeconds = 15, $timeoutMicroseconds = 0) {
        if ($this->socket) {
            return;
        }

        // secured url?
        if ($this->isSecured) {
            $this->socket = @fsockopen('ssl://' . $this->host, $this->port, $errorNumber, $errorMessage, $timeoutSeconds);
        } else {
            $this->socket = @fsockopen($this->host, $this->port, $errorNumber, $errorMessage, $timeoutSeconds);
        }

        if (!$this->socket) {
            throw new ConnectionException('Could not connect to ' . $this->host);
        }

        stream_set_timeout($this->socket, $timeoutSeconds, $timeoutMicroseconds);
    }

    /**
     * Closes the connection with the host
     * @return null
     */
    public function disconnect() {
        if (!$this->socket) {
            return;
        }

        fclose($this->socket);
        $this->socket = null;
    }

    /**
     * Checks whether this connection is connected
     * @return boolean
     */
    public function isConnected() {
        return $this->socket ? true : false;
    }

    /**
     * Sends a request to the host
     * @param string $requestString The request string to send
     * @return null
     * @throws zibo\library\network\exception\ConnectionException when the request could not be send
     */
    public function sendRequest($requestString) {
        if (!$this->socket) {
            $this->connect();
        }

        $bytesRequestString = strlen($requestString);
        $bytesWritten = fwrite($this->socket, $requestString, $bytesRequestString);

        if ($bytesWritten === false) {
            throw ConnectionException('Could not send the request');
        }
    }

    /**
     * Receives the response from the host
     * @param integer $bytes Number of bytes to read
     * @return string The response
     */
    public function receiveResponse($bytes = 128) {
        $responseString = '';

        while (!feof($this->socket)) {
            $responseString .= fgets($this->socket, $bytes);
        }

        return $responseString;
    }

}