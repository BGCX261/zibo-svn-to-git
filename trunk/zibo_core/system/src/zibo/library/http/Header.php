<?php

namespace zibo\library\http;

use zibo\library\cache\AbstractCache;
use zibo\library\String;

use zibo\ZiboException;

use \DateTime;
use \DateTimeZone;

/**
 * Represents a HTTP header, as a name - value pair.
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
 */
class Header {

    /**
     * Name of the allow header
     * @var string
     */
    const HEADER_ALLOW = 'Allow';

    /**
     * Name of the accept header
     * @var string
     */
    const HEADER_ACCEPT = 'Accept';

    /**
     * Name of the accept language header
     * @var string
     */
    const HEADER_ACCEPT_LANGUAGE = 'Accept-Language';

    /**
     * Name of the accept charset header
     * @var string
     */
    const HEADER_ACCEPT_CHARSET = 'Accept-Charset';

    /**
     * Name of the accept encoding header
     * @var string
     */
    const HEADER_ACCEPT_ENCODING = 'Accept-Encoding';

    /**
     * Header name for HTTP authentication
     * @var string
     */
    const HEADER_AUTHENTICATE = 'WWW-Authenticate';

    /**
     * Name of the cache control header
     * @var string
     */
    const HEADER_CACHE_CONTROL = 'Cache-Control';

    /**
     * Name of the content encoding header
     * @var string
     */
    const HEADER_CONTENT_ENCODING = 'Content-Encoding';

    /**
     * Name of the content language header
     * @var string
     */
    const HEADER_CONTENT_LANGUAGE = 'Content-Language';

    /**
     * Name of the content length header
     * @var string
     */
    const HEADER_CONTENT_LENGTH = 'Content-Length';

    /**
     * Name of the content MD5 header
     * @var string
     */
    const HEADER_CONTENT_MD5 = 'Content-MD5';

    /**
     * Name of the content type header
     * @var string
     */
    const HEADER_CONTENT_TYPE = 'Content-Type';

    /**
     * Name of the date header
     * @var string
     */
    const HEADER_DATE = 'Date';

    /**
     * Name of the etag header
     * @var string
     */
    const HEADER_ETAG = 'ETag';

    /**
     * Name of the expires header
     * @var string
     */
    const HEADER_EXPIRES = 'Expires';

    /**
     * Header name for last modified header
     * @var string
     */
    const HEADER_LAST_MODIFIED = 'Last-Modified';

    /**
     * Header name for Location, used for redirects
     * @var string
     */
    const HEADER_LOCATION = 'Location';

    /**
     * Name of the if modified since header
     * @var string
     */
    const HEADER_IF_MODIFIED_SINCE = 'If-Modified-Since';

    /**
     * Name of the if none match header
     * @var string
     */
    const HEADER_IF_NONE_MATCH = 'If-None-Match';

    /**
     * Name of the referer header
     * @var string
     */
    const HEADER_REFERER = 'Referer';

    /**
     * Name of the request with header
     * @var string
     */
    const HEADER_REQUEST_WITH = 'X-Requested-With';

    /**
     * Name of the user agent header
     * @var string
     */
    const HEADER_USER_AGENT = 'User-Agent';

    /**
     * Name of the header
     * @var string
     */
    private $name;

    /**
     * Value of the header
     * @var string
     */
    private $value;

    /**
     * Construct this header
     * @param string $name
     * @param string $value
     * @return null
     */
    public function __construct($name, $value) {
        $this->setName($name);
        $this->setValue($value);
    }

    /**
     * Returns the header formatted as a string
     *
     * The returned string is ready to be used by {@link header() PHP's header function}
     * @return string
     */
    public function __toString() {
        return $this->name . ': ' . $this->value;
    }

    /**
     * Checks if the provided value represents the same header as this
     * @param mixed $value The value to check
     * @return boolean True if the provided value is the same, false otherwise
     */
    public function equals($value) {
        if (!$value instanceof self) {
            return false;
        }

        if ($value->name != $this->name || $value->value != $this->value) {
            return false;
        }

        return true;
    }

    /**
     * Sets the name of this header
     * @param string $name The name of the header
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     */
    private function setName($name) {
        $this->name = self::parseName($name);
    }

    /**
     * Get the name of this header
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name of this header
     * @param string $name The name of the header
     * @return null
     * @throws zibo\ZiboException when the provided name is empty or invalid
     */
    private function setValue($value) {
        if (!String::isString($value)) {
            throw new ZiboException('Provided value of the header is invalid or empty');
        }

        $this->value = $value;
    }

    /**
     * Get the value of this header
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Since header names are case insensitive, this method can be used to
     * unify the name of a header to a specific format (eg Content-Length)
     * @param string $name The header name to unify
     * @return string Unified header name
     * @throws ZiboException when the provided name is not a string or is empty
     */
    public static function parseName($name) {
        if (!String::isString($name, String::NOT_EMPTY)) {
            throw new ZiboException('Provided name of the header is invalid or empty');
        }

        return str_replace(' ', '-', ucwords(strtolower(str_replace(array('-', '_'), ' ', $name))));
    }

    /**
     * Converts a timestamp to a RFC2822 date and vica versa.
     * @param integer|string $date A timestamp or a RFC2822 formatted date
     * @return integer|string A RFC2822 formatted date from the timestamp or a
     * timestamp from the RFC2822 formatted date
     * @throws zibo\ZiboException when the provided date is not a string or
     * not numeric
     * @throws zibo\ZiboException when the formatted date could not be converted
     * to a timestamp
     */
    public static function parseTime($time) {
        if (!String::isString($time, String::NOT_EMPTY)) {
            throw new ZiboException('Provided date should be a string or numeric');
        }

        $dateFormat = 'D, d M Y H:i:s';

        if (is_numeric($time)) {
            // timestamp
            $converted = new DateTime('@' . $time);
            $converted->setTimezone(new DateTimeZone('UTC'));
            $converted = $converted->format($dateFormat) . ' GMT';
        } else {
            // header formatted date
            $converted = DateTime::createFromFormat($dateFormat . ' e', $time);
            if ($converted === false) {
                throw new ZiboException('Could not convert ' . $time);
            }

            $converted = $converted->getTimestamp();
        }

        return $converted;
    }

    /**
     * Parses a value of a accept header in the preferable order
     *
     * Taken from Symfony2
     * @param string $header Accept header value
     * @return array Array with the value as key and the preferable order as value
     */
    public static function parseAccept($header) {
        if (!String::isString($header, String::NOT_EMPTY)) {
            throw new ZiboException('Provided accept header value is invalid or empty');
        }

        $values = array();

        foreach (array_filter(explode(',', $header)) as $value) {
            // Cut off any q-value that might come after a semi-colon
            if (preg_match('/;\s*(q=.*$)/', $value, $match)) {
                $q = (float) substr(trim($match[1]), 2);
                $value = trim(substr($value, 0, -strlen($match[0])));
            } else {
                $q = 1;
            }

            if (0 < $q) {
                $values[trim($value)] = $q;
            }
        }

        arsort($values);
        reset($values);

        return $values;
    }

    /**
     * Parses the ETags of a if match or if none match header
     * @param string $header If match of if none match header value
     * @return array Array with the ETag as key and weak flag as value
     * @throws zibo\ZiboException when the provided header value is empty or invalid
     */
    public static function parseIfMatch($header) {
        if (!String::isString($header, String::NOT_EMPTY)) {
            throw new ZiboException('Provided if match header value is invalid or empty');
        }

        $result = array();

        $eTags = explode(',', $header);
        foreach ($eTags as $index => $eTag) {
            $eTag = trim($eTag);

            $weak = false;
            if (substr($eTag, 0, 2) == 'W/') {
                $weak = true;
                $eTag = substr($eTag, 2);
            }

            $eTagLength = strlen($eTag);
            if ($eTag[0] == '"' && $eTag[$eTagLength - 1] == '"') {
                $eTag = substr($eTag, 1, -1);
            }

            $result[$eTag] = $weak;
        }

        return $result;
    }

}