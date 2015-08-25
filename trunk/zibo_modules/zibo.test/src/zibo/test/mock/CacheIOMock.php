<?php

namespace zibo\test\mock;

use zibo\library\cache\io\MemoryCacheIO;

class CacheIOMock extends MemoryCacheIO {

    public function __destruct() {
        // intentionally don't call parent's __destruct() method
        // because we don't want our mock to write to any files
    }
}

?>
