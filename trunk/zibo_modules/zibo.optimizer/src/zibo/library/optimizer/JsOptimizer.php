<?php

namespace zibo\library\optimizer;

use zibo\library\filesystem\File;
use zibo\library\optimizer\jsmin\JSMin;

/**
 * Optimize an array of js files into one file
 */
class JsOptimizer extends AbstractOptimizer {

    /**
     * File extension (or type) of this optimizer
     * @var string
     */
    const EXTENSION = 'js';

    /**
     * Path for this optimizer in the cache
     * @var string
     */
    const PATH_CACHE = 'scripts';

    /**
     * Constructs a new JS optimizer
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
     * Minifies the provided JS source
     * @param string $source JS source
     * @param zibo\library\filesystem\File $file The file of the source
     * @return string Minified JS source
     */
    protected function optimizeSource($source, File $file) {
        $minified = JSMin::minify($source);
        return str_replace('"+++', '"+ ++', $minified);
    }

}