<?php

use zibo\admin\message\Message;

use zibo\library\message\MessageList;

use zibo\ZiboException;

function smarty_function_messages($params, &$tpl) {
    if (isset($params['messages'])) {
        $messages = $params['messages'];
    } else {
        $messages = $tpl->get_template_vars('_messages');
    }

    if (!$messages) {
        return;
    }

    if (!($messages instanceof MessageList)) {
        throw new ZiboException('Provided messages is not an instance of zibo\\library\\message\\MessageList');
    }
    if (!$messages->hasMessages()) {
        return;
    }

    $id = md5(serialize($messages));

    $containerElementId = 'container' . $id;
    $html = '<div class="messages" id="' . $containerElementId . '">' . "\n";
    $html .= smarty_function_messages_parse_type($messages, Message::TYPE_INFORMATION);
    $html .= smarty_function_messages_parse_type($messages, Message::TYPE_ERROR);
    $html .= smarty_function_messages_parse_type($messages, Message::TYPE_WARNING);
    $html .= '</div>' . "\n";

    return $html;
}

function smarty_function_messages_parse_type(MessageList $messages, $type, $id = null) {
    $messages = $messages->getByType($type);

    if (!$messages) {
        return '';
    }

    return smarty_function_messages_get_html($messages, $type, $id);
}

function smarty_function_messages_get_html($messages, $class, $id = null) {
    $html = "\t" . '<div class="' . $class . '"' . ($id != null ? ' id="' . $id . '"' : '') . '>' . "\n";
    $html .= "\t\t" . '<ul>' . "\n";
    foreach ($messages as $message) {
        $html .= "\t\t\t" . '<li>' . $message->getMessage() . '</li>' . "\n";
    }
    $html .= "\t\t" . '</ul>' . "\n";
    $html .= "\t" . '</div>' . "\n";

    return $html;
}
