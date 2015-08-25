<?php

namespace zibo\library\mail\transport;

use zibo\library\mail\exception\MailException;
use zibo\library\mail\Message;

/**
 * Simple message transport using PHP's mail function
 */
class SimpleTransport extends AbstractTransport {

    /**
     * Deliver a mail message to the server mail transport using PHP's mail function
     * @param zibo\library\mail\Message $message The message to send
     * @param array $variables Array containing variables to replace in the mail body
     * @return null
     * @throws zibo\ZiboException when the message is not accepted for delivery. Check the installation of the mail tools on the server.
     */
    public function send(Message $message, array $variables = array()) {
        $parser = new MessageParser($message, $variables);
        $subject = $parser->getSubject();
        $body = $parser->getBody();
        $headers = $parser->getHeaders();

        $headersString = $this->implodeHeaders($headers);

        $additionalParameters = null;

        $returnPath = $message->getReturnPath();
        if ($returnPath) {
            $additionalParameters = '-f ' . $returnPath->getEmailAddress();
        }

        $result = mail(null, $subject, $body, $headersString, $additionalParameters);

        $this->logMail($subject, $headersString, $result);

        if (!$result) {
            throw new MailException('The message is not accepted for delivery. Check your mail configuration on the server.');
        }
    }

}
