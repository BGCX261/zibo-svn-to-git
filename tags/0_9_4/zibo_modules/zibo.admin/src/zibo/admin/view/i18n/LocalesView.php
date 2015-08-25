<?php

namespace zibo\admin\view\i18n;

use zibo\admin\view\BaseView;

use zibo\jquery\Module;

/**
 * View of the installed modules
 */
class LocalesView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/i18n/locales';

    /**
     * Path to the JS script of this view
     * @var string
     */
    const SCRIPT_LOCALES = 'web/scripts/admin/locales.js';

    /**
     * Path to the CSS of this view
     * @var string
     */
    const STYLE_LOCALES = 'web/styles/admin/locales.css';

    /**
     * Constructs a new locales view
     * @param string $sortAction URL where the dnd of the locales will point to
     * @param array $locales Array with Locale objects
     * @return null
     */
    public function __construct($sortAction, array $locales) {
        parent::__construct(self::TEMPLATE);

        $this->set('locales', $locales);

        $this->addStyle(self::STYLE_LOCALES);
        $this->addJavascript(self::SCRIPT_LOCALES);
        $this->addJavascript(Module::SCRIPT_JQUERY_UI);
        $this->addInlineJavascript('ziboAdminInitializeLocales("' . $sortAction . '");');
    }

}