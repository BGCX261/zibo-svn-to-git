<?php

namespace joppa\security\controller;

use zibo\library\security\SecurityManager;
use zibo\library\widget\controller\AbstractWidget;

/**
 * Widget to logout the current user
 */
class LogoutWidget extends AbstractWidget {

	/**
	 * Path to the icon of the widget
	 * @var string
	 */
	const ICON = 'web/images/joppa/widget/logout.png';

	/**
	 * Translation key for the name of the widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.security.widget.logout.name';

    /**
     * Constructs a new logout widget
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Action to logout the current user and redirect to the home page
     * @return null
     */
    public function indexAction() {
        $securityManager = SecurityManager::getInstance();
        $securityManager->logout();
        $this->response->setRedirect($this->request->getBaseUrl());
    }

}