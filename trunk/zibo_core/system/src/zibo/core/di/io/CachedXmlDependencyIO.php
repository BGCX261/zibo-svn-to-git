<?php

namespace zibo\core\di\io;

/**
 * A cached XML DependencyIO defined in a class so it can be set from the
 * configuration.
 */
class CachedXmlDependencyIO extends CachedDependencyIO {

    /**
     * Constructs a new cached XmlDependencyIO
     * @return null
     */
    public function __construct() {
        parent::__construct(new XmlDependencyIO());
    }

}