<?php

namespace zibo\admin\view\security;

use zibo\admin\table\SecurityTable;
use zibo\admin\view\BaseView;

/**
 * View to show an overview of the security settings
 */
class SecurityView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/security/security';

    /**
     * Path to the JS script of this view
     * @var string
     */
    const SCRIPT_SECURITY = 'web/scripts/admin/security.js';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_SECURITY = 'web/styles/admin/security.css';

    /**
     * Constructs a new security view
     * @param zibo\admin\table\SecurityTable $table
     * @return null
     */
    public function __construct(SecurityTable $table) {
        parent::__construct(self::TEMPLATE);

        $this->set('table', $table);

        $this->addStyle(self::STYLE_SECURITY);
        $this->addJavascript(self::SCRIPT_SECURITY);
        $this->addInlineJavascript('ziboAdminInitializeSecurity();');
    }

}