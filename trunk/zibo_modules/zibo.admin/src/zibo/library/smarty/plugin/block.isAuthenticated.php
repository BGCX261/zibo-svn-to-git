<?php

use zibo\library\security\SecurityManager;

function smarty_block_isAuthenticated($params, $content, &$smarty, &$repeat) {
    if ($repeat) {
        return;
    }

    $user = SecurityManager::getInstance()->getUser();

    if ($user) {
        return $content;
    }

    return;
}