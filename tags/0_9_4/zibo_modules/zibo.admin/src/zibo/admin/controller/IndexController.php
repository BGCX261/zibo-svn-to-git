<?php

namespace zibo\admin\controller;

use zibo\admin\view\BaseView;

/**
 * Default controller
 */
class IndexController extends AbstractController {

    /**
     * Sets an empty base view to the response
     * @return null
     */
    public function indexAction() {
        if (func_num_args()) {
            $this->setError404();
            return;
        }

        $view = new BaseView();
        $this->response->setView($view);
    }

}