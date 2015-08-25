<?php

namespace zibo\library\jira;

use zibo\library\Callback;

use \Exception;
use \SoapClient;

/**
 * Jira webservices client
 * @see http://docs.atlassian.com/software/jira/docs/api/rpc-jira-plugin/latest/com/atlassian/jira/rpc/soap/JiraSoapService.html
 */
class JiraClient {

    /**
     * Prefix to the base URL for the WSDL of the Jira webservices
     * @var string
     */
    const URL_PREFIX_WSDL = '/rpc/soap/jirasoapservice-v2?wsdl';

    /**
     * The base URL of the Jira server
     * @var string
     */
    protected $server;

    /**
     * The username of the Jira user
     * @var string
     */
    protected $username;

    /**
     * The password of the Jira user
     * @var string
     */
    protected $password;

    /**
     * Authentication token of the Jira client
     * @var string
     */
    protected $token;

    /**
     * The SOAP client used for communication with the Jira server
     * @var SoapClient
     */
    protected $client;

    /**
     * Constructs a new Jira client
     * @return null
     */
    public function __construct($server, $username, $password) {
        $this->server = rtrim($server, '/');
        $this->username = $username;
        $this->password = $password;
        $this->token = null;

        $this->client = new SoapClient($this->server . self::URL_PREFIX_WSDL);
    }

    /**
     * Makes sure the client is logged out before destructing
     * @return null
     */
    public function __destruct() {
        try {
            $this->logout();
        } catch (Exception $exception) {
            // destructor, don't do nothing on error
        }
    }

    /**
     * Gets the URL of the Jira server
     * @return string
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * Makes sure the Jira user is logged in and a token is obtained
     * @return null
     */
    protected function login() {
        if ($this->token) {
            return;
        }

        $this->token = $this->client->login($this->username, $this->password);
    }

    /**
     * Logout the current user
     * @return null
     */
    protected function logout() {
        if ($this->token) {
            $this->client->logout($this->token);
        }
    }

    /**
     * Invokes a method on the Soap client
     * @param string $name Name of the method
     * @param array $arguments The arguments for the method, the authentication token will be prepended to this array before passing it to the Soap client
     * @return mixed The result of the remote method
     */
    public function __call($name, $arguments) {
        $this->login();

        array_unshift($arguments, $this->token);

        $callback = new Callback(array($this->client, $name));
        return $callback->invokeWithArrayArguments($arguments);
    }

}