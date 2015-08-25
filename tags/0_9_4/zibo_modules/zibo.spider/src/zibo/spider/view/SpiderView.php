<?php

namespace zibo\spider\view;

use zibo\admin\view\BaseView;

use zibo\jquery\Module;

use zibo\spider\form\SpiderForm;

class SpiderView extends BaseView {

    const TEMPLATE = 'spider/index';

    public function __construct(SpiderForm $form, $statusUrl = null, $reportUrl = null) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);

        $this->addStyle(Module::STYLE_JQUERY_UI);
        $this->addStyle('web/styles/spider/spider.css');

        $this->addJavascript(Module::SCRIPT_JQUERY_UI);
        $this->addJavascript('web/scripts/spider/spider.js');
        $this->addInlineJavascript('ziboSpiderInitialize("' . $statusUrl . '", "' . $reportUrl . '");');
    }

}