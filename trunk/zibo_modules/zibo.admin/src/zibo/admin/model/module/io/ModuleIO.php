<?php

namespace zibo\admin\model\module\io;

use zibo\library\filesystem\File;

/**
 * Interface for the input/output of module definitions
 */
interface ModuleIO {

    /**
     * Read the modules from the provided path
     * @param zibo\library\filesystem\File $path Path to read the module definitions from
     * @return array Array with Module instances
     * @throws zibo\admin\model\exception\ModuleDefinitionNotFoundException when no module definition could be read from the provided path
     */
    public function readModules(File $path);

    /**
     * Write modules to the provided path
     * @param zibo\library\filesystem\File $path Path to write the modules definitions to
     * @param array $modules Array with Module instances
     * @return null
     */
    public function writeModules(File $path, array $modules);

}