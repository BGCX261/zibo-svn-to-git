<?php

namespace zibo\orm\security\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\i18n\I18n;

use zibo\orm\security\model\data\RoleData;

/**
 * Decorator for a role
 */
class RoleDecorator implements Decorator {

    /**
     * Translation key for the permissions label
     * @var string
     */
    const TRANSLATION_PERMISSIONS = 'orm.security.label.permissions';

    /**
     * Translation key for the super role label
     * @var string
     */
    const TRANSLATION_SUPER_ROLE = 'orm.security.label.role.super.table';

    /**
     * URL where the name of a role will point to
     * @var string
     */
    private $action;

    /**
     * Instance of the translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new role decorator
     * @param string $action URL where the name of a role will point to
     * @return null
     */
    public function __construct($action) {
        $this->action = $action;
        $this->translator = I18n::getInstance()->getTranslator();
    }

    /**
     * Decorates the role in the provided cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingRows Array with the values of the remaining rows
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingRows) {
        $role = $cell->getValue();
        if (!($role instanceof RoleData)) {
            $cell->setValue('');
            return;
        }

        $html = $role->name;
        if ($this->action) {
            $anchor = new Anchor($html, $this->action . $role->id);
            $html = $anchor->getHtml();
        }

        if ($role->permissions || $role->isSuperRole) {
            $html .= '<div class="info">';

            if ($role->isSuperRole) {
                $html .= $this->translator->translate(self::TRANSLATION_SUPER_ROLE) . '<br />';
            }

            if ($role->permissions) {

                $permissions = array();
                foreach ($role->permissions as $permission) {
                    $permissions[] = $permission->getPermissionCode();
                }

                asort($permissions);

                $html .= $this->translator->translate(self::TRANSLATION_PERMISSIONS) . ': ' . implode(', ', $permissions);
            }

            $html .= '</div>';
        }

        $cell->setValue($html);
    }

}