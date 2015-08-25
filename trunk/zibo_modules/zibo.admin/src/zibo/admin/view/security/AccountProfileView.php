<?php

namespace zibo\admin\view\security;

use zibo\admin\form\ProfileForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the account profile hook
 */
class AccountProfileView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/security/profile.account';

    /**
     * Constructs a new view for the account profile hook
     * @return null
     */
    public function __construct(ProfileForm $form) {
        parent::__construct(self::TEMPLATE);

        $this->set('user', $form->getUser());
        $this->set('form', $form);

        $this->addInlineJavascript('$("#formProfileEmail").focus();');
    }

}