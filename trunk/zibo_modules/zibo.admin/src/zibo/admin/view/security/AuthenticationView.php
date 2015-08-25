<?php

namespace zibo\admin\view\security;

use zibo\admin\form\AuthenticationForm;
use zibo\admin\view\BaseView;

/**
 * View to authenticate a user with username and password
 */
class AuthenticationView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/security/login';

    /**
     * Constructs a new authentication view
     * @param zibo\admin\form\AuthenticationForm $form
     * @return null
     */
    public function __construct(AuthenticationForm $form) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);

        $this->addInlineJavascript('$("#formAuthenticateUsername").focus();');
    }

}