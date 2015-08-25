<?php

namespace zibo\library\network\http;

use zibo\library\http\exception\ConnectionException;
use zibo\library\http\exception\InvalidUrlException;
use zibo\library\String;
use zibo\library\Url;

use \Exception;

/**
 * Class to perfom a HTTP request
 */
class HttpRequest {

    /**
     * The URL for the request
     * @var string
     */
    protected $url;

    /**
     * Array with headers to send along
     * @var array
     */
    protected $headers;

    /**
     * The body of the request
     * @var string
     */
    protected $body;

    /**
     * The HTTP version to send with the request
     * @var string
     */
    protected $httpVersion;

    /**
     * Constructs a new HTTP request
     * @param zibo\library\Url $url The URL to request
     * @param array $variables
     * @return null
     * @throws zibo\library\http\exception\InvalidUrlException when the provided URL is invalid
     */
    public function __construct(Url $url, array $variables = array(), $httpVersion = '1.1') {
        $this->setUrl($url);
        $this->setHttpVersion($httpVersion);
        $this->clearHeaders();

        $this->setPostVariables($variables);
    }

    /**
     * Gets the URL of this request
     * @return zibo\library\Url
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Sets the URL of this request
     * @param zibo\library\Url $url The URL to request
     * @return null
     * @throws zibo\library\http\exception\InvalidUrlException when the provided URL is invalid
     */
    public function setUrl(Url $url) {
        $this->url = $url;
    }

    /**
     * Gets the HTTP version to send with the request
     * @return string
     */
    public function getHttpVersion() {
        return $this->httpVersion;
    }

    /**
     * Sets the HTTP version to send with the request
     * @param string $httpVersion The HTTP version
     * @return null
     */
    public function setHttpVersion($httpVersion) {
        $this->httpVersion = $httpVersion;
    }

    /**
     * Sets a header for the request
     * @param string $name The name of the header
     * @param string $value The value for the header
     * @return null
     */
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    /**
     * Gets the value of a header
     * @param string $name Name of the header
     * @return string|null The value if set, null otherwise
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
     * Clears all the headers
     * @return null;
     */
    public function clearHeaders() {
        $this->headers = array();
    }

    /**
     * Gets the body of this request
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Sets the body of this request
     * @param string $content
     * @return null
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     * Sets the variables of the post as content
     * @param array $variables
     * @return null
     */
    public function setPostVariables(array $variables) {
        if (!$variables) {
            return;
        }

        $body = self::encodeValues($variables);

        $this->setBody($body);

        $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->setHeader('Content-Length', strlen($body));
    }

    /**
     * Gets an encoded string for a array of request variables
     * @param array $variables
     * @return string
     */
    public static function encodeValues(array $variables) {
        $content = '';

        foreach ($variables as $name => $value) {
            if ($content) {
                $content .= '&';
            }

            $content .= self::encodeValue($name) . '=' . self::encodeValue($value);
        }

        return $content;
    }

    /**
     * Gets an encoded string of a value
     * @param string $string The value to encode
     * @return string Encoded value
     */
    public static function encodeValue($string) {
        $string = str_replace(' ', '+', $string);
        return urlencode($string);
    }

}