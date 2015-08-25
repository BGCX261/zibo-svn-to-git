<?php

use zibo\library\html\Image;

use zibo\core\Zibo;

use \Exception;

function smarty_function_image($params, &$smarty) {
    try {
        if (empty($params['src'])) {
            throw new Exception('No src parameter provided for the image');
        }

        $src = $params['src'];
        unset($params['src']);

        $image = new Image($src);
        if (!empty($params['thumbnail'])) {
            if (empty($params['width'])) {
                throw new Exception('No width parameter provided for the thumbnailer');
            }
            if (empty($params['height'])) {
                throw new Exception('No height parameter provided for the thumbnailer');
            }
            $image->setThumbnailer($params['thumbnail'], $params['width'], $params['height']);
            unset($params['thumbnail']);
            unset($params['width']);
            unset($params['height']);
        }

        foreach ($params as $key => $value) {
            $image->setAttribute($key, $value);
        }

        $html = $image->getHtml();
    } catch (Exception $exception) {
        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
        $html = '<span class="red" style="color: red;">Could not load image: ' . $exception->getMessage() . '</span>';
    }

    return $html;
}