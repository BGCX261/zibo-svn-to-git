<?php

namespace zibo\library\xmlrpc\exception;

use \Exception;

/**
 * Exception thrown when a connection could not be opened
 */
class ConnectionException extends XmlRpcException {

    /**
     * Host to connect to
     * @var string
     */
    private $host;

    /**
     * Port to connect to
     * @var integer
     */
    private $port;

    /**
     * Constructs a new connection exception
     * @param string $host The host to connect to
     * @param integer $port The port to connect to
     * @param string $message The error message
     * @param string $code The error code
     * @param Exception $previousException The previous exception
     * @return null
     */
    public function __construct($host, $port, $message, $code, Exception $previousException = null) {
        $message = "Could not open a connection to {$this->host}:{$this->port}: " . $message;

        parent::__construct($message, $code);

        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Gets the host to connect to
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Gets the port to connect to
     * @return integer
     */
    public function getPort() {
        return $this->port;
    }

}