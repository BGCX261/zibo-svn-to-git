<?php

namespace zibo\core;

use zibo\test\BaseTestCase;

class ErrorHandlerTest extends BaseTestCase {

    /**
     * @expectedException \ErrorException
     */
    public function testHandleError() {
        $errorHandler = new ErrorHandler();
        $errorHandler->registerErrorHandler();

        fopen('/test/unexistantFile', 'r');

        $value = 10 / 0;

        restore_error_handler();
    }

}