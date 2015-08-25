<?php

namespace zibo\library\orm\loader\io;

use zibo\library\filesystem\File;

/**
 * Interface to read and write model definitions
 */
interface ModelIO {

    /**
     * Read models from a path in the Zibo file system structure. The implementation will decide the file name.
     * @param zibo\library\filesystem\File $path path in the Zibo file system structure (eg. ./modules/zibo.orm.country or ./application)
     * @return array Array with Model instances
     */
    public function readModelsFromPath(File $path);

    /**
     * Write the model definitions of the provided models to a model definition file in the provided path in the Zibo file system structure.
     * @param zibo\library\filesystem\File $path path in the Zibo file system structure (eg. ./modules/zibo.orm.country or ./application)
     * @param array $models models to write to file
     * @return null
     */
    public function writeModelsToPath(File $path, array $models);

}