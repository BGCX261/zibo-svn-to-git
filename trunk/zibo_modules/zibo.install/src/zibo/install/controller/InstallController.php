<?php

namespace zibo\install\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\view\HtmlView;

use zibo\install\form\InstallWizard;
use zibo\install\view\BaseView;

class InstallController extends AbstractController {

    public function indexAction() {
        $wizard = new InstallWizard($this->request->getBasePath());
//
//        echo '<pre>';
//        print_r($_REQUEST);
//        print_r($_SESSION);
//        echo '</pre>';

        $environment = $this->getEnvironment();
        $action = $environment->getArgument('action');
        if ($action == 'reset') {
            $wizard->reset();
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $wizard->invoke($this->request, $this->response);

        $step = $wizard->getCurrentStep();

        $view = $this->response->getView();
        if ($view instanceof HtmlView) {
            $view = new BaseView($step, $view);
        }

        $this->response->setView($view);
    }

}