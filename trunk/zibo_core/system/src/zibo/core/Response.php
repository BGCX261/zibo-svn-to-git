<?php

namespace zibo\core;

use zibo\core\view\View;

use zibo\library\message\Message;
use zibo\library\message\MessageContainer;
use zibo\library\http\Header;
use zibo\library\http\HeaderContainer;
use zibo\library\Number;

use zibo\ZiboException;

/**
 * Represents the response to a request (in mosts cases a HTTP request).
 *
 * Provides an API for dealing with the common parts of a response:
 * <ul>
 *     <li>HTTP status code:
 *          <ul>
 *              <li>{@link setStatusCode() setStatusCode()}</li>
 *              <li>{@link getStatusCode() getStatusCode()}</li>
 *          </ul>
 *     </li>
 *     <li>HTTP headers:
 *          <ul>
 *              <li>{@link addHeader() addHeader()}</li>
 *              <li>{@link setHeader() setHeader()}</li>
 *              <li>{@link hasHeader() hasHeader()}</li>
 *              <li>{@link getHeader() getHeader()}</li>
 *              <li>{@link getHeaders() getHeaders()}</li>
 *              <li>{@link removeHeader() removeHeader()}</li>
 *              <li>{@link getHeaders() getHeaders()}</li>
 *          </ul>
 *     </li>
 *     <li>Cache (HTTP cache expiration and validation):
 *         <ul>
 *         		<li>{@link setIsPublic() setIsPublic()}</li>
 *         		<li>{@link setIsPrivate() setIsPrivate()}</li>
 *              <li>{@link setLastModified() setLastModified()}</li>
 *              <li>{@link getLastModified() getLastModified()}</li>
 *              <li>{@link setETag() setETag()}</li>
 *              <li>{@link getETag() getETag()}</li>
 *              <li>{@link isNotModified() isNotModified()}</li>
 *              <li>{@link setNotModified() setNotModified()}</li>
 *         </ul>
 *     </li>
 *     <li>Redirecting (special purpose methods that use the HTTP status code
 *         and header handling methods under the hood):
 *         <ul>
 *              <li>{@link setRedirect() setRedirect()}</li>
 *              <li>{@link willRedirect() willRedirect()}</li>
 *              <li>{@link clearRedirect() clearRedirect()}</li>
 *         </ul>
 *     </li>
 *     <li>Messages:
 *         <ul>
 *             <li>{@link addMessage() addMessage()}</li>
 *             <li>{@link getMessages() getMessages()}</li>
 *         </ul>
 *     </li>
 *     <li>View (represents the response body):
 *         <ul>
 *             <li>{@link setView() setView()}</li>
 *             <li>{@link getView() getView()}</li>
 *         </ul>
 *     </li>
 * </ul>
 */
class Response {

    /**
     * HTTP status code for a ok status
     * @var int
     */
    const STATUS_CODE_OK = 200;

    /**
     * HTTP status code for a moved permanently status
     * @var int
     */
    const STATUS_CODE_MOVED_PERMANENTLY = 301;

    /**
     * HTTP status code for a found status
     * @var int
     */
    const STATUS_CODE_FOUND = 302;

    /**
     * HTTP status code for a not modified status
     * @var int
     */
    const STATUS_CODE_NOT_MODIFIED = 304;

    /**
     * HTTP status code for a unauthorized status
     * @var int
     */
    const STATUS_CODE_UNAUTHORIZED = 401;

    /**
     * HTTP status code for a forbidden status
     * @var int
     */
    const STATUS_CODE_FORBIDDEN = 403;

    /**
     * HTTP status code for a not found status
     * @var int
     */
    const STATUS_CODE_NOT_FOUND = 404;

    /**
     * HTTP status code for a server error status
     * @var int
     */
    const STATUS_CODE_SERVER_ERROR = 500;

    /**
     * HTTP status code for a unimplemented request
     * @var int
     */
    const STATUS_CODE_NOT_IMPLEMENTED = 501;

    /**
     * The HTTP response status code
     * @var int
     */
    private $statusCode;

    /**
     * Container of the headers assigned to this response
     * @var zibo\library\http\HeaderContainer
     */
    private $headers;

    /**
     * The timestamp of the date of the response
     * @var integer
     */
    private $date;

    /**
     * The timestamp of the last modified date of the content
     * @var integer
     */
    private $dateLastModified;

    /**
     * Container of the messages assigned to this response
     * @var zibo\library\message\MessageContainer
     */
    private $messages;

    /**
     * The view for this response
     * @var zibo\core\view\View
     */
    private $view;

    /**
     * Construct a new response
     * @return null
     */
    public function __construct() {
        $this->statusCode = self::STATUS_CODE_OK;

        $this->date = time();
        $this->dateLastModified = null;

        $this->headers = new HeaderContainer();
        $this->headers->setHeader(Header::HEADER_DATE, Header::parseTime($this->date));

        $this->messages = new MessageContainer();
        $this->view = null;
    }

    /**
     * Sets the HTTP status code. At Wikipedia you can find a
     * {@link http://en.wikipedia.org/wiki/List_of_HTTP_status_codes list of HTTP status codes}.
     * @param integer $code The HTTP status code
     * @return null
     * @see STATUS_CODE_OK, STATUS_CODE_MOVED_PERMANENTLY, STATUS_CODE_FOUND,
     * STATUS_CODE_NOT_MODIFIED, STATUS_CODE_NOT_FOUND
     * @throws zibo\ZiboException when the provided response code is not a
     * valid reponse code
     */
    public function setStatusCode($code) {
        if (!is_int($code) || $code < 100 || $code > 599) {
            throw new ZiboException('Provided code is an invalid status code');
        }

        $this->statusCode = $code;
    }

	/**
	 * Returns the current HTTP status code.
     * @return integer
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Adds a HTTP header.
     *
     * On Wikipedia you can find a {@link http://en.wikipedia.org/wiki/List_of_HTTP_headers list of HTTP headers}.
     * If a Locaton header is added, the status code will also be automatically
     * set to 302 Found if the current status code is 200 OK.
     * @param string $name the name of the header
     * @param string $value the value of the header
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     * @throws zibo\ZiboException when the provided value is empty or invalid
     * @see setHeader()
     */
    public function addHeader($name, $value) {
        $header = new Header($name, $value);

        if ($header->getName() == Header::HEADER_LOCATION && !$this->willRedirect()) {
            $this->setStatusCode(self::STATUS_CODE_FOUND);
        }

        $this->headers->addHeader($header);
    }

    /**
     * Sets a HTTP header, replacing any previously added HTTP headers with
     * the same name.
     * @param string $name the name of the header
     * @param string $value the value of the header
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     * @throws zibo\ZiboException when the provided value is empty or invalid
     * @see addHeader()
     */
    public function setHeader($name, $value) {
        $this->headers->removeHeader($name);
        $this->addHeader($name, $value);
    }

    /**
     * Checks if a header is set
     * @param string $name The name of the header
     * @return boolean True if the header is set, false otherwise
     */
    public function hasHeader($name) {
        return $this->headers->hasHeader($name);
    }

    /**
    * Gets a HTTP header value
    * @param string $name Name of the header
    * @return string|array|null The value of the header, an array of values if
    * the header is set multiple times, null if not set
    * @see zibo\library\http\Header
    */
    public function getHeader($name) {
        $header = $this->headers->getHeader($name);
        if (!$header) {
            return null;
        }

        if (!is_array($header)) {
            return $header->getValue();
        }

        $values = array();
        foreach ($header as $h) {
            $values[] = $h->getValue();
        }

        return $values;
    }

    /**
     * Returns the HTTP headers.
     * @return zibo\library\http\HeaderContainer The container of the HTTP headers
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Removes a HTTP header.
     * @param string $name the name of the header you want to remove
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     */
    public function removeHeader($name) {
        $this->headers->removeHeader($name);
    }

    /**
     * Sets the HTTP headers required to make the browser redirect.
     * @param string $url The URL to redirect to
     * @param string $statusCode The status code for this redirect
     * @return null
     * @throws zibo\ZiboException when the provided status code is not a
     * valid redirect status code
     */
    public function setRedirect($url, $statusCode = null) {
        if ($statusCode) {
            if ($statusCode < 300 || 400 <= $statusCode) {
                throw new ZiboException('Invalid redirect status code provided');
            }

            $this->setStatusCode($statusCode);
        }

        $this->setHeader(Header::HEADER_LOCATION, $url);
    }

    /**
     * Checks if the response will redirect
     * @return boolean True if the status code is a redirect code, false otherwise
     */
    public function willRedirect() {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Removes the HTTP headers that cause the browser to redirect.
     *
     * The HTTP status code of the response will be reset to 200 OK.
     * @return null
     */
    public function clearRedirect() {
        if (!$this->willRedirect()) {
            return;
        }

        $this->headers->removeHeader(Header::HEADER_LOCATION);

        $this->statusCode = self::STATUS_CODE_OK;
    }

    /**
     * Sets the date the content will become stale
     * @param integer $timestamp Timestamp of the date
     * @return null
     */
    public function setExpires($timestamp = null) {
        if ($timestamp === null) {
            $this->headers->removeHeader(Header::HEADER_EXPIRES);
            return;
        }

        if (!Number::isNumeric($timestamp, Number::NOT_NEGATIVE | Number::NOT_ZERO | Number::NOT_FLOAT)) {
            throw new ZiboException('Invalid date provided');
        }

        $this->headers->setHeader(Header::HEADER_EXPIRES, Header::parseTime($timestamp));
    }

    /**
     * Gets the date the content was become stale
     * @return integer|null Timestamp of the date if set, null otherwise
     */
    public function getExpires() {
        $header = $this->headers->getHeader(Header::HEADER_EXPIRES);
        if (!$header) {
            return null;
        }

        return Header::parseTime($header->getValue());
    }

    /**
     * Sets or unsets the public cache control directive.
     *
     * When set to true, all caches may cache the response.
     * @param boolean $flag Set to false to unset the directive, true sets it
     * @return null
     */
    public function setIsPublic($flag = true) {
        $this->headers->removeCacheControlDirective(HeaderContainer::CACHE_CONTROL_PRIVATE);
        if ($flag) {
            $this->headers->addCacheControlDirective(HeaderContainer::CACHE_CONTROL_PUBLIC);
        } else {
            $this->headers->removeCacheControlDirective(HeaderContainer::CACHE_CONTROL_PUBLIC);
        }
    }

    /**
     * Gets the public cache control directive
     * @return boolean|null True if set, null otherwise
     */
    public function isPublic() {
        return $this->headers->getCacheControlDirective(HeaderContainer::CACHE_CONTROL_PUBLIC);
    }

    /**
     * Sets or unsets the private cache control directive
     *
     * When set to true, a shared cache must not cache the response.
     * @param boolean $flag Set to false to unset the directive, true or any value sets it
     * @return null
     */
    public function setIsPrivate($flag = true) {
        $this->headers->removeCacheControlDirective(HeaderContainer::CACHE_CONTROL_PUBLIC);
        if ($value !== false) {
            $this->headers->addCacheControlDirective(HeaderContainer::CACHE_CONTROL_PRIVATE, $value);
        } else {
            $this->headers->removeCacheControlDirective(HeaderContainer::CACHE_CONTROL_PRIVATE);
        }
    }

    /**
     * Gets the private cache control directive
     * @return boolean|string|null True or the field if set, null otherwise
     */
    public function isPrivate() {
        return $this->headers->getCacheControlDirective(HeaderContainer::CACHE_CONTROL_PRIVATE);
    }

    /**
     * Sets the max age cache control directive
     *
     * When set to true, a shared cache must not cache the response.
     * @param boolean $flag Set to false to unset the directive, true or any value sets it
     * @return null
     */
    public function setMaxAge($seconds = null) {
        if ($seconds === null) {
            $this->headers->removeCacheControlDirective(HeaderContainer::CACHE_CONTROL_MAX_AGE);
            return;
        }

        if (!Number::isNumeric($seconds, Number::NOT_NEGATIVE | Number::NOT_FLOAT)) {
            throw new ZiboException('The max age should be a unsigned integer');
        }

        $this->headers->addCacheControlDirective(HeaderContainer::CACHE_CONTROL_MAX_AGE, $seconds);
    }

    /**
     * Gets the max age cache control directive
     * @return integer|null Seconds if set, null otherwise
     */
    public function getMaxAge() {
        return $this->headers->getCacheControlDirective(HeaderContainer::CACHE_CONTROL_MAX_AGE);
    }

    /**
     * Sets the shared max age cache control directive
     *
     * This will make your response public
     * @param boolean $flag Set to false to unset the directive, true or any value sets it
     * @return null
     * @see setIsPublic()
     */
    public function setSharedMaxAge($seconds = null) {
        if ($seconds === null) {
            $this->headers->removeCacheControlDirective(HeaderContainer::CACHE_CONTROL_SHARED_MAX_AGE);
            return;
        }

        if (!Number::isNumeric($seconds, Number::NOT_NEGATIVE | Number::NOT_FLOAT)) {
            throw new ZiboException('The max age should be a unsigned integer');
        }

        $this->headers->addCacheControlDirective(HeaderContainer::CACHE_CONTROL_SHARED_MAX_AGE, $seconds);
        $this->setIsPublic();
    }

    /**
     * Gets the shared max age cache control directive
     * @return integer|null Seconds if set, null otherwise
     */
    public function getSharedMaxAge() {
        return $this->headers->getCacheControlDirective(HeaderContainer::CACHE_CONTROL_SHARED_MAX_AGE);
    }

    /**
     * Sets the date the content was last modified
     * @param integer $timestamp Timestamp of the date
     * @return null
     */
    public function setLastModified($timestamp = null) {
        if ($timestamp === null) {
            $this->dateLastModified = null;
            $this->headers->removeHeader(Header::HEADER_LAST_MODIFIED);
            return;
        }

        if (!Number::isNumeric($timestamp, Number::NOT_NEGATIVE | Number::NOT_ZERO | Number::NOT_FLOAT)) {
            throw new ZiboException('Invalid date provided');
        }

        $this->dateLastModified = $timestamp;

        $this->headers->setHeader(Header::HEADER_LAST_MODIFIED, Header::parseTime($timestamp));
    }

    /**
     * Gets the date the content was last modified
     * @return integer|null Timestamp of the date if set, null otherwise
     */
    public function getLastModified() {
        return $this->dateLastModified;
    }

    /**
     * Sets the ETag
     * @param string $eTag A unique identifier of the current version of
     * the content
     * @return null
     */
    public function setETag($eTag = null) {
        if ($eTag === null) {
            $this->headers->removeHeader(Header::HEADER_ETAG);
        } else {
            $this->headers->setHeader(Header::HEADER_ETAG, $eTag);
        }
    }

    /**
     * Gets the ETag
     * @return null|string A unique identifier of the current version of
     * the content if set, null otherwise
     */
    public function getETag() {
        $header = $this->headers->getHeader(Header::HEADER_ETAG);

        if (!$header) {
            return null;
        }

        return $header->getValue();
    }

    /**
     * Checks if the current status is not modified. If the status code is set
     * @param zibo\core\Request $request
     * @return boolean True if the content is not modified, false otherwise
     */
    public function isNotModified(Request $request) {
        $noneMatch = $request->getIfNoneMatch();
        $modifiedSince = $request->getIfModifiedSince();

        $eTag = $this->getETag();

        $isNoneMatch = !$noneMatch || isset($noneMatch['*']) || ($eTag && isset($noneMatch[$eTag]));
        $isModifiedSince = !$modifiedSince || $this->getLastModified() == $modifiedSince;

        $isNotModified = false;
        if ($noneMatch && $modifiedSince) {
            $isNotModified = $isNoneMatch && $isModifiedSince;
        } elseif ($noneMatch) {
            $isNotModified = $isNoneMatch;
        } elseif ($modifiedSince) {
            $isNotModified = $isModifiedSince;
        }

        return $isNotModified;
    }

    /**
     * Sets the response status code to not modified and removes illegal headers
     * for such a response code
     * @return null
     */
    public function setNotModified() {
        $this->setStatusCode(self::STATUS_CODE_NOT_MODIFIED);
        $this->setView(null);

        $removeHeaders = array(
            Header::HEADER_ALLOW,
            Header::HEADER_CONTENT_ENCODING,
            Header::HEADER_CONTENT_LANGUAGE,
            Header::HEADER_CONTENT_LENGTH,
            Header::HEADER_CONTENT_MD5,
            Header::HEADER_CONTENT_TYPE,
            Header::HEADER_LAST_MODIFIED,
        );

        $this->headers->removeHeader($removeHeaders);
    }

    /**
     * Adds a message.
     * @param zibo\library\message\Message $message
     */
    public function addMessage(Message $message) {
        $this->messages->add($message);
    }

    /**
     * Returns the messages.
     * @return zibo\library\message\MessageContainer a list of messages
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Sets the view of this response.
     * @param zibo\core\view\View $view The view
     * @return null
     */
    public function setView(View $view = null) {
        $this->view = $view;
    }

    /**
     * Returns the view of this response.
     * @return zibo\core\view\View The view
     */
    public function getView() {
        return $this->view;
    }

}