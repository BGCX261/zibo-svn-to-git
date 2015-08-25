<?php

namespace zibo\library\mail\client;

use zibo\library\mail\exception\MailException;

/**
 * Connection with a mail server
 */
class ClientConnection {

    /**
     * The reference of the connection for the imap functions
     * @var string
     */
    private $reference;

    /**
     * The stream of the connection
     * @var resource
     */
    private $stream;

    /**
     * Constructs a new connection
     * @param zibo\library\mail\client\Account $account The account of the connection
     * @param string $mailbox The mailbox of the account
     * @return null
     */
    public function __construct(Account $account, $mailbox = null) {
        $username = $account->getUsername();
        $password = $account->getPassword();

        $this->reference = $account->getServerReference();
        if ($mailbox) {
            $this->reference .= $mailbox;
        }

        $this->stream = @imap_open($this->reference, $username, $password);
        if (!$this->stream) {
            throw new MailException('Could not connect to ' . $this->reference . ': ' . imap_last_error());
        }
    }

    /**
     * Destructs the connection, make sure all connections are closed
     * @return null
     */
    public function __destruct() {
        if (!$this->stream) {
            return;
        }

        if (!imap_close($this->stream)) {
            throw new MailException('Could not disconnect ' . $this->reference . ': ' . imap_last_error());
        }
    }

    /**
     * Gets the reference of the connection
     * @return string
     */
    public function getReference() {
        return $this->reference;
    }

    /**
     * Gets the stream of the connection
     * @return resource
     */
    public function getStream() {
        return $this->stream;
    }

}