<?php

namespace zibo\library\http;

use zibo\library\String;

use zibo\ZiboException;

/**
 * URL library
 */
class Url {

    /**
     * Name of the HTTP protocol
     * @var string
     */
    const PROTOCOL_HTTP = 'http';

    /**
     * Name of the HTTPS protocol
     * @var string
     */
    const PROTOCOL_HTTPS = 'https';

    /**
     * The URL for the request
     * @var string
     */
    protected $url;

    /**
     * The protocol of the URL (http|https)
     * @var string
     */
    protected $protocol;

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
     * The path of the URL
     * @var string
     */
    protected $path;

    /**
     * The query of the URL
     * @var string
     */
    protected $query;

    /**
     * The base URL of the provided URL
     * @var string
     */
    protected $baseUrl;

    /**
     * The base URL with the path
     * @var string
     */
    protected $basePath;

    /**
     * Constructs a new URL
     * @param string $url The URL
     * @return null
     * @throws zibo\library\http\exception\InvalidUrlException when the provided URL is invalid
     */
    public function __construct($url) {
        $this->setUrl($url);
    }

    /**
     * Gets a string representation of this URL
     * @return string
     */
    public function __toString() {
        return $this->url;
    }

    /**
     * Gets the URL
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Sets the URL
     * @param string $url The URL to request
     * @return null
     * @throws zibo\ZiboException when the provided URL is invalid
     */
    public function setUrl($url) {
        if (!String::isString($url, String::NOT_EMPTY)) {
            throw new ZiboException('Provided URL is empty or invalid');
        }

        $urlInformation = @parse_url($url);
        if ($urlInformation === false) {
            throw new ZiboException('Provided URL is invalid: ' . $url);
        }

        $this->host = $urlInformation['host'];
        if (!$this->host) {
            throw new ZiboException('Could not parse the host from the provided URL');
        }

        $this->url = $url;

        if (isset($urlInformation['scheme'])) {
            $this->protocol = $urlInformation['scheme'];
        } else {
            $this->protocol = self::PROTOCOL_HTTP;
        }

        $this->baseUrl = $this->protocol . '://' . $this->host;

        if ($this->protocol == self::PROTOCOL_HTTPS) {
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

    /**
     * Gets the protocol
     * @return string
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * Gets the host
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Gets the port
     * @return string
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Gets the path
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Gets the query
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Gets the base URL
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * Gets the base URL with the path
     * @return string
     */
    public function getBasePath() {
        return $this->basePath;
    }

    /**
     * Checks whether the provided string looks like an HTTP URL
     * @param string $string String to check
     * @return boolean True when the string starts with http:// or https://, false otherwise
     */
    public static function looksLikeUrl($string) {
        if (String::startsWith($string, 'http://') || String::startsWith($string, 'https://')) {
            return true;
        }

        return false;
    }

}