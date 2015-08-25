<?php

namespace zibo\library\security\authenticator;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\security\exception\AuthenticationException;
use zibo\library\security\exception\SecurityException;
use zibo\library\security\exception\UsernameAuthenticationException;
use zibo\library\security\model\SecurityModel;
use zibo\library\security\model\User;
use zibo\library\security\SecurityManager;
use zibo\library\Session;

/**
 * Simple HTTP digest authenticator to wrap around another authenticator
 *
 * Users which are stored before using this authenticator will not be able to authenticate themselves.
 * This is due to hashed passwords of the security model.
 *
 * This authenticator hooks into the security model and stores the A1 part of the valid digest
 * response in a file per user. If the file is not there, or the realm has changed, authentication
 * will fail
 *
 * @see http://www.faqs.org/rfcs/rfc2617
 */
class HttpAuthenticator implements Authenticator {

    /**
     * Configuration key for the realm
     * @var string
     */
    const CONFIG_REALM = 'security.realm';

    /**
     * The name of the session variable to store the nonce
     * @var string
     */
    const SESSION_NONCE = 'security.nonce';

    /**
     * The path to store the user digest
     * @var string
     */
    const PATH_DIGEST = 'application/data/security/digest';

    /**
     * The wrapped authenticator
     * @var Authenticator
     */
    private $authenticator;

    /**
     * The realm for the authentication
     * @var string
     */
    private $realm;

    /**
     * The nonce of the authentication
     * @var string
     */
    private $nonce;

    /**
     * The authenticated user
     * @var zibo\library\security\model\User
     */
    private $user;

    /**
     * Constructs a new authenticator
     * @param Authenticator $authenticator The wrapped authenticator
     * @return null
     */
    public function __construct(Authenticator $authenticator) {
        $zibo = Zibo::getInstance();
        $request = $zibo->getRequest();

        $this->authenticator = $authenticator;
        $this->user = false;

        $this->realm = $zibo->getConfigValue(self::CONFIG_REALM, $request->getBaseUrl());

        $this->initNonce();

        $zibo->registerEventListener(User::EVENT_PASSWORD_UPDATE, array($this, 'onUserPasswordUpdate'));
    }

    /**
     * Sets the security model to the authenticator
     * @param zibo\library\security\model\SecurityModel $model The security model
     * @return null
     */
    public function setSecurityModel(SecurityModel $model = null) {
        $this->authenticator->setSecurityModel($model);
    }

    /**
     * Gets the security model of the authenticator
     * @return zibo\library\security\model\SecurityModel $model The security model
     */
    public function getSecurityModel() {
        return $this->authenticator->getSecurityModel();
    }

    /**
     * Hook with the security model used to store A1 of the
     * @param $user
     */
    public function onUserPasswordUpdate($user, $password) {
        $username = $user->getUserName();
        $a1 = md5($username . ':' . $this->realm . ':' . $password);

        $a1File = $this->getDigestA1File($username);
        $a1File->getParent()->create();
        $a1File->write($a1);
    }

    /**
     * Switch to the provided user to test it's permissions. When logging out, the user before switching will be the current user
     * @param string $username The username of the user to switch to
     * @return null
     * @throws zibo\library\security\exception\SecurityException when the user does not exist
     */
    public function switchUser($username) {
        $this->user = $this->authenticator->switchUser($username);
    }

    /**
     * Login a user
     * @param string $username
     * @param string $password
     * @return zibo\library\security\model\User The user if the login succeeded
     * @throws zibo\library\security\exception\AuthenticationException when the user could not be authenticated
     */
    public function login($username, $password) {
        $this->user = $this->authenticator->login($username, $password);

        return $this->user;
    }

    /**
     * Logout the current user
     * @return null
     */
    public function logout() {
        $this->authenticator->logout();

        $this->user = false;

        $this->refreshNonce();
    }

    /**
     * Gets the current user.
     * @return zibo\library\security\model\User User instance if a user is logged in, null otherwise
     */
    public function getUser() {
        if ($this->user !== false) {
            return $this->user;
        }

        $this->user = $this->authenticator->getUser();
        if ($this->user) {
            return $this->user;
        }

        if (!array_key_exists('PHP_AUTH_DIGEST', $_SERVER) || empty($_SERVER['PHP_AUTH_DIGEST'])) {
            return $this->user;
        }

        $digest = $this->parseDigest($_SERVER['PHP_AUTH_DIGEST']);
        if (!$digest) {
            return $this->user;
        }

        $validResponse = $this->generateValidResponse($digest);

        if ($digest['response'] == $validResponse) {
            $securityModel = $this->authenticator->getSecurityModel();

            $user = $securityModel->getUserByUsername($digest['username']);
            if (!$user || ($user && !$user->isUserActive())) {
                $this->user = null;
            } else {
                $this->user = $this->authenticator->setUser($user);
            }
        } else {
            $this->user = null;
        }

        return $this->user;
    }

    /**
     * Sets the current authenticated user
     * @param zibo\library\security\model\User $user User to set the authentication for
     * @return User updated user with the information of the authentification
     */
    public function setUser(User $user) {
        return $this->authenticator->setUser($user);
    }

    /**
     * Parses the provided string digest into an array of key-value pairs
     * @param string $digest The digest string to parse
     * @return array Array with key-value pairs
     */
    private function parseDigest($digest) {
        // protect against missing data
        $neededParts = array(
            'nonce' => 1,
            'nc' => 1,
            'cnonce' => 1,
            'qop' => 1,
            'username' => 1,
            'uri' => 1,
            'response' => 1,
            'realm' => 1,
            'opaque' => 1,
        );
        $data = array();
        $keys = implode('|', array_keys($neededParts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $digest, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $data[$match[1]] = $match[3] ? $match[3] : $match[4];
            unset($neededParts[$match[1]]);
        }

        return $neededParts ? false : $data;
    }

    /**
     * Generates a valid response from the digest data
     * @param array $digest The data of the digest
     * @return string Valid response to compare the digest response with
     */
    private function generateValidResponse(array $digest) {
        $a1File = $this->getDigestA1File($digest['username']);
        if (!$a1File->exists()) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Could not generate a valid response', 'No A1 digest file exists for ' . $digest['username'], 1, SecurityManager::LOG_NAME);

            return null;
        }

        $request = Zibo::getInstance()->getRequest();

        $a1 = $a1File->read();
        $a2 = md5($request->getMethod() . ':' . $digest['uri']);
        $response = md5($a1 . ':' . $this->nonce . ':' . $digest['nc'] . ':' . $digest['cnonce'] . ':' . $digest['qop'] . ':' . $a2);

        return $response;
    }

    /**
     * Gets the file where the A1 of the response is stored.
     * @param string $username The username for the digest file
     * @return zibo\library\filesystem\File
     */
    private function getDigestA1File($username) {
        return new File(self::PATH_DIGEST, $username);
    }

    /**
     * Gets the value for the WWW-Authenticate header
     * @return string
     */
    public function getAuthenticateHeader() {
        $header = 'Digest realm="' . $this->realm . '"';
        $header .= ',qop="auth"';
        $header .= ',nonce="' . $this->nonce . '"';
        $header .= ',opaque="' . md5($this->realm) . '"';

        return $header;
    }

    /**
     * Initializes the nonce
     * @return null
     */
    private function initNonce() {
        $this->nonce = Session::getInstance()->get(self::SESSION_NONCE);
        if (!$this->nonce) {
            $this->refreshNonce();
        }
    }

    /**
     * Creates a new nounce
     * @return null
     */
    private function refreshNonce() {
        $this->nonce = uniqid();
        Session::getInstance()->set(self::SESSION_NONCE, $this->nonce);
    }

    /**
     * Gets the number of current visitors. This number includes the current users.
     * @return integer
     */
    public function getNumVisitors() {
        return $this->authenticator->getNumVisitors();
    }

    /**
     * Gets the number of current users.
     * @return integer
     */
    public function getNumUsers() {
        return $this->authenticator->getNumUsers();
    }

    /**
     * Gets the usernames of the current users
     * @return array Array with usernames
     */
    public function getCurrentUsers() {
        return $this->authenticator->getCurrentUsers();
    }

}