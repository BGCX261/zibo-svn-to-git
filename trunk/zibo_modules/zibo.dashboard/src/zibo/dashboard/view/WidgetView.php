<?php

namespace zibo\dashboard\view;

use zibo\library\smarty\view\SmartyView;

class WidgetView extends SmartyView {

    public function __construct($widgetId, $title, $content, $propertiesAction = null, $minimized = false) {
        parent::__construct('dashboard/widget');

        $this->set('widgetId', $widgetId);
        $this->set('propertiesAction', $propertiesAction);
        $this->set('minimized', $minimized);
        $this->set('title', $title);
        $this->set('content', $content);
    }

}