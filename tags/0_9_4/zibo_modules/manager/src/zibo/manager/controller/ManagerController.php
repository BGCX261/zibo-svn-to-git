<?php

namespace zibo\manager\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\Zibo;

use zibo\manager\model\ManagerModel;
use zibo\manager\view\ManagerView;

/**
 * Controller to dispatch the managers
 */
class ManagerController extends AbstractController {

    /**
     * Name of the current manager
     * @var string
     */
    private $managerName;

    /**
     * Dispatches the requested manager
     * @param string $managerName
     * @return null|zibo\core\Request
     */
    public function indexAction($managerName = null) {
        if (!$managerName) {
            $view = new ManagerView();
            $this->response->setView($view);
            return;
        }

        $managerModel = ManagerModel::getInstance();
        $manager = $managerModel->getManager($managerName);
        $managerClass = get_class($manager);

        $this->managerName = $managerName;

        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, 'preResponse'), 10);

        return $this->forward($managerClass);
    }

    /**
     * Surrounds the view in the response with a manager view
     * @return null
     */
    public function preResponse() {
        if ($this->response->willRedirect()) {
            return;
        }

        $managerView = $this->response->getView();

        $view = new ManagerView($managerView, $this->managerName);
        $this->response->setView($view);
    }

}