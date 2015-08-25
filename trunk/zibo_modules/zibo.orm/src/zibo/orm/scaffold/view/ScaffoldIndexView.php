<?php

namespace zibo\orm\scaffold\view;

use zibo\admin\view\i18n\LocalizePanelView;
use zibo\admin\view\BaseView;

use zibo\library\html\table\Table;
use zibo\library\orm\model\meta\ModelMeta;

/**
 * View for the index of a scaffold controller
 */
class ScaffoldIndexView extends BaseView {

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

        $this->setPageTitle($title);

        $this->addJavascript(self::SCRIPT_TABLE);
    }

    /**
     * Prepares the taskbar and adds the taskbar to the view
     * @return null
     */
    protected function addTaskbar() {
        $hasLocalizePanel = false;

        $panels = $this->taskbar->getNotificationPanels();
        foreach ($panels as $panel) {
            if ($panel instanceof LocalizePanelView) {
                $hasLocalizePanel = true;
                break;
            }
        }

        if (!$hasLocalizePanel) {
            $localizePanelView = new LocalizePanelView($this->isLocalized);
            $this->taskbar->addNotificationPanel($localizePanelView);
        }

        parent::addTaskbar();
    }

}