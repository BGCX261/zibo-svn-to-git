<?php

namespace zibo\core\router;

use zibo\ZiboException;

use zibo\test\BaseTestCase;

class RouteTest extends BaseTestCase {

    public function testConstruct() {
        $path = 'test/tester';
        $controllerClass = 'controller';
        $action = '*';

        $route = new Route($path, $controllerClass, $action);

        $this->assertEquals($path, $route->getPath());
        $this->assertEquals($controllerClass, $route->getControllerClass());
        $this->assertEquals($action, $route->getAction());
    }

    /**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidPathProvided
     */
    public function testConstructThrowsExceptionWhenInvalidPathProvided($path) {
        try {
            new Route($path, 'controller');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

    public function providerConstructThrowsExceptionWhenInvalidPathProvided() {
        return array(
           array(null),
           array('"!çà'),
        );
    }
}