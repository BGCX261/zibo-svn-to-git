<?php

namespace zibo\library\spider;

use zibo\library\String;

use \Exception;

class Crawl {

    private $url;

    private $baseUrl;

    private $basePath;

    private $response;

    public function __construct($url) {
        $this->setUrl($url);
    }

    public function getResponse() {
        return $this->response;
    }

    public function getBasePath() {
        return $this->basePath;
    }

    public function getBaseUrl() {
        return $this->baseUrl;
    }

    public function getUrl() {
        return $this->url;
    }

    private function setUrl($url) {
        if (String::isEmpty($url)) {
            throw new Exception('Provided URL is empty');
        }

        $urlInformation = @parse_url($url);
        if ($urlInformation === false) {
            throw new Exception('Provided URL is invalid');
        }

        $this->host = $urlInformation['host'];
        if (!$this->host) {
            throw new Exception('Could not parse the host from the provided URL');
        }

        $this->response = null;
        $this->url = $url;

        if (isset($urlInformation['scheme'])) {
            $this->scheme = $urlInformation['scheme'];
        } else {
            $this->scheme = 'http';
        }

        $this->baseUrl = $this->scheme . '://' . $this->host;

        if ($this->scheme == 'https') {
            $this->port = isset($urlInformation['port']) ? $urlInformation['port'] : 443;

            if ($this->port != 443) {
                $this->baseUrl .= ':' . $this->port;
            }
        } else {
            $this->port = isset($urlInformation['port']) ? $urlInformation['port'] : 80;

            if ($this->port != 80) {
                $this->baseUrl .= ':' . $this->port;
            }
        }

        $this->basePath = $this->baseUrl;

        if (isset($urlInformation['path'])) {
            $this->path = $urlInformation['path'];

            if (substr($this->path, -1, 1) == '/') {
                $this->basePath .= $this->path;
            } else {
                $position = strrpos($this->path, '/');
                if ($position) {
                    $this->basePath .= substr($this->path, 0, $position);
                }

                $this->basePath .= '/';
            }
        } else {
            $this->path = '/';

            $this->basePath .= '/';
        }

        $this->query = '';
        if (isset($urlInformation['query'])) {
            $this->query = '?' . $urlInformation['query'];
        }
    }

    public function performCrawl() {
        $socket = $this->connect();

        $this->response = $this->performHead($socket);

        $responseCode = $this->response->getResponseCode();

        if (!$responseCode) {
            throw new Exception('No response received');
        }

        if ($this->response->isRedirect()) {
            return;
        }

        $contentType = $this->response->getHeader('Content-Type');
        if ($responseCode == 200 && String::startsWith($contentType, 'text/')) {
            $this->performGet($this->response);
        }

        fclose($socket);
    }

    private function connect($timeoutSeconds = 15, $timeoutMicroseconds = 0) {
        // secured url?
        if ($this->scheme == 'https') {
            $socket = @fsockopen('ssl://'. $this->host, $this->port, $errorNumber, $errorMessage, $timeoutSeconds);
        } else {
            $socket = @fsockopen($this->host, $this->port, $errorNumber, $errorMessage, $timeoutSeconds);
        }

        if (!$socket) {
            throw new Exception('Could not connect to ' . $this->host);
        }

        stream_set_timeout($socket, $timeoutSeconds, $timeoutMicroseconds);

        return $socket;
    }

    private function performHead($socket) {
        $request = "HEAD " . $this->path . $this->query . " HTTP/1.0\r\nHost: " . $this->host . "\r\nConnection: keep-alive\r\n\r\n";

        if (!fputs($socket, $request, strlen($request))) {
            throw Exception('Could not send the request');
        }

        $responseString = fread($socket, 4096);

        $attempt = 0;
        while (!$responseString && $attempt < 5) {
            sleep($attempt + 1);

            if (!fputs($socket, $request, strlen($request))) {
                throw new Exception('Could not resend the request');
            }

            $responseString = fread($socket, 4096);
            $attempt++;
        }

        return new HttpResponse($responseString);
    }

    private function performGet(HttpResponse $response) {
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => 15
            )
        ));

        $content = file_get_contents($this->url, false, $context);

        if (!$content) {
            throw new Exception('No content received');
        }

        $response->setContent($content);
    }

}