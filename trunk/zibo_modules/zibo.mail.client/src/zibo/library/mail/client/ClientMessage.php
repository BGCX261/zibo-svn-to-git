<?php

namespace zibo\library\mail\client;

use zibo\library\mail\Address;
use zibo\library\mail\Message;

use \Exception;

/**
 * Data container for a mail message extended with client functions
 */
class ClientMessage extends Message {

    /**
     * The internal id of the message
     * @var integer
     */
    private $id;

    /**
     * The date of the message
     * @var string
     */
    private $date;

    /**
     * The size of the message in bytes
     * @var integer
     */
    private $size;

    /**
     * Flag to see if this message has been read
     * @var boolean
     */
    private $isRead;

    /**
     * Flag to see if this message has been answered
     * @var boolean
     */
    private $isAnswered;

    /**
     * Flag to see if this message has been deleted
     * @var boolean
     */
    private $isDeleted;

    /**
     * Construct a client message
     * @param string $id The internal id of the message
     * @param object $data Result of imap_header
     * @return null
     */
    public function __construct($id, $data) {
        $this->id = $id;

        $from = $this->parseAddresses($data->from);
        $from = array_pop($from);
        $this->setFrom($from);
        if (isset($data->to) && $data->to) {
            $this->setTo($this->parseAddresses($data->to));
        }

        if (isset($data->cc) && $data->cc) {
            $this->setCc($this->parseAddresses($data->cc));
        }

        if (isset($data->bcc) && $data->bcc) {
            $this->setBcc($this->parseAddresses($data->bcc));
        }

        if (isset($data->reply_to) && $data->reply_to) {
            $replyTo = $this->parseAddresses($data->reply_to);
            $replyTo = array_pop($replyTo);
            $this->setReplyTo($replyTo);
        }

        if (isset($data->in_reply_to) && $data->in_reply_to) {
            $this->setInReplyTo($data->in_reply_to);
        }

        if (isset($data->message_id)) {
            $this->setMessageId($data->message_id);
        }

        if (isset($data->Subject)) {
            $this->setSubject($data->Subject);
        } else {
            $this->setSubject('no subject');
        }

        if (isset($data->Date)) {
            $this->setDate(strtotime($data->Date));
        }

        if (isset($data->Size)) {
            $this->setSize($data->Size);
        }

        if (isset($data->Recent) && isset($data->Unseen)) {
            $this->setIsRead(!($data->Recent == 'N' || $data->Unseen == 'U'));
        }

        if (isset($data->Answered)) {
            $this->setIsAnswered($data->Answered == 'A');
        }

        if (isset($data->Deleted)) {
            $this->setIsDeleted($data->Deleted == 'D');
        }
    }

    /**
     * Gets the internal id of the message
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Sets the date of this message
     * @param string $date
     * @return null
     */
    private function setDate($date) {
        $this->date = $date;
    }

    /**
     * Gets the date of this message
     * @return string
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Sets the size of this message
     * @param integer $size The size in bytes
     * @return null
     */
    private function setSize($size) {
        $this->size = $size;
    }

    /**
     * Gets the size of this message
     * @return integer The size in bytes
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Sets whether this message is read
     * @param boolean $flag True if read, false otherwise
     * @return null
     */
    private function setIsRead($flag) {
        $this->isRead = $flag;
    }

    /**
     * Gets whether this message is read
     * @return boolean True if read, false otherwise
     */
    public function isRead() {
        return $this->isRead;
    }

    /**
     * Sets whether this message is answered
     * @param boolean $flag True if answered, false otherwise
     * @return null
     */
    private function setIsAnswered($flag) {
        $this->isAnswered = $flag;
    }

    /**
     * Gets whether this message is read
     * @return boolean True if answered, false otherwise
     */
    public function isAnswered() {
        return $this->isAnswered;
    }

    /**
     * Sets whether this message is deleted
     * @param boolean $flag True if deleted, false otherwise
     * @return null
     */
    private function setIsDeleted($flag) {
        $this->isDeleted = $flag;
    }

    /**
     * Gets whether this message is deleted
     * @return boolean True if deleted, false otherwise
     */
    public function isDeleted() {
        return $this->isDeleted;
    }

    /**
     * Gets Address objects from the array of address
     * @param array $addresses Array with addresses from the resulting object of imap_header
     * @return array Array with Address objects
     */
    private function parseAddresses(array $addresses) {
        $result = array();

        foreach ($addresses as $address) {
            try {
                if (!isset($address->mailbox)) {
                    $address->mailbox = 'unknown';
                }
                if (!isset($address->host)) {
                    $address->host = 'localhost';
                }

                $email = $address->mailbox . '@' . $address->host;
                if (!empty($address->personal)) {
                    $result[$email] = new Address($address->personal . ' <' . $email . '>');
                } else {
                    $result[$email] = new Address($email);
                }
            } catch (Exception $e) {
                $email = 'Undisclosed recipients <undisclosed-recipients@localhost>';
                $result[$email] = new Address($email);
            }
        }

        return $result;
    }

}