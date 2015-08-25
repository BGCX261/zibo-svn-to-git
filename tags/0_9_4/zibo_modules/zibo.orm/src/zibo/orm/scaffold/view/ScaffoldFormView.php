<?php

namespace zibo\orm\scaffold\view;

use zibo\admin\view\i18n\LocalizePanelView;
use zibo\admin\view\BaseView;

use zibo\library\html\form\field\HiddenField;
use zibo\library\html\form\field\SubmitField;
use zibo\library\html\form\Form;
use zibo\library\i18n\I18n;
use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\model\Model;

use zibo\orm\scaffold\form\DataForm;
use zibo\orm\scaffold\form\ScaffoldForm;
use zibo\orm\Module;

/**
 * View for a data form
 */
class ScaffoldFormView extends BaseView {

    /**
     * Template of this view
     * @var string
     */
    const TEMPLATE = 'orm/scaffold/form';

    /**
     * Path to the js script to localize the form
     * @var string
     */
    const SCRIPT_LOCALIZE = 'web/scripts/orm/localize.js';

    /**
     * Flag to set whether the data is localized
     * @var boolean
     */
    private $isLocalized;

    /**
     * Constructs a new scaffold form view
     * @param zibo\library\orm\model\meta\ModelMeta $meta Meta of the model
     * @param zibo\library\html\form\Form $form Form of the data
     * @param string $title Title for the view
     * @param mixed $data Data object set to the form
     * @param string $localizeAction URL to change the content locale
     * @param string $template Provide to override the default template
     * @return null
     */
    public function __construct(ModelMeta $meta, Form $form, $title, $data = null, $localizeAction = null, $template = null) {
        if (!$template) {
            $template = self::TEMPLATE;
        }

        parent::__construct($template);

        $this->isLocalized = $meta->isLocalized();

        $this->set('title', $title);
        $this->set('data', $data);

        $this->setPageTitle($title);

        $this->setForm($form);

        $this->setLocalizeSubview($meta, $data, $localizeAction);
        $this->setLogSubview($meta, $data);
    }

    /**
     * Sets the form to the view
     * @param zibo\library\html\form\Form $form Form of the data
     * @return null
     */
    protected function setForm(Form $form) {
        $fields = array();
        $hiddenFields = array();

        $fieldIterator = $form->getFields()->getIterator();
        foreach ($fieldIterator as $fieldName => $field) {
            if ($field instanceof SubmitField) {
                continue;
            }

            if (!($field instanceof HiddenField)) {
                $fields[$fieldName] = $fieldName;
                continue;
            }

            if ($fieldName == Form::SUBMIT_NAME . $form->getName()) {
                continue;
            }

            $hiddenFields[$fieldName] = $fieldName;
        }

        if ($form instanceof ScaffoldForm) {
            $fieldLabels = $form->getFieldLabels();
        } else {
            $fieldLabels = array();
        }

        $this->set('form', $form);
        $this->set('fields', $fields);
        $this->set('hiddenFields', $hiddenFields);
        $this->set('fieldLabels', $fieldLabels);
    }

    /**
     * Sets the localize subview to the view if necessairy
     * @param zibo\library\orm\model\meta\ModelMeta $meta Meta of the model
     * @param mixed $data Data object of the form
     * @return null
     */
    protected function setLocalizeSubview(ModelMeta $meta, $data, $action = null) {
        if (!$this->isLocalized || !$data || !$data->id) {
            return;
        }

        $localizedFields = array();

        $fields = $meta->getFields();
        foreach ($fields as $fieldName => $field) {
            if (!$field->isLocalized()) {
                continue;
            }

            $localizedFields[$fieldName] = $fieldName;
        }

        $this->set('localizedFields', $localizedFields);

        $form = $this->get('form');

        $this->addJavascript(self::SCRIPT_LOCALIZE);
        $this->addInlineJavascript("ZiboOrmInitializeLocalizedForm('" . $form->getId() . "');");

        $localizeView = new LocalizeView($meta, $data, $action);

        $this->setSubView('localize', $localizeView);
    }

    /**
     * Sets the log subview to the view if necessairy
     * @param zibo\library\orm\model\meta\ModelMeta $meta Meta of the model
     * @param mixed $data Data object of the form
     * @return null
     */
    protected function setLogSubview(ModelMeta $meta, $data) {
        if (!$meta->isLogged()) {
            return;
        }

        if (!$data || empty($data->id)) {
            return;
        }

        $logView = new LogView($meta->getName(), $data->id);

        $this->setSubView('log', $logView);
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