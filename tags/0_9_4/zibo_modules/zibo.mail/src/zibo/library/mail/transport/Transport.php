<?php

namespace zibo\library\mail\transport;

use zibo\library\mail\Message;

/**
 * Interface to the mail transport
 */
interface Transport {

    /**
     * Deliver a mail message to the mail transport
     * @param zibo\library\mail\Message $message
     * @param array $variables Array containing variables to replace in the mail body
     * @return null
     */
    public function send(Message $message, array $variables = array());

}