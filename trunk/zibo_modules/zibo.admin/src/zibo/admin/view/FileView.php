<?php

namespace zibo\admin\view;

use zibo\core\Response;
use zibo\core\View;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\filesystem\Mime;

use zibo\ZiboException;

/**
 * View to render the contents of a file
 */
class FileView implements View {

    /**
     * The file to render
     * @var zibo\library\filesystem\File
     */
    private $file;

    /**
     * Constructs a new file view
     * @param zibo\library\filesystem\File $file File to render
     * @return null
     */
    public function __construct(File $file) {
        $response = Zibo::getInstance()->getResponse();

        if (!$file->exists() || $file->isDirectory()) {
            $response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
            return;
        }

        $maxAge = 60 * 60;
        $mime = Mime::getMimeType($file);
        $lastModified = gmdate('r', $file->getModificationTime());
        $expires = gmdate('r', time() + $maxAge);

        $response->setHeader('Pragma', 'public');
        $response->setHeader('Cache-Control', 'max-age=' . $maxAge);
        $response->setHeader('Expires', $expires);
        $response->setHeader('Last-Modified', $lastModified);
        $response->setHeader('ETag', md5($lastModified));
        $response->setHeader('Content-Type', $mime);
        $response->setHeader('Content-Length', $file->getSize());
        $this->file = $file;
    }

    /**
     * Renders the file view
     * @param boolean $return True to return the contents of the file, false to passthru the file to the output
     * @return null|string
     */
    public function render($return = true) {
        if ($return) {
            return $this->file->read();
        }

        $this->file->passthru();
    }

}