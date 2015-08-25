<?php

use zibo\library\i18n\I18n;

use zibo\library\html\Pagination;

function smarty_function_pagination($params, &$smarty) {
    $translator = I18n::getInstance()->getTranslator();

    $page = 0;
    $pages = null;
    $href = null;
    $onclick = null;
    $label = null;

    foreach ($params as $k => $v) {
        switch ($k) {
            case 'label':
            case 'page':
            case 'pages':
            case 'href':
            case 'onclick':
                $$k = $v;
                break;
        }
    }

    $pagination = new Pagination($pages, $page);
    $pagination->setHref($href);
    $pagination->setOnclick($onclick);
    if ($label) {
        $pagination->setLabel($label);
    }

    return $pagination->getHtml();
}