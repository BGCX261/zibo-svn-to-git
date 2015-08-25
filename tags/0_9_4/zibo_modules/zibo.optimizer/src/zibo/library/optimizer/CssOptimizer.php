<?php

namespace zibo\library\optimizer;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\html\Image;
use zibo\library\optimizer\cssmin\CSSMin;
use zibo\library\Structure;

use zibo\ZiboException;

/**
 * Optimize an array of css files into one file without comments and unnecessairy whitespaces
 */
class CssOptimizer extends AbstractOptimizer {

    /**
     * File extension (or type) of this optimizer
     * @var string
     */
    const EXTENSION = 'css';

    /**
     * Path for this optimizer in the cache
     * @var string
     */
    const PATH_CACHE = 'styles';

    /**
     * CSS source minifier
     * @var zibo\library\optimizer\cssmin\CSSMin
     */
    private $cssMinifier;

    /**
     * Constructs a new CSS optimizer
     * @return null
     */
    public function __construct() {
        parent::__construct(new File(self::PATH_CACHE));
    }

    /**
     * Gets the extension of this optimizer
     * @return string
     */
    protected function getExtension() {
        return self::EXTENSION;
    }

    /**
     * Gets all the files used by the provided CSS files
     * @param array $fileNames Array with the file names of CSS files
     * @return array Array with the path of the file as key and the File object as value
     */
    protected function getFilesFromArray(array $fileNames) {
        $files = array();

        $zibo = Zibo::getInstance();

        foreach ($fileNames as $fileName) {
            $file = new File($fileName);
            if (!$file->isAbsolute()) {
                $file = $zibo->getFile($fileName);
                if (!$file) {
                    continue;
                }
            }

            $styleFiles = $this->getFilesFromStyle($zibo, $file);
            $files = Structure::merge($files, $styleFiles);
        }

        return $files;
    }

    /**
     * Gets all the files needed for the provided CSS file. This will extract the imports from the CSS.
     * @param zibo\core\Zibo $zibo Instance of Zibo
     * @param zibo\library\filesystem\File $file CSS source file
     * @return array Array with the path of the file as key and the File object as value
     */
    private function getFilesFromStyle(Zibo $zibo, File $file) {
        $source = $file->read();
        $source = preg_replace(CSSMin::REGEX_COMMENT, '', $source);

        $files = array();

        $parent = $file->getParent();

        $lines = explode("\n", $source);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (!preg_match(CSSMin::REGEX_IMPORT, $line)) {
                break;
            }

            $importFileName = $this->getFileNameFromImportLine($line);
            $importFile = $zibo->getRelativeFile(new File($parent, $importFileName));
            $importFile = $zibo->getFile($importFile);
            if (!$importFile) {
                continue;
            }

            $styleFiles = $this->getFilesFromStyle($zibo, $importFile);
            $files = Structure::merge($files, $styleFiles);
        }

        $files[$file->getPath()] = $file;

        return $files;
    }

    /**
     * Extracts the file name of a CSS import statement
     * @param string $line Line of the import statement
     * @return string File name referenced in the import statement
     */
    private function getFileNameFromImportLine($line) {
        $line = str_replace(array('@import', ';'), '', $line);
        $line = trim($line);

        if (strpos($line, ' ') !== false) {
            list($fileToken, $mediaToken) = explode(' ', $line, 2);
        } else {
            $fileToken = $line;
        }

        if (preg_match('/^url/', $fileToken)) {
            $fileToken = substr($fileToken, 3);
        }

        return str_replace(array('(', '"', '\'', ')'), '', $fileToken);
    }

    /**
     * Optimizes the provided CSS source
     * @param string $source CSS source
     * @param zibo\library\filesystem\File $file The file of the source
     * @return string optimized and minified CSS source
     */
    protected function optimizeSource($source, File $file) {
        $source = preg_replace(CSSMin::REGEX_IMPORT, '', $source);

        $source = $this->getCssMinifier()->minify($source, true);

        $zibo = Zibo::getInstance();
        $parent = $file->getParent();

        $source = preg_replace_callback(
            '/url( )?\\(["\']?([^;\\\\"\')]*)(["\']?)\\)([^;\\)]*);/',
            function ($matches) use ($zibo, $parent) {
                try {
                    $source = new File($parent, $matches[2]);
                    $source = $zibo->getRelativeFile($source);
                    $source = $source->getPath();
                } catch (ZiboException $e) {
                    $zibo->runEvent(Zibo::EVENT_LOG, $e->getMessage(), $e->getTraceAsString());
                    $source = $matches[2];
                }

                try {
                    $image = new Image($source);
                    $image->getHtml();

                    $source = $image->getSource();
                } catch (ZiboException $e) {
                    $zibo->runEvent(Zibo::EVENT_LOG, $e->getMessage(), $e->getTraceAsString());
                }

                return "url(" . $source . ")" . $matches[4] . ';';
            },
            $source
        );

        return $source;
    }

    /**
     * Gets the CSS minifier
     * @return zibo\library\optimizer\CSSMin
     */
    private function getCssMinifier() {
        if (!$this->cssMinifier) {
            $this->cssMinifier = new CSSMin();
        }

        return $this->cssMinifier;
    }

}