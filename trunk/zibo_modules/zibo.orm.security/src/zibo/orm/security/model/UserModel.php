<?php

namespace zibo\orm\security\model;

use zibo\core\Zibo;

use zibo\library\orm\query\ModelQuery;
use zibo\library\orm\model\ExtendedModel;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

use \Exception;

/**
 * User model
 */
class UserModel extends ExtendedModel {

    /**
     * Name of this model
     * @var string
     */
    const NAME = 'User';

    /**
     * Gets a data list of users
     * @param string $locale
     * @return array Array with the id as key and the username as value
     */
    public function getDataList($locale = null) {
        $list = array();

        $query = $this->createQuery(0, $locale);
        $query->setFields('{id}, {username}');
        $query->addOrderBy('{username}');

        $result = $query->query();
        foreach ($result as $user) {
            $list[$user->id] = $user->username;
        }

        return $list;
    }

    /**
     * Gets a user by the username
     * @param string $username The username of the user
     * @param UserData|null The user if found, null otherwise
     */
    public function getUserByUsername($username) {
        $query = $this->createQuery(2);
        $query->addCondition('{username} = %1%', $username);

        $user = $query->queryFirst();

        if ($user) {
            $user->initializePermissions();
            $user->initializeRoutes();
        }

        return $user;
    }

    /**
     * Gets a user by the email
     * @param string $email The email address of the user
     * @param UserData|null The user if found, null otherwise
     */
    public function getUserByEmail($email) {
        $query = $this->createQuery(2);
        $query->addCondition('{email} = %1%', $email);

        $user = $query->queryFirst();

        if ($user) {
            $user->initializePermissions();
            $user->initializeRoutes();
        }

        return $user;
    }

    /**
     * Find the users which match the provided part of a username
     * @param string $queryString Part of a username to match
     * @return array Array with the usernames which match the provided query
     */
    public function findUsersByUsername($queryString) {
        $list = array();

        $query = $this->createQuery(0);
        $query->setFields('{id}, {username}');
        $query->addCondition('{username} LIKE %1%', '%' . $queryString . '%');
        $query->addOrderBy('{username}');

        $result = $query->query();
        foreach ($result as $user) {
            $list[] = $user->username;
        }

        return $list;
    }

    /**
     * Find the users which match the provided part of a email address
     * @param string $queryString Part of a email address
     * @return array Array with the usernames of the users which match the provided query
     */
    public function findUsersByEmail($queryString) {
        $list = array();

        $query = $this->createQuery(0);
        $query->setFields('{id}, {username}');
        $query->addCondition('{email} LIKE %1%', '%' . $queryString . '%');
        $query->addOrderBy('{username}');

        $result = $query->query();
        foreach ($result as $user) {
            $list[] = $user->username;
        }

        return $list;
    }

    /**
     * Gets all the users with the provided permission
     * @param $permission
     * @return array Array with UserData objects
     */
    public function getUsersWithPermission($permission) {
        $query = $this->createQuery(2);
        $query->addJoin('INNER', PermissionModel::NAME . RoleModel::NAME, 'permissionRoles', '{permissionRoles.role} = {roles.id}');
        $query->addJoin('INNER', PermissionModel::NAME, 'permissions', '{permissionRoles.permission} = {permissions.id}');
        $query->addCondition('{roles.isSuperRole} = %1% OR {permissions.code} = %2%', true, $permission);
        $query->addOrderBy('{username} ASC');

        return $query->query();
    }

    /**
     * Validates a data object of this model
     * @param mixed $data Data object of the model
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when one of the fields is not valid
     */
    public function validate($data) {
        $exception = new ValidationException('Validation errors occured in ' . $this->getName());

        $this->dataValidator->validateData($exception, $data);

        if (isset($data->username) && !$exception->hasErrors('username')) {
            $query = $this->createQuery(0);
            $query->addCondition('{username} = %1%', $data->username);
            if ($data->id) {
                $query->addCondition('{id} <> %1%', $data->id);
            }

            if ($query->count()) {
                $error = new ValidationError('orm.security.error.username.exists', 'Username %username% is already used by another user', array('username' => $data->username));
                $exception->addErrors('username', array($error));
            }
        }

        if (isset($data->email) && $data->email && !$exception->hasErrors('email')) {
            $query = $this->createQuery(0);
            $query->addCondition('{email} = %1%', $data->email);
            if ($data->id) {
                $query->addCondition('{id} <> %1%', $data->id);
            }

            if ($query->count()) {
                $error = new ValidationError('orm.security.error.email.exists', 'Email address %email% is already used by another user', array('email' => $data->email));
                $exception->addErrors('email', array($error));
            }
        }

        if ($exception->hasErrors()) {
            throw $exception;
        }
    }

}