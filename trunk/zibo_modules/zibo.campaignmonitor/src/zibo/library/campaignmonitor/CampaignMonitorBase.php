<?php

namespace zibo\library\campaignmonitor;

use zibo\library\campaignmonitor\exception\CampaignMonitorException;

use zibo\ZiboException;

// WARNING: this is needed to keep the socket from apparently hanging (even when it should be done reading)
// NOTE: using a timeout (SOCKET_TIMEOUT) that's passed when calling fsockopen. safer thing to do.
//ini_set('default_socket_timeout', 1);
define('SOCKET_TIMEOUT', 1);

/**
 * Connection with CampaignMonitor
 * @see http://www.campaignmonitor.com/api
 */
abstract class CampaignMonitorBase {

    /**
     * URL to submit the request to
     * @var string
     */
    const INVOKE_URL = 'http://api.createsend.com/api/api.asmx';

    /**
     * The soap action
     * @var string
     */
    const SOAP_ACTION = 'http://api.createsend.com/api/';

    /**
     * Request method get
     * @var string
     */
    const METHOD_GET = 'get';

    /**
     * Request method post
     * @var string
     */
    const METHOD_POST = 'post';

    /**
     * Request method soap
     * @var string
     */
    const METHOD_SOAP = 'soap';

    /**
     * Code for the invalid API error
     * @var int
     */
    const ERROR_CODE_INVALID_API = 100;

    /**
     * Message for the invalid API error
     * @var string
     */
    const ERROR_MESSAGE_INVALID_API = 'Invalid API Key';

    /**
     * Code for the invalid list id error
     * @var int
     */
    const ERROR_CODE_INVALID_LIST = 101;

    /**
     * Message for the invalid list id error
     * @var string
     */
    const ERROR_MESSAGE_INVALID_LIST = 'Invalid ListID';

    /**
     * Code for the invalid client id error
     * @var int
     */
    const ERROR_CODE_INVALID_CLIENT = 102;

    /**
     * Message for the invalid client id error
     * @var string
     */
    const ERROR_MESSAGE_INVALID_CLIENT = 'Invalid ClientID';

    /**
     * Code for the invalid campaign id error
     * @var int
     */
    const ERROR_CODE_INVALID_CAMPAIGN = 301;

    /**
     * Message for the invalid client id error
     * @var string
     */
    const ERROR_MESSAGE_INVALID_CAMPAIGN = 'Invalid CampaignID';

    /**
     * Priority for attributes when converting from xml to array
     * @var string
     */
    const PRIORITY_ATTRIBUTE = 'attribute';

    /**
     * Priority for tags when converting from xml to array
     * @var string
     */
    const PRIORITY_TAG = 'tag';

    /**
     * Key for the header option
     * @var string
     */
    const OPTION_HEADER = 'header';

    /**
     * Key for the parameters option
     * @var string
     */
    const OPTION_PARAMETERS = 'params';

    /**
     * The API key
     * @var string
     */
    protected $api;

    /**
     * The default campaign id
     * @var string
     */
    protected $campaignId;

    /**
     * The default client id
     * @var string
     */
    protected $clientId;

    /**
     * The default list id
     * @var string
     */
    protected $listId;

    /**
     * The request method to use
     * @var string
     */
    private $method;

    /**
     * Flag to see whether to use cURL
     * @var boolean
     */
    private $useCurl = true;

    /**
     * Flag to see if the cURL extension is installed
     * @var boolean
     */
    private $isCurlAvailable;

    /**
     * Constructs a new CampaignMonitor connection
     * @param string $api Your API key.
     * @param string $clientId The default ClientId you're going to work with.
     * @param string $campaignId The default CampaignId you're going to work with.
     * @param string $listId The default ListId you're going to work with.
     * @param string $method Determines request type. Values are either get, post, or soap.
     * @return null
     */
    public function __construct($api = null, $clientId = null, $campaignId = null, $listId = null, $method = self::METHOD_GET) {
        $this->api = $api;
        $this->clientId = $clientId;
        $this->campaignId = $campaignId;
        $this->listId = $listId;

        $this->setMethod($method);

        $this->isCurlAvailable = function_exists('curl_init') && function_exists('curl_setopt');
    }

    /**
     * Sets the method of the requests
     * @param string $method
     * @return null
     */
    public function setMethod($method) {
        if ($method != self::METHOD_GET && $method != self::METHOD_POST && $method != self::METHOD_SOAP) {
            throw new ZiboException('Provided method is invalid');
        }

        $this->method = $method;
    }

    /**
     * Gets the method used for the requests
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Gets the client id to use, if no client id is provided, the default as provided in the constructor will be used
     * @param string $clientId
     * @return string
     */
    protected function getClientId($clientId = null) {
        if ($clientId) {
            return $clientId;
        }

        return $this->clientId;
    }

    /**
     * Gets the campaign id to use, if no campaign id is provided, the default as provided in the constructor will be used
     * @param string $campaignId
     * @return string
     */
    protected function getCampaignId($campaignId = null) {
        if ($campaignId) {
            return $campaignId;
        }

        return $this->campaignId;
    }

    /**
     * Gets the list id to use, if no list id is provided, the default as provided in the constructor will be used
     * @param string $listId
     * @return string
     */
    protected function getListId($listId = null) {
        if ($listId) {
            return $listId;
        }

        return $this->listId;
    }

    /**
    * The direct way to make an API call. This allows developers to include new API
    * methods that might not yet have a wrapper method as part of the package.
    *
    * @param string $action The API call.
    * @param array $options An associative array of values to send as part of the request.
    * @param boolean $escape Set to true to escape the parameters in the provided options
    * @return array The parsed XML of the request.
    */
    protected function invoke($action = '', array $options = array(), $encode = false) {
        if (!$action) {
            return null;
        }

        $url = self::INVOKE_URL;

        // DONE: like facebook's client, allow for get/post through the file wrappers
        // if curl isn't available. (or maybe have curl-emulating functions defined
        // at the bottom of this script.)

        //$ch = curl_init();
        if (!array_key_exists(self::OPTION_HEADER, $options)) {
            $options[self::OPTION_HEADER] = array();
        }

        $options[self::OPTION_HEADER][] = 'User-Agent: CMBase URL Handler 1.5';

        $postData = '';
        $method = 'GET';

        if ($this->method == self::METHOD_SOAP) {
            $options[self::OPTION_HEADER][] = 'Content-Type: text/xml; charset=utf-8';
            $options[self::OPTION_HEADER][] = 'SOAPAction: "' . self::SOAP_ACTION . $action . '"';

            $postData = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
            $postData .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"";
            $postData .= " xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"";
            $postData .= " xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n";
            $postData .= "<soap:Body>\n";
            $postData .= '  <' . $action . ' xmlns="' . self::SOAP_ACTION . '">' . "\n";
            $postData .= "      <ApiKey>{$this->api}</ApiKey>\n";

            if (isset($options[self::OPTION_PARAMETERS])) {
                $postData .= $this->array2xml($options[self::OPTION_PARAMETERS], "\t\t", $encode);
            }

            $postData .= "  </{$action}>\n";
            $postData .= "</soap:Body>\n";
            $postData .= "</soap:Envelope>";

            $method = 'POST';
        } else {
            $postData = "ApiKey=" . $this->api;
            $url .= "/{$action}";

            // NOTE: since this is GET, the assumption is that params is a set of simple key-value pairs.
            if (isset($options[self::OPTION_PARAMETERS])) {
                foreach ($options[self::OPTION_PARAMETERS] as $key => $value) {
                    if ($encode) {
                        $value = utf8_encode($value);
                    }
                    $postData .= '&' . $key . '=' .rawurlencode($value);
                }
            }

            if ($this->method == self::METHOD_GET) {
                $url .= '?' . $postData;
                $postData = '';
            } else {
                $options[self::OPTION_HEADER][] = 'Content-Type: application/x-www-form-urlencoded';
                $method = 'POST';
            }
        }

        $result = '';

        // WARNING: using fopen() does not recognize stream contexts in PHP 4.x, so
        // my guess is using fopen() in PHP 4.x implies that POST is not supported
        // (otherwise, how do you tell fopen() to use POST?). tried fsockopen(), but
        // response time was terrible. if someone has more experience with working
        // directly with streams, please troubleshoot that.
        // NOTE: fsockopen() needs a small timeout to force the socket to close.
        // it's defined in SOCKET_TIMEOUT.

        // preferred method is curl, only if it exists and $this->useCurl is true.
        if ($this->useCurl && $this->isCurlAvailable) {
            $ch = curl_init();
            if ($this->method != self::METHOD_GET) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options[self::OPTION_HEADER]);
            curl_setopt($ch, CURLOPT_HEADER, false);

            // except for the response, all other information will be stored when debugging is on.
            $result = curl_exec($ch);

            curl_close($ch);
        } else {
            // 'header' is actually the entire HTTP payload. as such, you need
            // Content-Length header, otherwise you'll get errors returned/emitted.

            $postLen = strlen($postData);
            $streamContext = array(
                'method' => $method,
                'header' => implode("\n", $options[self::OPTION_HEADER])
                            . "\nContent-Length: " . $postLen
                            . "\n\n" . $postData
            );

            $context = stream_context_create(array('http' => $streamContext));
            $resource = fopen($url, 'r', false, $context);
            ob_start();
            fpassthru($resource);
            fclose($resource);

            $result = ob_get_clean();
        }

        if (!$result) {
            return null;
        }

        if ($this->method != self::METHOD_SOAP) {
            $result = $this->xml2array($result);
        } else {
            $result = $this->xml2array($result, '/soap:Envelope/soap:Body');
            if (is_array($result)) {
                $result = $result[$action . 'Response'][$action . 'Result'];
            }
        }

        $this->checkResultForErrors($result);

        return $result;
    }

    /**
     * Checks the result for errors, if errors found, an exception will be thrown for it
     * @param mixed $result Result of the invokation
     * @return null
     * @throws zibo\library\campaignmonitor\exception\CampaignMonitorException when an error is found in the result
     */
    protected function checkResultForErrors($result, $key = 'Result') {
        if (isset($result[$key]['Code'])) {
            switch ($result[$key]['Code']) {
                case self::ERROR_CODE_INVALID_API:
                case self::ERROR_CODE_INVALID_LIST:
                case self::ERROR_CODE_INVALID_CLIENT:
                case self::ERROR_CODE_INVALID_CAMPAIGN:
                    throw new CampaignMonitorException($result[$key]['Message'], $result[$key]['Code']);
            }

            return;
        }

        if ($result == self::ERROR_CODE_INVALID_API . ' ' . self::ERROR_MESSAGE_INVALID_API) {
            throw new CampaignMonitorException(self::ERROR_MESSAGE_INVALID_API, self::ERROR_CODE_INVALID_API);
        } elseif ($result == self::ERROR_CODE_INVALID_LIST . ' ' . self::ERROR_MESSAGE_INVALID_LIST) {
            throw new CampaignMonitorException(self::ERROR_MESSAGE_INVALID_LIST, self::ERROR_CODE_INVALID_LIST);
        } elseif ($result == self::ERROR_CODE_INVALID_CLIENT . ' ' . self::ERROR_MESSAGE_INVALID_CLIENT) {
            throw new CampaignMonitorException(self::ERROR_MESSAGE_INVALID_CLIENT, self::ERROR_CODE_INVALID_CLIENT);
        } elseif ($result == self::ERROR_CODE_INVALID_CAMPAIGN . ' ' . self::ERROR_MESSAGE_INVALID_CAMPAIGN) {
            throw new CampaignMonitorException(self::ERROR_MESSAGE_INVALID_CLIENT, self::ERROR_CODE_INVALID_CLIENT);
        }
    }

    /**
     * Convert the given XML $contents into an array. Based on code from http://www.bin-co.com/php/scripts/xml2array/
     * @param string $contents The XML to be converted.
     * @param string $root The path of the root element within the XML at which conversion should occur.
     * @param string $charset The character set to use.
     * @param boolean $includeAttributes If this is true the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
     * @param string $priority Can be 'tag' or 'attribute'. This will change the structure of the resulting array. For 'tag', the tags are given more importance.
     * @return array Array representing the XML $contents passed in
     * @throws zibo\ZiboException when the provided priority is invalid
     */
    private function xml2array($contents, $root = '/', $charset = 'utf-8', $includeAttributes = false, $priority = self::PRIORITY_TAG) {
        if ($priority != self::PRIORITY_TAG && $priority != self::PRIORITY_ATTRIBUTE) {
            throw new ZiboException('Provided priority is invalid, try PRIORITY_TAG or PRIORITY_ATTRIBUTE');
        }

        if (!$contents) {
            return array();
        }

        if (!function_exists('xml_parser_create')) {
            return array();
        }

        // Get the PHP XML parser
        $parser = xml_parser_create($charset);

        // Attempt to find the last tag in the $root path and use this as the
        // start/end tag for the process of extracting the xml
        // Example input: '/soap:Envelope/soap:Body'

        // Toggles whether the extraction of xml into the array actually occurs
        $extractionEnabled = true;
        $startAndEndElementName = '';

        $rootElements = explode('/', $root);
        if ($rootElements != false && !empty($rootElements)) {
            $startAndEndElementName = trim(end($rootElements));
            if (!empty($startAndEndElementName)) {
                $extractionEnabled = false;
            }
        }

        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xmlValues);
        xml_parser_free($parser);

        if (!$xmlValues) {
            return array();
        }

        $xmlArray = array();
        $parents = array();
        $openedTags = array();
        $array = array();

        $current = &$xmlArray; // Reference

        // Go through the tags.
        $repeatedTagIndex = array(); // Multiple tags with same name will be turned into an array
        foreach($xmlValues as $data) {
            unset($attributes, $value); // Remove existing values, or there will be trouble

            // This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data);

            if (!empty($startAndEndElementName) && $tag == $startAndEndElementName) {
                // Start at the next element (if looking at the opening tag),
                // or don't process any more elements (if looking at the closing tag)...
                $extractionEnabled = !$extractionEnabled;
                continue;
            }

            if (!$extractionEnabled) {
                continue;
            }

            $result = array();
            $attributesData = array();

            if (isset($value)) {
                if ($priority == self::PRIORITY_TAG) {
                    $result = $value;
                } else {
                    $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
                }
            }

            // Set the attributes too.
            if (isset($attributes) && $includeAttributes) {
                foreach($attributes as $attributeName => $attributeValue) {
                    if ($priority == self::PRIORITY_TAG) {
                        $attributes_data[$attributeName] = $attributeValue;
                    } else {
                        $result['attr'][$attributeName] = $attributeValue; // Set all the attributes in a array called 'attr'
                    }
                }
            }

            // See tag status and do the needed.
            if ($type == "open") { // The starting of the tag '<tag>'
                $parent[$level - 1] = &$current;

                if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if ($attributesData) {
                        $current[$tag . '_attr'] = $attributesData;
                    }

                    $repeatedTagIndex[$tag . '_' . $level] = 1;
                    $current = &$current[$tag];
                } else { // There was another element with the same tag name
                    if (isset($current[$tag][0])) { // If there is a 0th element it is already an array
                        $current[$tag][$repeatedTagIndex[$tag . '_' . $level]] = $result;
                        $repeatedTagIndex[$tag . '_' . $level]++;
                    } else { // This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag], $result); // This will combine the existing item and the new item together to make an array
                        $repeatedTagIndex[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr'])) { // The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }
                    }

                    $lastItemIndex = $repeatedTagIndex[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$lastItemIndex];
                }
            } elseif ($type == "complete") { // Tags that ends in 1 line '<tag />'
                // See if the key is already taken.
                if (!isset($current[$tag])) { // New Key
                    // Don't insert an empty array - we don't want it!
                    if (!(is_array($result) && empty($result))) {
                        $current[$tag] = $result;
                    }

                    $repeatedTagIndex[$tag . '_' . $level] = 1;

                    if ($priority == self::PRIORITY_TAG && $attributesData) {
                        $current[$tag . '_attr'] = $attributesData;
                    }
                } else { // If taken, put all things inside a list(array)
                    if (isset($current[$tag][0]) && is_array($current[$tag])) { // If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeatedTagIndex[$tag . '_' . $level]] = $result;

                        if ($priority == self::PRIORITY_TAG && $includeAttributes && $attributesData) {
                            $current[$tag][$repeatedTagIndex[$tag . '_' . $level] . '_attr'] = $attributesData;
                        }

                        $repeatedTagIndex[$tag . '_' . $level]++;
                    } else { // If it is not an array...
                        $current[$tag] = array($current[$tag], $result); // ...Make it an array using using the existing value and the new value
                        $repeatedTagIndex[$tag . '_' . $level] = 1;

                        if ($priority == self::PRIORITY_TAG && $includeAttributes) {
                            if (isset($current[$tag . '_attr'])) { // The attribute of the last(0th) tag must be moved as well
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributesData) {
                                $current[$tag][$repeatedTagIndex[$tag . '_' . $level] . '_attr'] = $attributesData;
                            }
                        }

                        $repeatedTagIndex[$tag . '_' . $level]++; // 0 and 1 index is already taken
                    }
                }
            } elseif ($type == 'close') { // End of tag '</tag>'
                $current = &$parent[$level - 1];
            }
        }

        return $xmlArray;
    }

    /**
    * Converts an array to XML. This is the inverse to xml2array(). Values
    * are automatically escaped with htmlentities(), so you don't need to escape
    * values ahead of time. If you have, just set the third parameter to false.
    * This is an all-or-nothing deal.
    *
    * @param mixed $arr The associative to convert to an XML fragment
    * @param string $indent (Optional) Starting identation of each element
    * @param string $escape (Optional) Determines whether or not to escape a text node.
    * @return string An XML fragment.
    */
    private function array2xml($array, $indent = '', $escape = true) {
        $buffer = '';

        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $buffer .= $indent . "<" . $key . ">" . ($escape ? utf8_encode( htmlspecialchars($value)) : $value) . "</" . $key . ">\n";
                continue;
            }

            if (isset($value[0])) {
                foreach ($value as $innerValue) {
                    if (is_array($innerValue)) {
                        $buffer .= $indent . "<" . $key . ">\n" . $this->array2xml($innerValue, $indent . "\t", $escape) . $indent . "</" . $key . ">\n";
                    } else {
                        $buffer .= $indent . "<" . $key . ">" . ($escape ? utf8_encode(htmlspecialchars($innerValue)) : $innerValue ) . "</" . $key . ">\n";
                    }
                }
            } else {
                $buffer .= $indent . "<" . $key . ">\n" . $this->array2xml($value, $indent . "\t", $escape) . $indent . "</" . $key .">\n";
            }
        }

        return $buffer;
    }

}