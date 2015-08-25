<?php

namespace zibo\library\soap\curl;

use \SoapClient;
use \SoapFault;

/**
 * SOAP client which uses the cURL (Client URL Library) PHP extension
 * to make its requests. This allows you to use some additional features
 * that cURL has (to name one: NTLM authentication)
 */
class Client extends SOAPClient {

    /**
     * additional cURL options
     *
     * @var array
     */
    protected $curlOptions = array();

    /**
     *
     * @param <type> $wsdl
     * @param <type> $options
     */
    public function __construct($wsdl, $options = array()) {
        if (array_key_exists('curl', $options)) {
            $this->curlOptions = $options['curl'];
            unset($options['curl']);
        }

        parent::__construct($wsdl, $options);
    }

    /**
     * Performs a SOAP request
     *
     * @param string $request the xml soap request
     * @param string $location the url to request
     * @param string $action the soap action.
     * @param integer $version the soap version
     * @param integer $one_way
     * @return string the xml soap response.
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $headers = array(
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "' . $action . '"',
        );

        $ch = curl_init($location);

        $options = array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => $request,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_HEADERFUNCTION  => array($this, 'curlHeader'),
            CURLINFO_HEADER_OUT     => true,
        );

        $options = $this->curlOptions + $options;

        curl_setopt_array($ch, $options);

        $this->__last_response_headers = NULL;
        $response = curl_exec($ch);

        if (curl_errno($ch) !== 0) {
            throw new SoapFault('Sender', curl_error($ch));
        }

        if (isset($this->trace) && $this->trace) {
            $info = curl_getinfo($ch);
            if (array_key_exists('request_header', $info)) {
                $this->__last_request_headers = rtrim($info['request_header'], "\r\n");
            }
        }

        return $response;
    }

    protected function curlHeader($ch, $string) {
        if (isset($this->trace) && $this->trace) {
            $header = rtrim($string, "\r\n");
            if ($header != '') {
                if ($this->__last_response_headers) {
                    $this->__last_response_headers .= "\n";
                }
                $this->__last_response_headers .= $header;
            }
        }

        return strlen($string);
    }
}