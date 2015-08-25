<?php

namespace zibo\library\security\authenticator;

use zibo\library\Session;

use \Exception;

/**
 * Authenticator with authentication storage in the session
 */
class SessionAuthenticator extends AbstractAuthenticator {

    /**
     * Session name for the switched user name
     * @var string
     */
    const SESSION_SWITCHED_USERNAME = 'security.username.switched';

    /**
     * Session name for the user name
     * @var string
     */
    const SESSION_USERNAME = 'security.username';

    /**
     * Session name for the authentication string
     * @var string
     */
    const SESSION_AUTHENTICATION_STRING = 'security.authentication';

    /**
     * Instance of the session
     * @var zibo\library\Session
     */
    private $session;

    /**
     * The number of visitors currently visiting the site
     * @var integer
     */
    private $numVisitors;

    /**
     * The number of users currently visiting the site
     * @var integer
     */
    private $numUsers;

    /**
     * The users currently visiting the site
     * @var array
     */
    private $currentUsers;

    /**
     * Constructs a new authenticator
     * @return null
     */
    public function __construct() {
        parent::__construct();

        $this->session = Session::getInstance();

        $this->numVisitors = false;
        $this->numUsers = false;
        $this->currentUsers = false;
    }

    /**
     * Gets the number of current visitors. This number includes the current users.
     * @return integer
     */
    public function getNumVisitors() {
        if ($this->numVisitors === false) {
            $this->calculateVisitorStats();
        }

        return $this->numVisitors;
    }

    /**
     * Gets the number of current users.
     * @return integer
     */
    public function getNumUsers() {
        if ($this->numUsers === false) {
            $this->calculateVisitorStats();
        }

        return $this->numUsers;
    }

    /**
     * Gets the usernames of the current users
     * @return array Array with usernames
     */
    public function getCurrentUsers() {
        if ($this->currentUsers === false) {
            $this->calculateVisitorStats();
        }

        return $this->currentUsers;
    }

    /**
     * Calculates the number of current visitors and users
     * @return null
     */
    private function calculateVisitorStats() {
        $this->numUsers = 0;
        $this->numVisitors = 0;
        $this->currentUsers = array();

        $sessionPath = $this->session->getPath();
        $sessionLifeTime = $this->session->getLifeTime();

        $sessionTimeOutTime = time() - $sessionLifeTime;

        $sessionFiles = $sessionPath->read();
        foreach ($sessionFiles as $sessionFile) {
            if ($sessionFile->getModificationTime() < $sessionTimeOutTime || !$sessionFile->isReadable()) {
                continue;
            }

            $content = $sessionFile->read();
            if (!$content) {
                continue;
            }

            $content = substr($content, strlen(Session::SESSION_NAME) + 1);
            try {
                $content = unserialize($content);

                $this->numVisitors++;

                if (array_key_exists(self::SESSION_USERNAME, $content)) {
                    $username = $content[self::SESSION_USERNAME];

                    $this->numUsers++;
                    $this->currentUsers[$username] = $username;
                }
            } catch (Exception $exception) {

            }
        }

        sort($this->currentUsers);
    }

    /**
     * Save the authentification to the session
     * @param string $username Username
     * @param string $authentificationString Authentification string
     * @return null
     */
    protected function setAuthentification($username, $authentificationString = null) {
        $this->session->set(self::SESSION_USERNAME, $username);
        $this->session->set(self::SESSION_AUTHENTICATION_STRING, $authentificationString);
    }

    /**
     * Sets the switched username to the storage
     * @param $username string Username of the user to switch to
     * @return null
     */
    protected function setSwitchedUsername($username) {
        return $this->session->set(self::SESSION_SWITCHED_USERNAME, $username);
    }

    /**
     * Gets the stored username from the session
     * @return string Stored username if set, null otherwise
     */
    protected function getAuthentificationUsername() {
        return $this->session->get(self::SESSION_USERNAME);
    }

    /**
     * Gets the stored authentification string from the session
     * @return string Stored authentification string if set, null otherwise
     */
    protected function getAuthentificationString() {
        return $this->session->get(self::SESSION_AUTHENTICATION_STRING);
    }

    /**
     * Gets the stored username from the session
     * @return string Stored username if set, null otherwise
     */
    protected function getSwitchedUsername() {
        return $this->session->get(self::SESSION_SWITCHED_USERNAME);
    }

    /**
     * Clears the authentification in the storage. When there is a user switch, only the authentication of the switched user should be cleared.
     * If there is no user switch, all authentication data should be cleared
     * @return null
     */
    protected function clearAuthentification() {
        if ($this->getSwitchedUsername()) {
            $this->session->set(self::SESSION_SWITCHED_USERNAME, null);
        } else {
            $this->session->set(self::SESSION_USERNAME, null);
            $this->session->set(self::SESSION_AUTHENTICATION_STRING, null);
        }
    }

}