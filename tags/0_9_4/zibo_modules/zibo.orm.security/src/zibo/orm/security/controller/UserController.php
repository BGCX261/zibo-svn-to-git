<?php

namespace zibo\orm\security\controller;

use zibo\core\Zibo;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\ExtendedTable;
use zibo\library\security\SecurityManager;

use zibo\orm\scaffold\controller\ScaffoldController;
use zibo\orm\scaffold\form\ScaffoldForm;
use zibo\orm\scaffold\view\ScaffoldIndexView;
use zibo\orm\security\model\RoleModel;
use zibo\orm\security\model\UserModel;
use zibo\orm\security\table\decorator\UserDecorator;
use zibo\orm\security\Module;

/**
 * User management controller
 */
class UserController extends ScaffoldController {

    /**
     * Configuration key for the hidden roles
     * @var string
     */
    const CONFIG_HIDDEN_ROLES = 'security.roles.hidden';

    /**
     * Translation key for the username label
     * @var string
     */
    const TRANSLATION_USERNAME = 'orm.security.label.username';

    /**
     * Translation key for the email label
     * @var string
     */
    const TRANSLATION_EMAIL = 'orm.security.label.email';

    /**
     * Translation key for the active label
     * @var string
     */
    const TRANSLATION_ACTIVE = 'orm.security.label.active';

    /**
     * Translation key for the add button
     * @var string
     */
    const TRANSLATION_ADD_USER = 'orm.security.button.add.user';

    /**
     * Translation key for the manage roles button
     * @var string
     */
    const TRANSLATION_MANAGE_ROLES = 'orm.security.button.manage.roles';

    /**
     * Translation key for the manage permissions button
     * @var string
     */
    const TRANSLATION_MANAGE_PERMISSIONS = 'orm.security.button.manage.permissions';

    /**
     * Translation key for the warning when trying to edit a super user
     * @var string
     */
    const TRANSLATION_WARNING_EDIT = 'orm.security.error.user.edit';

    /**
     * The current user
     * @var zibo\library\security\model\User
     */
    private $user;

    /**
     * Constructs a new User controller
     * @return null
     */
    public function __construct() {
        $translator = $this->getTranslator();

        $isReadOnly = false;

        $search = array('username', 'email', 'roles.name');

        $order = array(
            $translator->translate(self::TRANSLATION_USERNAME) => array(
                'ASC' => '{username} ASC',
                'DESC' => '{username} DESC',
            ),
            $translator->translate(self::TRANSLATION_EMAIL) => array(
                'ASC' => '{email} ASC, {username} ASC',
                'DESC' => '{email} DESC, {username} ASC',
            ),
            $translator->translate(self::TRANSLATION_ACTIVE) => array(
                'ASC' => '{isActive} DESC, {username} ASC',
                'DESC' => '{isActive} ASC, {username} ASC',
            ),
        );

        $pagination = true;

        parent::__construct(UserModel::NAME, $isReadOnly, $search, $order, $pagination);
    }

    /**
     * Gets the user from the security manager to use in this controller
     * @return null
     */
    public function preAction() {
        $this->user = SecurityManager::getInstance()->getUser();
    }

    /**
     * Gets the data for the edit action
     * @param integer $id Primary key of the data to retrieve
     * @return mixed Data object for the provided id
     */
    public function getData($id) {
        $data = parent::getData($id);

        if (!$this->user || $this->user->isSuperUser()) {
            return $data;
        } elseif ($data->isSuperUser()) {
            $this->addWarning(self::TRANSLATION_WARNING_EDIT);
            return null;
        }

        return $data;
    }

    /**
     * Saves the user to the model
     * @param mixed $data
     * @return null
     */
    protected function saveData($data) {
        if (empty($data->password)) {
            unset($data->password);
        } else {
            $data->setUserPassword($data->password);
        }

        $data = $this->model->save($data);
    }

    /**
     * Gets a data table for the model
     * @param string $formAction URL where the table form will point to
     * @return zibo\library\html\table\ExtendedTable
     */
    protected function getTable($formAction) {
        $table = parent::getTable($formAction);
        $table->addDecorator(new ZebraDecorator(new UserDecorator($this->request->getBasePath() . '/' . self::ACTION_EDIT . '/')));

        if ($this->user && !$this->user->isSuperUser()) {
            $query = $table->getModelQuery();
            $query->setDistinct(true);
            $query->addCondition('{roles.isSuperRole} = 0 OR {roles.isSuperRole} IS NULL');

            $hiddenRoleIds = $this->getHiddenRoleIds();
            if ($hiddenRoleIds) {
                foreach ($hiddenRoleIds as $roleId) {
                    $query->addCondition('{roles.id} <> %1%', $roleId);
                }
            }
        }

        return $table;
    }

    /**
     * Gets the index view for the scaffold
     * @param zibo\library\html\table\Table $table Table with the model data
     * @return zibo\core\View
     */
    protected function getIndexView(ExtendedTable $table, array $actions = null) {
        $translator = $this->getTranslator();

        $meta = $this->model->getMeta();

        $title = $translator->translate(Module::TRANSLATION_USERS);

        if (!$this->user || ($this->user && $this->user->isSuperUser())) {
            $actions = array(
                $this->request->getBasePath() . '/' . self::ACTION_ADD => $translator->translate(self::TRANSLATION_ADD_USER),
                $this->request->getBaseUrl() . '/' . Module::ROUTE_ROLES => $translator->translate(self::TRANSLATION_MANAGE_ROLES),
                $this->request->getBaseUrl() . '/' . Module::ROUTE_PERMISSIONS => $translator->translate(self::TRANSLATION_MANAGE_PERMISSIONS),
            );
        } else {
            $actions = array(
                $this->request->getBasePath() . '/' . self::ACTION_ADD => $translator->translate(self::TRANSLATION_ADD_USER),
            );
        }

        return new ScaffoldIndexView($meta, $table, $title, $actions);
    }

    /**
     * Gets the form for the data of the model
     * @param mixed $data Data object to preset the form
     * @return zibo\library\html\form\Form
     */
    protected function getForm($data = null) {
        $fields = array('preferences');
        $form = new ScaffoldForm($this->request->getBasePath() . '/' . self::ACTION_SAVE, $this->model, $data, $fields, true);

        if (!(!$this->user || ($this->user && $this->user->isSuperUser()))) {
            $superRoles = $this->getModel(RoleModel::NAME)->getSuperRoles();
            $hiddenRoles = $this->getHiddenRoleIds();

            $field = $form->getField('roles');
            $roles = $field->getOptions();

            foreach ($superRoles as $role) {
                if (array_key_exists($role->id, $roles)) {
                    unset($roles[$role->id]);
                }
            }

            foreach ($hiddenRoles as $role) {
                if (array_key_exists($role, $roles)) {
                    unset($roles[$role]);
                }
            }

            $field->setOptions($roles);
        }

        $form->setIsDisabled(true, 'dateLastLogin');
        $form->setIsDisabled(true, 'lastIp');

        return $form;
    }

    /**
     * Gets the hidden roles as defined in the configuration. There roles, and users thereof, are only viewable for a superuser.
     * @return array Array with the role id as value
     */
    private function getHiddenRoleIds() {
        $roleIds = Zibo::getInstance()->getConfigValue(self::CONFIG_HIDDEN_ROLES);
        if (!$roleIds) {
            return array();
        }

        $roleIds = explode(',', $roleIds);
        foreach ($roleIds as $index => $roleId) {
            $roleIds[$index] = trim($roleId);
        }

        return $roleIds;
    }

}