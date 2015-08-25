<?php

namespace zibo\core;

use zibo\library\message\Message;
use zibo\library\message\MessageList;
use zibo\library\Header;

use zibo\ZiboException;

/**
 * Represents the response to a request (in mosts cases a HTTP request).
 *
 * Provides an API for dealing with the common parts of a response:
 * <ul>
 *     <li>the HTTP status code:
 *          <ul>
 *              <li>{@link setStatusCode() setStatusCode()}</li>
 *              <li>{@link getStatusCode() getStatusCode()}</li>
 *          </ul>
 *     </li>
 *     <li>HTTP headers:
 *          <ul>
 *              <li>{@link addHeader() addHeader()}</li>
 *              <li>{@link setHeader() setHeader()}</li>
 *              <li>{@link removeHeader() removeHeader()}</li>
 *              <li>{@link getHeaders() getHeaders()}</li>
 *          </ul>
 *     </li>
 *     <li>redirecting (special purpose methods that use the HTTP status code and header handling methods under the hood):
 *         <ul>
 *              <li>{@link setRedirect() setRedirect()}</li>
 *              <li>{@link willRedirect() willRedirect()}</li>
 *              <li>{@link clearRedirect() clearRedirect()}</li>
 *         </ul>
 *     </li>
 *     <li>messages:
 *         <ul>
 *             <li>{@link addMessage() addMessage()}</li>
 *             <li>{@link getMessages() getMessages()}</li>
 *         </ul>
 *     </li>
 *     <li>the view, which eventually will be used to render the response body:
 *         <ul>
 *             <li>{@link setView() setView()}</li>
 *             <li>{@link getView() getView()}</li>
 *         </ul>
 *     </li>
 * </ul>
 *
 */
class Response {

    /**
     * Header name for HTTP authentication
     * @var string
     */
    const HEADER_AUTHENTICATE = 'WWW-Authenticate';

    /**
     * Header name for a redirect
     * @var string
     */
    const HEADER_REDIRECT = 'Location';

    /**
     * Http status code for a ok status
     * @var int
     */
    const STATUS_CODE_OK = 200;

    /**
     * Http status code for a moved permanently status
     * @var int
     */
    const STATUS_CODE_MOVED_PERMANENTLY = 301;

    /**
     * Http status code for a found status
     * @var int
     */
    const STATUS_CODE_FOUND = 302;

    /**
     * Http status code for a not modified status
     * @var int
     */
    const STATUS_CODE_NOT_MODIFIED = 304;

    /**
     * Http status code for a unauthorized status
     * @var int
     */
    const STATUS_CODE_UNAUTHORIZED = 401;

    /**
     * Http status code for a forbidden status
     * @var int
     */
    const STATUS_CODE_FORBIDDEN = 403;

    /**
     * Http status code for a not found status
     * @var int
     */
    const STATUS_CODE_NOT_FOUND = 404;

    /**
     * Http status code for a server error status
     * @var int
     */
    const STATUS_CODE_SERVER_ERROR = 500;

    /**
     * Array containing the headers
     * @var array
     */
    private $headers;

    /**
     * The Http response code
     * @var int
     */
    private $statusCode;

    /**
     * Container of the messages assigned to this response
     * @var zibo\core\MessageList
     */
    private $messages;

    /**
     * The view for this response
     * @var zibo\core\View
     */
    private $view;

    /**
     * Construct a new response
     * @return null
     */
    public function __construct() {
        $this->headers = array();
        $this->messages = new MessageList();
        $this->statusCode = self::STATUS_CODE_OK;
    }

    /**
     * Sets a HTTP header, replacing any previously added HTTP headers with the same name.
     *
     * @param string $name the name of the header
     * @param string $value the value of the header
     * @return null
     *
     * @see addHeader()
     */
    public function setHeader($name, $value) {
        $this->removeHeader($name);
        $this->addHeader($name, $value);
    }

    /**
     * Adds a HTTP header. On Wikipedia you can find a {@link http://en.wikipedia.org/wiki/List_of_HTTP_headers list of HTTP headers}
     *
     * If a Locaton header is added, the status code will also be automatically set to 302 Found if the current status code is not 200 OK.
     *
     * @param string $name the name of the header
     * @param string $value the value of the header
     * @return null
     *
     * @see setHeader()
     */
    public function addHeader($name, $value) {
        if (self::HEADER_REDIRECT == $name && $this->statusCode == self::STATUS_CODE_OK) {
            $this->statusCode = self::STATUS_CODE_FOUND;
        }

        $this->headers[] = new Header($name, $value);
    }

    /**
     * Removes a HTTP header.
     *
     * @param string $name the name of the header you want to remove
     * @return null
     */
    public function removeHeader($name) {
        foreach ($this->headers as $key => $header) {
            if ($header->getName() == $name) {
                unset($this->headers[$key]);
            }
        }
    }

    /**
     * Returns the HTTP headers.
     *
     * @return array the HTTP headers as an array of zibo\core\Header objects
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Removes those HTTP headers that cause the browser to redirect.
     *
     * The HTTP status code of the response will be reset to 200 OK.
     * @return null
     */
    public function clearRedirect() {
        if (!$this->willRedirect()) {
            return;
        }

        $this->statusCode = self::STATUS_CODE_OK;

        $this->removeHeader(self::HEADER_REDIRECT);
    }

    /**
     * Returns if the current HTTP headers will cause a redirect.
     *
     * @return bool
     */
    public function willRedirect() {
        return ($this->statusCode >= 300 && $this->statusCode < 400);
    }

    /**
     * Sets the HTTP headers required to make the browser redirect.
     *
     * The HTTP status code is set to 302 Found. If you want to use another status code,
     * you need to call {@link setStatusCode() setStatusCode()} after calling setRedirect().
     *
     * @param string $url the URL to redirect to
     * @param string $code The response code for this redirect
     * @return null
     * @throws zibo\ZiboException when the provided response code is not a valid redirect reponse code
     */
    public function setRedirect($url, $code = null) {
        if (!$code) {
            $code = self::STATUS_CODE_FOUND;
        }

        if (!is_int($code) || $code < 300 || $code > 399) {
            throw new ZiboException('Provided code is an invalid redirect status code');
        }

        $this->setStatusCode($code);
        $this->setHeader(self::HEADER_REDIRECT, $url);
    }

    /**
     * Sets the HTTP status code. At Wikipedia you can find a {@link http://en.wikipedia.org/wiki/List_of_HTTP_status_codes list of HTTP status codes}.
     *
     * @param int $code the HTTP status code
     * @return null
     * @see STATUS_CODE_OK, STATUS_CODE_MOVED_PERMANENTLY, STATUS_CODE_FOUND, STATUS_CODE_NOT_MODIFIED, STATUS_CODE_NOT_FOUND
     * @throws zibo\ZiboException when the provided response code is not a valid reponse code
     */
    public function setStatusCode($code) {
        if (!is_int($code) || $code < 100 || $code > 599) {
            throw new ZiboException('Provided code is an invalid status code');
        }

        $this->statusCode = $code;
    }

    /**
     * Returns the current HTTP status code.
     *
     * @return int
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Adds a message.
     *
     * @param zibo\library\message\Message $message
     */
    public function addMessage(Message $message) {
        $this->messages->add($message);
    }

    /**
     * Returns the messages.
     *
     * @return zibo\library\message\MessageList a list of messages
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Sets the view of this response.
     *
     * @param zibo\core\View $view the view
     * @return null
     */
    public function setView(View $view = null) {
        $this->view = $view;
    }

    /**
     * Returns the view of this response.
     *
     * @return zibo\core\View the view
     */
    public function getView() {
        return $this->view;
    }

}