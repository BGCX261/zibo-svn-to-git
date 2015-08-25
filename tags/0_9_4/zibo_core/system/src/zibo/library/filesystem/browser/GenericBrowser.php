<?php

namespace zibo\library\filesystem\browser;

use zibo\library\filesystem\File;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Generic browser to find files in the Zibo filesystem structure
 */
class GenericBrowser extends AbstractBrowser {

    /**
     * Look for files by looping through the include paths
     * @param string $fileName relative path of a file in the Zibo filesystem structure
     * @param boolean $firstOnly true to get the first matched file, false to get an array
     *                           with all the matched files
     * @return zibo\library\filesystem\File|array Depending on the firstOnly flag, an instance or an array of zibo\library\filesystem\File
     * @throws zibo\ZiboException when fileName is empty or not a string
     */
    protected function lookupFile($fileName, $firstOnly) {
        if (!($fileName instanceof File) && String::isEmpty($fileName)) {
            throw new ZiboException('Provided filename is empty');
        }

        $files = array();

        $includePaths = $this->getIncludePaths();
        foreach ($includePaths as $includePath) {
            $file = new File($includePath, $fileName);

            if (!$file->exists()) {
                continue;
            }

            if ($firstOnly) {
                return $file;
            }

            $files[$file->getPath()] = $file;
        }

        if ($firstOnly) {
            return null;
        }

        ksort($files);

        return $files;
    }

}