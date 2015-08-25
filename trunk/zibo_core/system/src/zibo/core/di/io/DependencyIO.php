<?php

namespace zibo\core\di\io;

use zibo\core\Zibo;

/**
 * Interface to get the dependency container
 */
interface DependencyIO {

    /**
     * Gets the dependency container
     * @param zibo\core\Zibo $zibo Instance of zibo
     * @return zibo\core\di\DependencyContainer
     */
    public function getContainer(Zibo $zibo);

}