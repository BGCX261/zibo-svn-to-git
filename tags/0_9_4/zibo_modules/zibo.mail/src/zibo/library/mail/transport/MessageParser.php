<?php

namespace zibo\library\mail\transport;

use zibo\core\Zibo;

use zibo\library\mail\exception\MailException;
use zibo\library\mail\Address;
use zibo\library\mail\Message;
use zibo\library\mail\MimePart;

/**
 * Object to parse mail message objects into the parts needed to actually send the message
 */
class MessageParser {

    /**
     * Configuration key for the debug recipient, when this is set, all mails will go to this address and no other
     * @var string
     */
    const CONFIG_DEBUG = 'mail.debug';

    /**
     * Configuration key for the default sender of messages
     * @var string
     */
    const CONFIG_SENDER = 'mail.sender';

    /**
     * Header for the MIME version
     * @var string
     */
    const HEADER_MIME_VERSION = 'MIME-Version';

    /**
     * Header for the content type
     * @var string
     */
    const HEADER_CONTENT_TYPE = 'Content-Type';

    /**
     * Header for the content disposition
     * @var string
     */
    const HEADER_CONTENT_DISPOSITION = 'Content-Disposition';

    /**
     * Header for the content transfer encoding
     * @var string
     */
    const HEADER_CONTENT_TRANSFER_ENCODING = 'Content-Transfer-Encoding';

    /**
     * Header for the from address
     * @var string
     */
    const HEADER_FROM = 'From';

    /**
     * Header for the to addresses
     * @var string
     */
    const HEADER_TO = 'To';

    /**
     * Header for the carbon copy addresses
     * @var string
     */
    const HEADER_CC = 'Cc';

    /**
     * Header for the blind carbon copy addresses
     * @var string
     */
    const HEADER_BCC = 'Bcc';

    /**
     * Header for the reply to address
     * @var string
     */
    const HEADER_REPLY_TO = 'Reply-To';

    /**
     * Header for the message id
     * @var string
     */
    const HEADER_MESSAGE_ID = 'Message-Id';

    /**
     * Header for the in reply to
     * @var string
     */
    const HEADER_IN_REPLY_TO = 'In-Reply-To';

    /**
     * Header for the references
     * @var string
     */
    const HEADER_REFERENCES = 'References';

    /**
     * Header for the auto submitted flag
     * @var string
     */
    const HEADER_AUTO_SUBMITTED = 'Auto-Submitted';

    /**
     * MIME type for a multipart mixed message
     * @var string
     */
    const MIME_MULTIPART_MIXED = 'multipart/mixed';

    /**
     * MIME type for a alternative message
     * @var string
     */
    const MIME_MULTIPART_ALTERNATIVE = 'multipart/alternative';

    /**
     * The subject of the parsed message
     * @var string
     */
    private $subject;

    /**
     * The body of the parsed message
     * @var string
     */
    private $body;

    /**
     * The headers of the parsed message
     * @var array
     */
    private $headers;

    /**
     * Parses the provided message and get all data to actually send it
     * @param zibo\library\mail\Message $message The message to parse
     * @param array $variables Array with variables to replace in the body
     * @return null
     */
    public function __construct(Message $message, array $variables = array()) {
        $this->parseMessage($message, $variables);
    }

    /**
     * Gets the subject of the message
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Gets the parsed body of the message
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Gets the parsed headers of the message
     * @return array Array with the header name as key and the full header as value
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Parses the headers and the body from the provided message
     * @param zibo\library\mail\Message $message The message to parse
     * @param array $variables Array with variables to replace in the body
     * @return null
     */
    private function parseMessage(Message $message, array $variables) {
        $this->headers = array();
        $this->subject = $message->getSubject();
        $this->body = '';

        $this->parseAddresses($message);

        $this->parseHeaders($message);

        $this->parseParts($message, $variables);
    }

    /**
     * Parses the addresses of the provided message and adds them to the headers of the message
     * @param zibo\library\mail\Message $message The message to parse the addresses of
     * @return null
     */
    private function parseAddresses(Message $message) {
        $from = $message->getFrom();
        $to = $message->getTo();
        $cc = $message->getCc();
        $bcc = $message->getBcc();
        $replyTo = $message->getReplyTo();

        if (empty($to) && empty($cc) && empty($bcc)) {
            throw new MailException('No recipients set');
        }

        if (!$from) {
            $sender = Zibo::getInstance()->getConfigValue(self::CONFIG_SENDER);
            if ($sender) {
                $from = new Address($sender);
            }
        }
        if ($from) {
            $this->headers[self::HEADER_FROM] = self::HEADER_FROM . ': ' . $from->__toString();
        }

        $debug = Zibo::getInstance()->getConfigValue(self::CONFIG_DEBUG);
        if ($debug) {
            $debugAddress = new Address($debug);
            $this->addAddressesToHeaders(self::HEADER_TO, array($debugAddress));
        } else {
            $this->addAddressesToHeaders(self::HEADER_TO, $to);
            $this->addAddressesToHeaders(self::HEADER_CC, $cc);
            $this->addAddressesToHeaders(self::HEADER_BCC, $bcc);
            if ($replyTo) {
                $this->headers[self::HEADER_REPLY_TO] = self::HEADER_REPLY_TO . ': ' . $replyTo->__toString();
            }
        }
    }

    /**
     * Adds the recipients to the provided header
     * @param string $header Name of the header (To, Cc, Bcc, ...)
     * @param array $addresses Array with Address objects
     * @return null
     */
    private function addAddressesToHeaders($header, $addresses) {
        if (!$addresses) {
            return;
        }

        $content = '';
        foreach ($addresses as $address) {
            $content .= ($content == '' ? '' : ', ') . $address->__toString();
        }

        $this->headers[$header] = $header . ': ' . $content;
    }

    /**
     * Parses the headers of the provided message
     * @param zibo\library\mail\Message $message The message to parse the headers of
     * @return null
     */
    private function parseHeaders(Message $message) {
        $this->headers[self::HEADER_MIME_VERSION] = self::HEADER_MIME_VERSION . ': 1.0';

        $messageId = $message->getMessageId();
        if ($messageId) {
            $this->headers[self::HEADER_MESSAGE_ID] = self::HEADER_MESSAGE_ID . ': ' . $messageId;
        }

        $inReplyTo = $message->getInReplyTo();
        if ($inReplyTo) {
            $this->headers[self::HEADER_IN_REPLY_TO] = self::HEADER_IN_REPLY_TO . ': ' . $inReplyTo;
        }

        $references = $message->getReferences();
        if ($references) {
            $this->headers[self::HEADER_REFERENCES] = self::HEADER_REFERENCES . ': ' . implode(' ', $references);
        }

        $autoSubmitted = $message->getAutoSubmitted();
        if ($autoSubmitted) {
            $this->headers[self::HEADER_AUTO_SUBMITTED] = self::HEADER_AUTO_SUBMITTED . ': ' . $autoSubmitted;
        }
    }

    /**
     * Parses the body parts of the provided message
     * @param zibo\library\mail\Message $message The message to parse the parts of
     * @param array $variables Array with variables to replace in the body
     * @return null
     */
    private function parseParts(Message $message, array $variables) {
        $attachments = $message->getParts();
        $numParts = count($attachments);

        $body = null;
        if (isset($attachments[Message::PART_BODY])) {
            $body = $attachments[Message::PART_BODY];
            unset($attachments[Message::PART_BODY]);
        }

        $alternative = null;
        if (isset($attachments[Message::PART_ALTERNATIVE])) {
            $alternative = $attachments[Message::PART_ALTERNATIVE];
            unset($attachments[Message::PART_ALTERNATIVE]);
        }

        if (!$alternative && $numParts == 1) {
            $this->headers[self::HEADER_CONTENT_TYPE] = self::HEADER_CONTENT_TYPE . ': ' . $body->getMimeType() . '; charset=' . $body->getCharset();
            $this->headers[self::HEADER_CONTENT_TRANSFER_ENCODING] = self::HEADER_CONTENT_TRANSFER_ENCODING . ': ' . $body->getTransferEncoding();

            $this->addPartToBody($body, $variables, true);

            return;
        }

        $salt = $this->generateSalt();

        if ($alternative && $numParts == 2) {
            $this->headers[self::HEADER_CONTENT_TYPE] = self::HEADER_CONTENT_TYPE . ': ' . self::MIME_MULTIPART_ALTERNATIVE . '; boundary=' . $salt;

            $this->addPartsToBody($body, $alternative, $variables, $salt);

            return;
        }

        $this->headers[self::HEADER_CONTENT_TYPE] = self::HEADER_CONTENT_TYPE . ': ' . self::MIME_MULTIPART_MIXED . '; boundary=' . $salt;

        if ($alternative) {
            $messageSalt = $this->generateSalt('message');

            $this->body .= '--' . $salt . "\n";
            $this->body .= self::HEADER_CONTENT_TYPE . ': ' . self::MIME_MULTIPART_ALTERNATIVE . '; boundary=' . $messageSalt . "\n\n";

            $this->addPartsToBody($body, $alternative, $variables, $messageSalt);
        } elseif ($body) {
            $this->body .= '--' . $salt . "\n";
            $this->addPartToBody($body, $variables);
        }

        if (isset($attachments) && is_array($attachments)) {
            foreach ($attachments as $name => $attachment) {
                $this->body .= '--' . $salt . "\n";
                $this->addAttachmentToBody($attachment, $name);
            }
        }

        $this->body .= '--' . $salt . '--' . "\n";
    }

    /**
     * Adds the body and the alternative body to the body of the mail
     * @param zibo\library\mail\MimePart $body The MIME part of the body (HTML)
     * @param zibo\library\mail\MimePart $alternative The MIME part of the alternative body (plain text)
     * @param array $variables Array with variables to replace in the body
     * @param string $salt The salt to delimit the parts
     * @return null
     */
    private function addPartsToBody(MimePart $body, MimePart $alternative, array $variables, $salt) {
        $this->body .= '--' . $salt . "\n";
        $this->addPartToBody($alternative, $variables);
        $this->body .= '--' . $salt . "\n";
        $this->addPartToBody($body, $variables);
        $this->body .= '--' . $salt . '--' . "\n";
    }

    /**
     * Adds a MIME part to the body of the message
     * @param zibo\library\mail\MimePart $part The MIME part to add
     * @param array $variables Array with variables to replace in the body of the part
     * @param boolean $skipHeaders Set to true to skip the content type and transfer encoding
     * @return null
     */
    private function addPartToBody(MimePart $part, array $variables, $skipHeaders = false) {
        if (!$skipHeaders) {
            $this->body .= self::HEADER_CONTENT_TYPE . ': ' . $part->getMimeType() . '; charset=' . $part->getCharset() . "\n";
            $this->body .= self::HEADER_CONTENT_TRANSFER_ENCODING . ': ' . $part->getTransferEncoding() . "\n\n";
        }

        $body = $part->getBody();
        foreach ($variables as $key => $value) {
            $body = str_replace('%' . $key . '%', $value, $body);
        }

        $this->body .= $body . "\n\n";
    }

    /**
     * Adds a attachment to the body of the message
     * @param zibo\library\mail\MimePart $part The MIME part of the attachment
     * @param string $name The name of the attachment
     * @return null
     */
    private function addAttachmentToBody(MimePart $part, $name) {
        $this->body .= self::HEADER_CONTENT_TYPE . ': ' . $part->getMimeType() . '; name="' . $name .  "\"\n";
        $this->body .= self::HEADER_CONTENT_DISPOSITION . ': attachment; filename="' . $name .  "\"\n";
        $this->body .= self::HEADER_CONTENT_TRANSFER_ENCODING . ': ' . $part->getTransferEncoding() . "\n\n";
        $this->body .= $part->getBody() . "\n\n";
    }

    /**
     * Generates a salt to delimit MIME parts
     * @param string $salt Token for the salt (optional)
     * @return string
     */
    private function generateSalt($salt = '') {
        return md5($salt . microtime());
    }

}
