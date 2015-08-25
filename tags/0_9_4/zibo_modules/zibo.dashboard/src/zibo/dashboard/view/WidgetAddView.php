<?php

namespace zibo\dashboard\view;

use zibo\admin\view\BaseView;

use zibo\dashboard\Module;

use zibo\library\i18n\I18n;

/**
 * Main dashboard view
 */
class WidgetAddView extends BaseView {

    /**
     * Construct the main dashboard view
     * @param array $widgets
     * @param string $returnAction
     * @param string $saveAction
     * @return null
     */
    public function __construct(array $widgets, $returnAction, $saveAction) {
        parent::__construct('dashboard/widget.add');

        $translator = I18n::getInstance()->getTranslator();
        $saveMessage = $translator->translate('dashboard.message.added');

        $this->set('returnAction', $returnAction);
        $this->set('widgets', $widgets);

        $this->addStyle(Module::STYLE_DASHBOARD);
        $this->addJavascript(Module::SCRIPT_DASHBOARD);
        $this->addInlineJavascript('dashboardSaveAction = "' . $saveAction . '";');
        $this->addInlineJavascript('dashboardSaveMessage = "' . $saveMessage . '";');
    }

}