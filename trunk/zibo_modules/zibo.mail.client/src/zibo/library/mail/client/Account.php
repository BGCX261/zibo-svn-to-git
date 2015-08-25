<?php

namespace zibo\library\mail\client;

use zibo\library\mail\exception\MailException;
use zibo\library\Number;
use zibo\library\String;

/**
 * Data container of a mail account
 */
class Account {

    /**
     * No security constant
     * @var string
     */
    const SECURITY_NO_TLS = 'notls';

    /**
     * SSL security constant
     * @var string
     */
    const SECURITY_SSL = 'ssl';

    /**
     * TLS security constant
     * @var string
     */
    const SECURITY_TLS = 'tls';

    /**
     * IMAP type
     * @var string
     */
    const TYPE_IMAP = 'imap';

    /**
     * POP3 type
     * @var string
     */
    const TYPE_POP3 = 'pop3';

    /**
     * The host of the mail server
     * @var string
     */
    private $server;

    /**
     * The port of the mail server
     * @var integer
     */
    private $port;

    /**
     * The type of the mail server
     * @var string
     */
    private $type;

    /**
     * The security to use
     * @var string
     */
    private $security;

    /**
     * Flag to see if the certificate should be validated (only for secure connections)
     * @var boolean
     */
    private $willValidateCertificate;

    /**
     * The username to authenticate with the server
     * @var string
     */
    private $username;

    /**
     * The password to authenticate with the server
     * @var string
     */
    private $password;

    /**
     * Constructs a new client account
     * @param string $server The host of the server
     * @param string $username The username to authenticate with the server
     * @param string $password The password to authenticate with the server
     * @param string $port The port where the server is listening
     * @return null
     */
    public function __construct($server, $username = '', $password = '', $port = null) {
        $this->setServer($server);
        $this->setPort($port);
        $this->setType(self::TYPE_IMAP);
        $this->setSecurity(self::SECURITY_NO_TLS);
        $this->setWillValidateCertificate(true);
        $this->setUsername($username);
        $this->setPassword($password);
    }

    /**
     * Gets the reference of the server to use in PHP's imap functions
     * @return string
     */
    public function getServerReference() {
        $string = '{';
        $string .= $this->server;
        if ($this->port) {
            $string .= ':' . $this->port;
        }
        $string .= '/service=' . $this->type;
        $string .= '/' . $this->security;
        if ($this->willValidateCertificate) {
            $string .= '/validate-cert';
        } else {
            $string .= '/novalidate-cert';
        }
        $string .= '}';

        return $string;
    }

    /**
     * Sets the host of the mail server
     * @param string $server The host
     * @return null
     * @throws zibo\library\mail\exception\MailException when an invalid server provided
     */
    public function setServer($server) {
        if (String::isEmpty($server)) {
            throw new MailException('Could not set the server: provided server is empty');
        }

        $this->server = $server;
    }

    /**
     * Gets the host of the mail server
     * @return string
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * Sets the port where the server is listening
     * @param integer|null $port The port or null for the default port
     * @return null
     * @throws zibo\library\mail\exception\MailException when the provided port is invalid
     */
    public function setPort($port) {
        if ($port === null) {
            $this->port = null;
            return;
        }

        if (Number::isNegative($port)) {
            throw new MailException('Could not set the port: provided port is negative');
        }

        $this->port = $port;
    }

    /**
     * Gets the port where the server is listening
     * @return null|integer
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Sets the type of the connection (IMAP or POP3)
     * @param string $type
     * @return null
     * @throws zibo\library\mail\exception\MailException when the provided type is invalid
     */
    public function setType($type) {
        if ($type !== self::TYPE_IMAP && $type !== self::TYPE_POP3) {
            throw new MailException('Coult not set the type: provided type is invalid, try imap or pop3');
        }

        $this->type = $type;
    }

    /**
     * Gets the type of the connection
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the security of the connection (notls, tls or ssl)
     * @param string $security
     * @return null
     * @throws zibo\library\mail\exception\MailException when the provided security is invalid
     */
    public function setSecurity($security) {
        if ($security !== self::SECURITY_NO_TLS && $security !== self::SECURITY_TLS && $security !== self::SECURITY_SSL) {
            throw new MailException('Could not set the security: provided security is invalid, try notls, tls or ssl');
        }

        $this->security = $security;
    }

    /**
     * Gets the security of the connection
     * @return string
     */
    public function getSecurity() {
        return $this->security;
    }

    /**
     * Sets the flag to see if the client will validate the security certificate
     * @param boolean $flag True to validate, false to skip validation
     * @return null
     */
    public function setWillValidateCertificate($flag) {
        $this->willValidateCertificate = $flag;
    }

    /**
     * Gets the flag to see if the client will validate the security certificate
     * @return null
     */
    public function willValidateCertificate() {
        return $this->willValidateCertificate;
    }

    /**
     * Sets the username to authenticate with the mail server
     * @param string $username
     * @return null
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * Gets the username to authenticate with the mail server
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Sets the password to authenticate with the mail server
     * @param string $password
     * @return null
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Gets the password to authenticate with the mail server
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

}