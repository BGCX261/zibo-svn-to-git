<?php

use zibo\library\security\SecurityManager;

function smarty_block_isAllowed($params, $content, &$smarty, &$repeat) {
    if ($repeat) {
        return;
    }

    if (!isset($params['route']) && !isset($params['permission'])) {
        throw new Exception('No route or permission provided');
    }
    if (isset($params['route']) && isset($params['permission'])) {
        throw new Exception('Route and permissions provided');
    }

    $manager = SecurityManager::getInstance();

    if (isset($params['route'])) {
        if ($manager->isRouteAllowed($params['route'])) {
            return $content;
        }
    } elseif ($manager->isPermissionAllowed($params['permission'])) {
        return $content;
    }

    return;
}