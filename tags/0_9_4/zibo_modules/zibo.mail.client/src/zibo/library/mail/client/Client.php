<?php

namespace zibo\library\mail\client;

use zibo\library\mail\exception\MailException;
use zibo\library\mail\transport\MessageParser;
use zibo\library\mail\Message;
use zibo\library\mail\MimePart;
use zibo\library\Structure;

/**
 * Simple mail client
 */
class Client {

    /**
     * The name for the default connection
     * @var string
     */
    const NAME_SERVER_CONNECTION = '__server__';

    /**
     * The account of the client
     * @var Account
     */
    private $account;

    /**
     * Array with the connections per mailbox
     * @var array
     */
    private $connections;

    /**
     * The parses for messages
     * @var Parser
     */
    private $parser;

    /**
     * Constructs a new mail client
     * @param Account $account The account for the client
     * @return null
     * @throws zibo\library\mail\exception\MailException when the imap extension of PHP is not installed
     */
    public function __construct(Account $account) {
        if (!extension_loaded('imap')) {
            throw new MailException('Your PHP installation does not have the imap extension installed. Please check your server configuration.');
        }

        $this->account = $account;
        $this->connections = array();
        $this->parser = new Parser();
    }

    /**
     * Appends a message to a mailbox
     * @param string $mailbox The mailbox to append the message to
     * @param zibo\library\mail\Message $message The message to append
     * @return null
     * @throws zibo\library\mail\exception\MailException when th message could not be appended to the mailbox
     */
    public function appendMessage($mailbox, Message $message) {
        $parser = new MessageParser($message);
        $message = 'Subject: ' . $parser->getSubject() . "\r\n";
        $message .= implode("\r\n", $parser->getHeaders());
        $message .= "\r\n\r\n" . $parser->getBody();

        $connection = $this->getConnection($mailbox);
        $stream = $connection->getStream();
        $reference = $connection->getReference();

        if (!imap_append($stream, $reference, $message)) {
            throw new MailException('Could not append the message to ' . $mailbox . ': ' . imap_last_error());
        }
    }

    /**
     * Deletes a message
     * @param string $mailbox The mailbox of the message
     * @param integer $id The internal id of the message
     * @param string $trash Provide a trash mailbox to move the message to the trash instead of actually deleting it
     * @return null
     * @throws zibo\łibrary\mail\MailException when the message could not be moved to trash
     * @throws zibo\łibrary\mail\MailException when the message could not be deleted
     * @see imap_mail_move
     * @see imap_delete
     */
    public function deleteMessage($mailbox, $id, $trash = null) {
        $connection = $this->getConnection($mailbox);
        $stream = $connection->getStream();

        if ($trash) {
            $trash = $connection->getReference() . $trash;

            if (!imap_mail_move($stream, $id, $trash)) {
                throw new MailException('Could not delete message ' . $id . ' to ' . $trash . ': ' . imap_last_error());
            }
        } elseif (!imap_delete($stream, $id)) {
            throw new MailException('Could not delete message ' . $id . ': ' . imap_last_error());
        }

        imap_expunge($stream);
    }

    /**
     * Moves a message to another folder
     * @param string $mailbox The mailbox of the message
     * @param integer $id The internal id of the message
     * @param string $destination The mailbox of the destination
     * @return null
     * @throws zibo\łibrary\mail\MailException when the message could not be moved
     * @see imap_mail_move
     */
    public function moveMessage($mailbox, $id, $destination) {
        $connection = $this->getConnection($mailbox);
        $stream = $connection->getStream();
        $destination = $connection->getReference() . $destination;

        if (!imap_mail_move($stream, $id, $destination)) {
            throw new MailException('Could not move message ' . $id . ' to ' . $destination . ': ' . imap_last_error());
        }

        imap_expunge($stream);
    }

    /**
     * Gets the folder from this account
     * @param string $query Query to select a subset of the folders
     * @return array Array with mailbox names
     * @see imap_list
     */
    public function getFolders($query = '*') {
        $connection = $this->getConnection();
        $reference = $connection->getReference();

        $folders = imap_list($connection->getStream(), $reference, $query);

        foreach ($folders as $index => $folderName) {
            $folders[$index] = str_replace($reference, '', $folderName);
        }

        return $folders;
    }

    /**
     * Gets the headers of messages in the provided mailbox
     * @param string $mailbox The nname of the mailbox
     * @return array Array with ClientMessage objects
     */
    public function getMessages($mailbox = 'INBOX') {
        $connection = $this->getConnection($mailbox);

        $messages = array();
        $headers = imap_headers($connection->getStream());
        foreach ($headers as $id => $null) {
            $id++;
            $messages[$id] = $this->getMessage($mailbox, $id, false);
        }

        return $messages;
    }

    /**
     * Gets a message from the mailbox
     * @param string $mailbox The mailbox of the message
     * @param integer $id The internal id of the message in the mailbox
     * @param boolean $includeParts True to fetch the body of the mail, false to only fetch the headers
     * @return ClientMessage The fetched message
     */
    public function getMessage($mailbox, $id, $includeBody = true) {
        $connection = $this->getConnection($mailbox);
        $stream = $connection->getStream();

        $data = imap_header($stream, $id);

        $message = new ClientMessage($id, $data);

        if (!$includeBody) {
            return $message;
        }

        $parts = $this->getMessageParts($stream, $id);
        foreach ($parts as $name => $part) {
            $message->addPart($part, $name);
        }

        return $message;
    }

    /**
     * Gets the parts of the provided message
     * @param resource $stream The stream of the connection with the server
     * @param integer $id The internal id of the message
     * @return array Array with MimePart objects
     * @see zibo\library\mail\MimePart
     */
    private function getMessageParts($stream, $id) {
        $structure = imap_fetchstructure($stream, $id);

        if (isset($structure->parts) && count($structure->parts)) {
            $parts = array();

            // multipart messages
            foreach ($structure->parts as $partId => $part) {
                $subParts = $this->parseMessagePart($stream, $id, $partId + 1, $part);
                $parts = Structure::merge($parts, $subParts);
            }

            return $parts;
        }

        // single part messages
        $text = imap_body($stream, $id);

        $mimeType = $this->parser->getPartMimeType($structure);
        $encoding = $this->parser->getPartEncoding($structure);
        $part = new MimePart($text, $mimeType, null, $encoding);

        return array(Message::PART_BODY => $part);
    }

    /**
     * Parses the provided part and subparts
     * @param resource $stream The stream of the connection with the server
     * @param integer $id The internal id of the message
     * @param integer $partId The internal id of the part
     * @param object $part The part to parse
     * @return array Array with MimePart objects extracted from the part
     */
    private function parseMessagePart($stream, $id, $partId, $part) {
        $parts = array();

        $body = imap_fetchbody($stream, $id, $partId);

        if ($part->type != 0) {
            // part is not text
            $filename = null;

            if (isset($part->dparameters)) {
                $filename = $this->parser->getPartFilename($part->dparameters);
            }
            if (empty($filename) && isset($part->parameters)) {
                $filename = $this->parser->getPartFilename($part->parameters);
            }

            if (!empty($filename)) {
                $mimeType = $this->parser->getPartMimeType($part);
                $encoding = $this->parser->getPartEncoding($part);
                $parts[$filename] = new MimePart($body, $mimeType, null, $encoding);
            }
        } else {
            // part is text
            $textPartId = $partId;
            if (!isset($parts[Message::PART_BODY])) {
                $textPartId = Message::PART_BODY;
            } elseif (!isset($parts[Message::PART_ALTERNATIVE])) {
                if ($parts[Message::PART_BODY]->getMimeType() === 'text/html') {
                    $textPartId = Message::PART_ALTERNATIVE;
                } else {
                    $textPartId = Message::PART_BODY;
                    $parts[Message::PART_ALTERNATIVE] = $parts[Message::PART_BODY];
                    unset($parts[Message::PART_BODY]);
                }
            }

            $mimeType = $this->parser->getPartMimeType($part);
            $encoding = $this->parser->getPartEncoding($part);
            $parts[$textPartId] = new MimePart($body, $mimeType, null, $encoding);
        }

        // if subparts... recurse into function and parse them too!
        if (isset($part->parts) && count($part->parts)) {
            foreach ($part->parts as $subPartId => $subPart) {
                $subParts = $this->parseMessagePart($stream, $id, $partId . '.' . ($subPartId + 1), $subPart);
                $parts = Structure::merge($parts, $subParts);
            }
        }

        return $parts;
    }

    /**
     * Gets a connection for the specified mailbox
     * @param string $mailbox The mailbox of the connection
     * @return ClientConnection The connection of the provided mailbox
     */
    private function getConnection($mailbox = null) {
        $name = $mailbox;
        if ($name === null) {
            $name = self::NAME_SERVER_CONNECTION;
        }

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        $connection = new ClientConnection($this->account, $mailbox);
        $this->connections[$name] = $connection;

        return $connection;
    }

}