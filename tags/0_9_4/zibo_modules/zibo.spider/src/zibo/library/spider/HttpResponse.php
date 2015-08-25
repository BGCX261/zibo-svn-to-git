<?php

namespace zibo\library\spider;

class HttpResponse {

    private $responseCode;

    private $headers;

    private $content;

    public function __construct($response) {
        $this->responseCode = 0;
        $this->headers = array();
        $this->content = null;

        $this->parseResponse($response);
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

    public function isRedirect() {
        return $this->responseCode > 300 && $this->responseCode < 399;
    }

    public function getHeader($name) {
        if (!array_key_exists($name, $this->headers)) {
            return null;
        }

        return $this->headers[$name];
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

    private function parseResponse($response) {
        $lines = explode("\n", $response);

        $response = array_shift($lines);

        preg_match('#^HTTP/.* ([0-9]{3,3})( (.*))?#i', $response, $matches);
        if (array_key_exists(1, $matches)) {
            $this->responseCode = $matches[1];
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if (!$line) {
                continue;
            }

            $position = strpos($line, ': ');
            if (!$position) {
                continue;
            }

            list($name, $value) = explode(': ', $line, 2);

            $this->headers[$name] = $value;
        }
    }

}