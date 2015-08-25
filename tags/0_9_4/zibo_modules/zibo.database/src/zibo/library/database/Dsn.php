<?php

namespace zibo\library\database;

use zibo\library\database\exception\DatabaseException;
use zibo\library\validation\validator\DsnValidator;
use zibo\library\String;

/**
 * Definition of a Database Source Name
 */
class Dsn {

    /**
     * The full DSN
     * @var string
     */
    private $dsn;

    /**
     * The protocol for the database
     * @var string
     */
    private $protocol;

    /**
     * Username to connect to the database
     * @var string
     */
    private $username;

    /**
     * Password to connect to the database
     * @var string
     */
    private $password;

    /**
     * Hostname or ip address of the server of the database
     * @var string
     */
    private $host;

    /**
     * Port of the server of the database
     * @var int
     */
    private $port;

    /**
     * Name of the database
     * @var string
     */
    private $database;

    /**
     * The DSN without the protocol
     * @var string
     */
    private $path;

    /**
     * Constructs a new DSN
     * @param string $dsn String of the database source name
     * @return null
     * @throws zibo\ZiboException when the provided DSN is not a string
     * @throws zibo\library\database\exception\DatabaseException when the provided DSN is empty or invalid
     */
    public function __construct($dsn) {
        if (String::isEmpty($dsn)) {
            throw new DatabaseException('Provided dsn string is empty');
        }

        $validator = new DsnValidator();
        if (!$validator->isValid($dsn)) {
            throw new DatabaseException('Invalid dsn string provided: ' . $dsn);
        }

        $this->parseDsn($dsn);
    }

    /**
     * Parses the given dsn in the fields of this definition
     * @param string $dsn String of the database source name
     * @return null
     */
    private function parseDsn($dsn) {
        $this->dsn = $dsn;

        list($this->protocol, $token) = explode('://', $dsn);

        if (strpos($token, '@') !== false) {
            list($login, $token) = explode('@', $token);

            if (strpos($login, ':') !== false) {
                list($this->username, $this->password) = explode(':', $login);
            } else {
                $this->username = $login;
            }
        }

        $this->path = $token;

        if (substr_count($token, '/') > 1) {
            $tokens = explode('/', $token);

            $token = array_pop($tokens);
            $this->database = $token;
        } else {
            list($this->host, $this->database) = explode('/', $token);
        }

        if (strpos($this->host, ':') !== false) {
            list($this->host, $this->port) = explode(':', $this->host);
        }
    }

    /**
     * Gets the DSN string
     * @return string DSN string
     */
    public function __toString() {
        return $this->dsn;
    }

    /**
     * Gets the database protocol used to connect to the server
     * @return string Database protocol (eg. mysql)
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * Gets the username of the database user
     * @return string Username
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Gets the password of the database user
     * @return string Password
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Gets the hostname or IP address of the server
     * @return string Hostname or IP address
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Gets the port to connect to
     * @return string Port to connect to
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Gets the name of the database
     * @return string Name of the database
     */
    public function getDatabase() {
        return $this->database;
    }

    /**
     * Gets the DSN without the protocol. Usefull for file data sources.
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

}