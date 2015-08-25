<?php

namespace zibo\admin\controller;

use zibo\admin\view\FileView;

use zibo\core\Request;
use zibo\core\Response;
use zibo\core\Zibo;

use zibo\library\filesystem\File;

/**
 * Controller to host files from the web directory in the Zibo include paths
 */
class WebController extends AbstractController {

    /**
     * Action to host a file. The filename is provided by the arguments as tokens
     * @return null
     */
    public function indexAction() {
        $args = func_get_args();
        $path = implode(Request::QUERY_SEPARATOR, $args);

        if (empty($path)) {
            $this->response->setRedirect($this->request->getBaseUrl());
            return;
        }

        $file = $this->getFile($path);
        if ($file == null) {
            $this->setError404();
            return;
        }

        if ($file->getExtension() == 'php') {
            require_once($file->getAbsolutePath());
            return;
        }

        $lastModified = gmdate('r', $file->getModificationTime());
        $headerModified = $this->request->getHeader(Request::HEADER_MODIFIED_SINCE);
        if ($headerModified == $lastModified) {
            $this->response->setStatusCode(Response::STATUS_CODE_NOT_MODIFIED);
            return;
        }

        $view = new FileView($file);
        $this->response->setView($view);
    }

    /**
     * Gets the file from the Zibo include paths
     * @param string $path Path, in the webdirectory, of the file
     * @return null|zibo\library\filesystem\File
     */
    private function getFile($path) {
        $zibo = Zibo::getInstance();

        $plainPath = new File(Zibo::DIRECTORY_WEB . File::DIRECTORY_SEPARATOR . $path);

        $file = $zibo->getFile($plainPath->getPath());
        if ($file) {
            return $file;
        }

        $encodedPath = new File($plainPath->getParent()->getPath(), urlencode($plainPath->getName()));

        return $zibo->getFile($encodedPath->getPath());
    }

}