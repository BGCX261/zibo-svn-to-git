<?php

namespace joppa\forum\model;

use joppa\forum\Module;

use zibo\library\orm\model\ExtendedModel;

use zibo\orm\security\model\UserModel;

/**
 * Model of the forum profile. A forum profile is the profile for a user within the forum. All his
 * forum data and forum preferences will be stored in this model.
 */
class ForumProfileModel extends ExtendedModel {

	/**
	 * Name of the model
	 */
	const NAME = 'ForumProfile';

	/**
	 * Gets the list of the available moderators
	 * @return array Array with the id of the profile as key and the username as value
	 */
	public function getModeratorList() {
        $moderators = array();
        $userModel = $this->getModel(UserModel::NAME);

        $users = $userModel->getUsersWithPermission(Module::PERMISSION_MODERATOR);
        foreach ($users as $user) {
        	$profile = $this->getForumProfileForUser($user->id);

        	if (!$profile->id) {
        		$this->save($profile);
        	}

            $moderators[$profile->id] = $user->getUserName();
        }

        return $moderators;
	}

	/**
	 * Gets the profiles of the moderators of the provided board
	 * @param integer $idBoard Id of the board
	 * @return array Array with ProfileData objects
	 */
	public function getModeratorsForBoard($idBoard) {
		$query = $this->createQuery(1);
		$query->addJoin('INNER', 'ForumBoardModerator', 'fbm', '{fbm.forumProfile} = {id}');
		$query->addCondition('{fbm.forumBoard} = %1%', $idBoard);
		$query->addOrderBy('{user.username}');

		return $query->query();
	}

	/**
	 * Gets a forum profile for the provided user
	 * @param integer $idUser Id of the user
	 * @return joppa\forum\model\data\ForumProfileData
	 */
	public function getForumProfileForUser($idUser) {
		$profile = $this->findFirstBy('user', $idUser, 0);
		if ($profile) {
			return $profile;
		}

		$userModel = $this->getModel(UserModel::NAME);
		$user = $userModel->findById($idUser, 0);
		if (!$user) {
			throw new ZiboException('Provided user does not exist');
		}

		$profile = $this->createData();
		$profile->user = $user->id;
		$profile->name = $user->username;

		return $profile;
	}

	/**
	 * Adds one post to the profile's total number of posts
	 * @param integer|joppa\forum\model\data\ForumProfileData $profile Id of the profile or the profile itself
	 * @return null
	 */
	public function addPost($profile) {
		$idProfile = $this->getPrimaryKey($profile);
		if (!$idProfile) {
			return;
		}

		$query = $this->createQuery();
		$query->setFields('{id}, {numPosts}');
		$query->addCondition('{id} = %1%', $idProfile);

		$profile = $query->queryFirst();
		if (!$profile) {
			return;
		}

		$profile->numPosts++;

		$this->save($profile);
	}

    /**
     * Substracts one post from the profile's total number of posts
     * @param integer|joppa\forum\model\data\ForumProfileData $profile Id of the profile or the profile itself
     * @return null
     */
	public function removePost($profile) {
		$idProfile = $this->getPrimaryKey($profile);
		if (!$idProfile) {
			return;
		}

        $query = $this->createQuery();
        $query->setFields('{id}, {numPosts}');
        $query->addCondition('{id} = %1%', $idProfile);

        $profile = $query->queryFirst();
        if (!$profile) {
        	return;
        }

        $profile->numPosts--;

        $this->save($profile);
	}

}