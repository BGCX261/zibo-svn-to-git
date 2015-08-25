<?php

namespace zibo\user\view;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the panel to switch a user with username
 */
class UserSwitchPanelView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'user/user.switch.panel';

    /**
     * Constructs a new switch user panel view
     * @return null
     */
    public function __construct() {
        parent::__construct(self::TEMPLATE);
    }

}