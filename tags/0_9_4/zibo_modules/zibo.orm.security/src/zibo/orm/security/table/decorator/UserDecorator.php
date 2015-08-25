<?php

namespace zibo\orm\security\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\i18n\I18n;

use zibo\orm\security\model\data\UserData;

/**
 * Decorator for a user
 */
class UserDecorator implements Decorator {

    /**
     * Translation key for the roles label
     * @var string
     */
    const TRANSLATION_ROLES = 'orm.security.label.roles';

    /**
     * Translation key for the inactive label
     * @var string
     */
    const TRANSLATION_INACTIVE = 'orm.security.label.inactive';

    /**
     * URL where the username will point to
     * @var string
     */
    private $action;

    /**
     * Instance of the translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new user decorator
     * @param string $action URL where the username will point to
     * @return null
     */
    public function __construct($action = null) {
        $this->action = $action;
        $this->translator = I18n::getInstance()->getTranslator();
    }

    /**
     * Decorates the user in the provided cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row of the cell to decorate
     * @param integer $rowNumber Number of the current row
     * @param array $remainingRows Array with the values of the remaining rows
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingRows) {
        $user = $cell->getValue();

        if (!($user instanceof UserData)) {
            $cell->setValue('');
            return;
        }

        $html = $user->username;
        if ($this->action) {
            $anchor = new Anchor($html, $this->action . $user->id);
            $html = $anchor->getHtml();
        }

        $userEmail = $user->getUserEmail();
        $userRoles = $user->getUserRoles();

        $html .= '<div class="info">';

        if ($userEmail) {
            $html .= $userEmail . '<br />';
        }

        if (!$user->isActive) {
            $html .= $this->translator->translate(self::TRANSLATION_INACTIVE) . '<br />';
        }

        if ($userRoles) {
            $roles = array();
            foreach ($userRoles as $role) {
                $roles[] = $role->getRoleName();
            }

            $html .= $this->translator->translate(self::TRANSLATION_ROLES) . ': ' . implode(', ', $roles);
        }

        $html .= '</div>';

        $cell->setValue($html);
    }

}