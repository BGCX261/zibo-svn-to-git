<?php

namespace zibo\filebrowser\table\decorator;

use zibo\filebrowser\model\filter\DirectoryFilter;
use zibo\filebrowser\model\FileBrowser;

use zibo\library\filesystem\File;
use zibo\library\filesystem\Formatter;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\i18n\I18n;

use \Exception;

/**
 * Decorator for a file or directory in the browser
 */
abstract class AbstractFileDecorator implements Decorator {

    /**
     * Style class for a directory
     * @var string
     */
    const CLASS_DIRECTORY = 'directory';

    /**
     * Style class for a file
     * @var string
     */
    const CLASS_FILE = 'file';

    /**
     * Gets the html of the file name
     * @param string $fileName Name of the file
     * @param string $class Style class for the name container
     * @param string $action URL for the action of the name
     * @return The HTML of the file name
     */
    protected function getNameHtml($fileName, $class, $action = null) {
        $html = '<div class="name ' . $class . '">';

        if ($action) {
            $html .= '<a href="' . $action . '/' . urlencode($fileName) . '">' . $fileName . '</a>';
        } else {
            $html .= $fileName;
        }

        $html .= '</div>';

        return $html;
    }

}