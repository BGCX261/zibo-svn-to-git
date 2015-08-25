<?php

namespace zibo\library\orm\loader;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\orm\loader\io\ModelIO;
use zibo\library\Structure;

/**
 * Read models from their definition
 */
class ModelReader {

    /**
     * Input/output implementation of the model definition
     * @var zibo\library\orm\loader\io\ModelIO
     */
    private $io;

    /**
     * Construct this model reader
     * @param zibo\library\orm\model\loader\ModelIO $io
     * @return null
     */
    public function __construct(ModelIO $io) {
        $this->io = $io;
    }

    /**
     * Gets the i/o implementation of the model definition
     * @return zibo\library\orm\loader\io\ModelIO
     */
    public function getModelIO() {
        return $this->io;
    }

    /**
     * Read all the models in the Zibo file system structure
     * @param boolean $onlyInModules Set to true to only read in the modules directory
     * @return array Array with Model instances
     */
    public function readModelsFromIncludePaths($onlyInModules = false) {
        $models = array();

        $includePaths = array_reverse(Zibo::getInstance()->getIncludePaths());
        if ($onlyInModules) {
            array_pop($includePaths);
            array_shift($includePaths);
        }

        foreach ($includePaths as $includePath) {
            $pathModels = $this->readModelsFromPath(new File($includePath));
            $models = Structure::merge($models, $pathModels);
        }

        return $models;
    }

    /**
     * Read all the models in a path of the Zibo file system structure
     * @param zibo\library\filesystem\File $path
     * @return array Array with Model instances
     */
    public function readModelsFromPath(File $path) {
        return $this->io->readModelsFromPath($path);
    }

}