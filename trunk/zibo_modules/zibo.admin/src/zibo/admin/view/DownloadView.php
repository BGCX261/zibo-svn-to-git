<?php

namespace zibo\admin\view;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\filesystem\File;

/**
 * View to force a download of a file
 */
class DownloadView extends FileView {

    /**
     * Constructs the download view
     * @param zibo\library\filesystem\File $file File to offer as download
     * @param string $name Name for the download file, if not provided, the name of the file will be used
     * @return null
     */
    public function __construct(File $file, $name = null) {
        parent::__construct($file);

        if ($name == null) {
            $name = $file->getName();
        }

        $zibo = Zibo::getInstance();
        $request = $zibo->getRequest();
        $response = $zibo->getResponse();

        $userAgent = $request->getHeader(Request::HEADER_USER_AGENT);
        if ($userAgent && strstr($userAgent, "MSIE")) {
            $name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
        }

        $response->setHeader('Cache-Control', 'no-cache, must-revalidate');
        $response->setHeader('Content-Description', 'File Transfer');
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $name);
    }

}