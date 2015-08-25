<?php

namespace zibo\orm\manager\view;

use zibo\admin\view\i18n\LocalizePanelView;
use zibo\admin\view\BaseView;

use zibo\library\html\table\Table;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\smarty\view\SmartyView;

/**
 * View for the index of a scaffold controller
 */
class ScaffoldManagerIndexView extends SmartyView {

    /**
     * Template of this view
     * @var string
     */
    const TEMPLATE = 'orm/scaffold/index';

    /**
     * Flag to set whether the data is localized
     * @var boolean
     */
    private $isLocalized;

    /**
     * Construct a new scaffold index view
     * @param boolean $isLocalized Flag to set whether the data is localized
     * @param zibo\library\html\table\Table $table Table with the model data
     * @param string $title Title for the page
     * @param array $actions Array with the URL for the action as key and the label as value
     * @return null
     */
    public function __construct(ModelMeta $meta, Table $table, $title, array $actions = null) {
        parent::__construct(self::TEMPLATE);

        $this->isLocalized = $meta->isLocalized();

        $this->set('table', $table);
        $this->set('title', $title);
        $this->set('actions', $actions);

        $this->addJavascript(BaseView::SCRIPT_TABLE);
    }

    /**
     * Prepares the taskbar and adds the taskbar to the view
     * @return null
     */
    protected function addTaskbar() {
        $localizePanelView = new LocalizePanelView($this->isLocalized);
        $this->taskbar->addNotificationPanel($localizePanelView);

        parent::addTaskbar();
    }

}