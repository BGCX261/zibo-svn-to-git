<?php

namespace zibo\orm\security\controller;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;

use zibo\orm\scaffold\controller\ScaffoldController;
use zibo\orm\scaffold\view\ScaffoldIndexView;
use zibo\orm\security\table\decorator\PermissionDecorator;
use zibo\orm\security\Module;

/**
 * Permission management controller
 */
class PermissionController extends ScaffoldController {

    /**
     * Gets a data table for the model
     * @param string $formAction URL where the table form will point to
     * @return zibo\library\html\table\Table
     */
    protected function getTable($formAction) {
        $table = parent::getTable($formAction);
        $table->addDecorator(new ZebraDecorator(new PermissionDecorator($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/')));

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

        $title = $translator->translate(Module::TRANSLATION_PERMISSIONS);

        $actions = array(
            $this->request->getBaseUrl() . '/' . Module::ROUTE_USERS  => $translator->translate(Module::TRANSLATION_BACK),
        );

        return new ScaffoldIndexView($meta, $table, $title, $actions);
    }

}