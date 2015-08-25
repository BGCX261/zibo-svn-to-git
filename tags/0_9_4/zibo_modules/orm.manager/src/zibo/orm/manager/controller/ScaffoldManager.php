<?php

namespace zibo\orm\manager\controller;

use zibo\library\html\form\Form;
use zibo\library\html\table\ExtendedTable;
use zibo\library\orm\model\meta\ModelMeta;

use zibo\manager\model\Manager;

use zibo\orm\manager\view\ScaffoldManagerFormView;
use zibo\orm\manager\view\ScaffoldManagerIndexView;
use zibo\orm\scaffold\controller\ScaffoldController;

/**
 * Scaffold manager
 */
class ScaffoldManager extends ScaffoldController implements Manager {

    /**
     * Translation key for the overview button
     * @var string
     */
    const TRANSLATION_OVERVIEW = 'orm.button.overview';

    /**
     * Name of this manager
     * @var string
     */
    private $name;

    /**
     * Path to the icon of this manager
     * @var string
     */
    private $icon;

    /**
     * Constructs a new scaffold manager
     * @param string $modelName Name of the model to scaffold
     * @param string $nameTranslation Translation key of the manager name
     * @param string $icon Path to the icon of the manager
     * @param boolean $isReadOnly Set to true to make the scaffolding read only
     * @param boolean|array $search Boolean to enable or disable the search functionality, an array of field names to query is also allowed to enable the search
     * @param boolean|array $order Boolean to enable or disable the order functionality, an array of field names to order is also allowed to enable the order
     * @param boolean|array $pagination Boolean to enable or disable the pagination functionality, an array of pagination options is also allowed to enable the pagination
     * @return null
     */
    public function __construct($modelName, $nameTranslation, $icon = null, $isReadOnly = false, $search = true, $order = true, $pagination = true) {
        parent::__construct($modelName, $isReadOnly, $search, $order, $pagination);
        $this->name = $this->getTranslator()->translate($nameTranslation);
        $this->icon = $icon;
        $this->translationTitle = $nameTranslation;
    }

    /**
     * Gets the name of this manager
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the icon of this manager
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Gets the actions for this manager
     * return array Array with the URL as key and the label as value
     */
    public function getActions() {
        $translator = $this->getTranslator();

        $actions = array();
        if (!$this->isReadOnly) {
            $actions[self::ACTION_ADD] = $translator->translate($this->translationAdd);
        }
        $actions[''] = $translator->translate($this->translationOverview);

        return $actions;
    }

    /**
     * Creates the actual form view
     * @param zibo\library\orm\model\meta\ModelMeta $meta
     * @param zibo\library\html\table\ExtendedTable $table
     * @param string $title
     * @param array $viewActions
     * @return zibo\core\View
     */
    protected function constructIndexView(ModelMeta $meta, ExtendedTable $table, $title, $viewActions) {
        return new ScaffoldManagerIndexView($meta, $table, $title, $viewActions);
    }

    /**
     * Gets the form view for the scaffold
     * @param zibo\library\html\form\Form $form Form of the data
     * @param mixed $data Data object
     * @return zibo\core\View;
     */
    protected function constructFormView(ModelMeta $meta, Form $form, $title, $data = null, $localizeAction = null) {
        return new ScaffoldManagerFormView($meta, $form, $title, $data, $localizeAction);
    }

}