<?php

namespace zibo\user\view;

use zibo\admin\view\BaseView;

use zibo\user\form\UserSwitchForm;

/**
 * View to switch a user with username
 */
class UserSwitchView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'user/user.switch';

    /**
     * Constructs a new switch user view
     * @param zibo\user\form\UserSwitchForm $form
     * @return null
     */
    public function __construct(UserSwitchForm $form) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);

        $this->addInlineJavascript('$("#formUserSwitchUsername").focus();');
    }

}