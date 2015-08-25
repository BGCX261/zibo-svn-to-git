<?php

namespace zibo\admin\view\security;

use zibo\admin\controller\AuthenticationController;
use zibo\admin\Module;

use zibo\core\Request;
use zibo\core\Zibo;

use zibo\library\security\SecurityManager;
use zibo\library\smarty\view\SmartyView;

/**
 * Panel to see the authentication status
 */
class UserPanelView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/security/user.panel';

    /**
     * Constructs a new authentication status panel
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TEMPLATE);

        $user = SecurityManager::getInstance()->getUser();
        $request = Zibo::getInstance()->getRequest();

        $baseUrl = $request->getBaseUrl() . Request::QUERY_SEPARATOR;
        $basePath = $baseUrl . Module::ROUTE_AUTHENTICATION . Request::QUERY_SEPARATOR;

        $this->set('user', $user);

        $this->set('urlLogin', $basePath . AuthenticationController::ACTION_LOGIN);
        $this->set('urlLogout', $basePath . AuthenticationController::ACTION_LOGOUT);
        $this->set('urlProfile', $baseUrl . Module::ROUTE_PROFILE);
    }

}