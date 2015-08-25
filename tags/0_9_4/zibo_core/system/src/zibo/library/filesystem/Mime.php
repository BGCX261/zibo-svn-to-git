<?php

namespace zibo\library\filesystem;

use zibo\core\Zibo;

/**
 * Basic mime support
 */
class Mime {

    /**
     * Configuration key for the known mime types
     * @var string
     */
    const CONFIG_MIME = 'mime.';

    /**
     * Default mime type for a unknown mime
     * @var string
     */
    const MIME_UNKNOWN = 'application/octet-stream';

    /**
     * Get the mime type of a file based on it's extension
     * @param File $file
     * @return string the mime type of the file
     */
    public static function getMimeType(File $file) {
        $extension = $file->getExtension();
        if (empty($extension)) {
            return self::MIME_UNKNOWN;
        }

        $mime = Zibo::getInstance()->getConfigValue(self::CONFIG_MIME . $extension);
        if (!$mime) {
            $mime = self::MIME_UNKNOWN;
        }

        return $mime;
    }

}