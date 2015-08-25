<?php

namespace zibo\orm\security\controller;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;

use zibo\orm\scaffold\controller\ScaffoldController;
use zibo\orm\scaffold\form\ScaffoldForm;
use zibo\orm\scaffold\view\ScaffoldIndexView;
use zibo\orm\security\table\decorator\RoleDecorator;
use zibo\orm\security\Module;

/**
 * Role management controller
 */
class RoleController extends ScaffoldController {

    /**
     * Translation key for the add button
     * @var string
     */
    const TRANSLATION_ADD_ROLE = 'orm.security.button.add.role';

    /**
     * Gets a data table for the model
     * @param string $formAction URL where the table form will point to
     * @return zibo\library\html\table\Table
     */
    protected function getTable($formAction) {
        $table = parent::getTable($formAction);
        $table->addDecorator(new ZebraDecorator(new RoleDecorator($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/')));

        return $table;
    }

    /**
     * Gets the index view for the scaffold
     * @param zibo\library\html\table\Table $table Table with the model data
     * @return zibo\core\View
     */
    protected function getIndexView(ExtendedTable $table, array $action = null) {
        $translator = $this->getTranslator();

        $meta = $this->model->getMeta();

        $title = $translator->translate(Module::TRANSLATION_ROLES);

        $actions = array(
            $this->request->getBasePath() . '/' . self::ACTION_ADD => $translator->translate(self::TRANSLATION_ADD_ROLE),
            $this->request->getBaseUrl() . '/' . Module::ROUTE_USERS => $translator->translate(Module::TRANSLATION_BACK),
        );

        return new ScaffoldIndexView($meta, $table, $title, $actions);
    }

    /**
     * Gets the form for the data of the model
     * @param mixed $data Data object to preset the form
     * @return zibo\library\html\form\Form
     */
    protected function getForm($data = null) {
        $fields = array('permissions', 'routes');

        $form = new ScaffoldForm($this->request->getBasePath() . '/' . self::ACTION_SAVE, $this->model, $data, $fields, true);

        return $form;
    }

}