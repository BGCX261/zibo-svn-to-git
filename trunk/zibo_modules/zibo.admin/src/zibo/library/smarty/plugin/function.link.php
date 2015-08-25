<?php

use zibo\library\i18n\I18n;

use zibo\library\html\Anchor;

function smarty_function_link($params, &$smarty) {
    if (empty($params['name'])) {
        throw new Exception('No name parameter provided for the link');
    }
    if (empty($params['href'])) {
        throw new Exception('No href parameter provided for the link');
    }
    $name = $params['name'];
    $href = $params['href'];

    unset($params['name']);
    unset($params['href']);

    $title = $name;
    if (!empty($params['title'])) {
        $title = $params['title'];
        unset($params['title']);
    }

    $translator = I18n::getInstance()->getTranslator();
    $name = $translator->translate($name);
    $title = $translator->translate($title);

    $anchor = new Anchor($name, $href);
    $anchor->setAttribute('title', $title);
    foreach ($params as $key => $value) {
        $anchor->setAttribute($key, $value);
    }

    return $anchor->getHtml();
}