<?php

namespace zibo\library\network\http;

use zibo\library\network\http\exception\InvalidResponseException;

/**
 * The data of a HTTP response
 */
class HttpResponse {

    /**
     * The response code
     * @var integer
     */
    protected $responseCode;

    /**
     * The received headers
     * @var array
     */
    protected $headers;

    /**
     * The body of the response
     * @var string
     */
    protected $body;

    /**
     * The source of the response
     * @var string
     */
    protected $source;

    /**
     * Constructs a new HTTP response object
     * @param string $response The full HTTP response from the request
     * @return null
     */
    public function __construct($response) {
        $this->responseCode = 0;
        $this->headers = array();
        $this->body = null;

        $this->parseResponse($response);

        $this->source = $response;
    }

    /**
     * Gets the source of the response, the unparsed response
     * @return string
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * Gets the response code
     * @return integer
     */
    public function getResponseCode() {
        return $this->responseCode;
    }

    /**
     * Checks whether this response is a redirect. Checked by looking if the response code is 3XX
     * @return boolean
     */
    public function isRedirect() {
        return substr($this->responseCode, 0, 1) == '3';
    }

    /**
     * Checks whether this is a success response
     * @return boolean
     */
    public function isSuccess() {
        return substr($this->responseCode, 0, 1) == '2';
    }

    /**
     * Gets a header from the response
     * @param string $name Name of the header
     * @return null|string The value of the header if found, null otherwise
     */
    public function getHeader($name) {
        if (!array_key_exists($name, $this->headers)) {
            return null;
        }

        return $this->headers[$name];
    }

    /**
     * Gets all the headers
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Gets the body of the response
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Parse the full HTTP response into the response code, headers and content
     * @return null
     */
    private function parseResponse($response) {
        $lines = explode("\r\n", $response);

        // get the status code
        $status = array_shift($lines);

        preg_match('#^HTTP/.* ([0-9]{3,3})( (.*))?#i', $status, $matches);
        if (array_key_exists(1, $matches)) {
            $this->responseCode = $matches[1];
        } else {
            throw new InvalidResponseException('Could not parse the response: no HTTP response');
        }

        // get the headers
        $emptyLine = false;
        while (!$emptyLine) {
            $line = array_shift($lines);
            $line = trim($line);

            if (!$line) {
                $emptyLine = true;
                continue;
            }

            $position = strpos($line, ': ');
            if (!$position) {
                continue;
            }

            list($name, $value) = explode(': ', $line, 2);

            $this->headers[$name] = $value;
        }

        // get the content
        $this->body = '';
        while ($lines) {
            $line = array_shift($lines);
            $this->body .= $line;
        }
    }

}