<?php

namespace zibo\install\view;

use zibo\core\View;

use zibo\install\Module;

use zibo\library\i18n\I18n;
use zibo\library\smarty\view\SmartyView;

class BaseView extends SmartyView {

    const TEMPLATE = 'install/index';

    const STYLE = 'web/styles/install/style.css';

    public function __construct($step, View $contentView = null) {
        $tempDirectory = Module::getTempDirectory();

        parent::__construct(self::TEMPLATE, $tempDirectory);

        $this->set('step', $step);
        $this->set('locale', I18n::getInstance()->getLocale()->getCode());

        if ($contentView) {
            $this->setSubview('content', $contentView);
        }

        $this->addStyle(self::STYLE);
    }

}