<?php

namespace zibo\dashboard\view;

use zibo\admin\view\BaseView;

use zibo\core\View;

/**
 * Widget properties view
 */
class WidgetPropertiesView extends BaseView {

    private $propertiesView;

    public function __construct($widgetName, View $view) {
        parent::__construct('dashboard/widget.properties');
        $this->propertiesView = $view;
        $this->set('widgetName', $widgetName);
    }

    public function render($return = true) {
        $content = $this->renderView($this->propertiesView);
        $this->set('content', $content);

        return parent::render($return);
    }

}