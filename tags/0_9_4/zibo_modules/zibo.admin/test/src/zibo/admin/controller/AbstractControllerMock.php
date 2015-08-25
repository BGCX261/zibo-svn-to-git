<?php

namespace zibo\admin\controller;

class AbstractControllerMock extends AbstractController {

    public function mockForward($controllerClass, $action = null, $parameters = false, $basePath = null) {
        return $this->forward($controllerClass, $action, $parameters, $basePath);
    }

}