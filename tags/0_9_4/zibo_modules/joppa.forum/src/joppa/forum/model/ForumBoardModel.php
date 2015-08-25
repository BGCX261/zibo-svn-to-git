<?php

namespace joppa\forum\model;

use zibo\library\i18n\translation\translator\Translator;
use zibo\library\i18n\I18n;
use zibo\library\orm\model\ExtendedModel;

use zibo\ZiboException;

/**
 * Model of the forum boards
 */
class ForumBoardModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'ForumBoard';

	/**
	 * Flag to disallow new topics
	 * @var integer
	 */
	const DISALLOW = 0;

	/**
	 * Flag to allow new topics for everybody
	 * @var integer
	 */
	const ALLOW_EVERYBODY = 1;

	/**
	 * Flag to allow new topics for registered users only
	 * @var integer
	 */
	const ALLOW_REGISTERED = 2;

	/**
	 * Translation key for the label of the disallow flag
	 * @var string
	 */
	const TRANSLATION_DISALLOW = 'joppa.forum.label.disallow';

	/**
	 * Translation key for the label of the allow for everybody flag
	 * @var string
	 */
	const TRANSLATION_ALLOW_EVERYBODY = 'joppa.forum.label.allow.everybody';

	/**
	 * Translation key for the label of the allow for registered users only flag
	 * @var string
	 */
	const TRANSLATION_ALLOW_REGISTERED = 'joppa.forum.label.allow.registered';

	/**
	 * Gets a board without it's topics
	 * @param integer $idBoard Id of the board
	 * @param integer $recursiveDepth Number of levels to fetch
	 * @return null|joppa\forum\model\data\ForumBoardData
	 */
	public function getBoard($idBoard, $recursiveDepth) {
		$query = $this->createQuery($recursiveDepth);
		$query->removeFields('{topics}');
		$query->addCondition('{id} = %1%', $idBoard);

		return $query->queryFirst();
	}

	/**
	 * Gets the boards for a category
	 * @param integer $categoryId Id of the category
	 * @param integer $recursiveDepth Number of levels to fetch
	 * @return array Array of ForumBoardData objects
	 */
	public function getBoardsForCategory($idCategory, $recursiveDepth = 1) {
		$query = $this->createQuery($recursiveDepth);
		$query->removeFields('{topics}');
		$query->addCondition('{category} = %1%', $idCategory);

		$boards = $query->query();

        $profileModel = $this->getModel(ForumProfileModel::NAME);
        $topicModel = $this->getModel(ForumTopicModel::NAME);

		foreach ($boards as $board) {
            $board->moderators = $profileModel->getModeratorsForBoard($board->id);
            $board->lastTopic = $topicModel->getLastTopicForBoard($board->id);
		}

		return $boards;
	}

	/**
	 * Checks whether the provided profile is a moderator of the provided board
	 * @param integer $idProfile Id of the profile
	 * @param integer $idBoard Id of the board
	 * @return boolean True if the profile is a moderator of the board, false otherwise
	 */
	public function isProfileModerator($idProfile, $idBoard) {
		$query = $this->createQuery(0);
		$query->addCondition('{id} = %1%', $idBoard);
		$query->addCondition('{moderators.id} = %1%', $idProfile);

		$count = $query->count();

		if ($count) {
			return true;
		}
		return false;
	}

    /**
     * Update the order index of the provided boards in a category
     * @param integer $idCategory Id of the category
     * @param array $order Array with the id's of the boards in the order they need to be
     * @return null
     */
    public function orderBoards($idCategory, array $order) {
        $query = $this->createQuery(0);
        $query->setFields('{id}, {orderIndex}');
        $query->addCondition('{category} = %1%', $idCategory);
        $query->addOrderBy('{orderIndex} ASC');

        $boards = $query->query();

        $index = 1;

        $transactionStarted = $this->startTransaction();
        try {
            foreach ($order as $boardId) {
                if (!array_key_exists($boardId, $boards)) {
                    throw new Exception('Provided board does not exist in the provided category (id: ' . $boardId . ')');
                }

                $boards[$boardId]->orderIndex = $index;

                $this->save($boards[$boardId]);

                unset($boards[$boardId]);

                $index++;
            }

            foreach ($boards as $board) {
                $board->orderIndex = $index;

                $this->save($board);

                $index++;
            }

            $this->commitTransaction($transactionStarted);
        } catch (Exception $exception) {
            $this->rollbackTransaction($transactionStarted);

            throw $exception;
        }
    }

    /**
     * Checks if the provided user is allowed
     * @param integer $flag Allow flag
     * @param User $user
     * @return boolean True if the user is allowed, false otherwiser
     */
    public static function isAllowed($flag, $user) {
    	switch ($flag) {
    		case self::DISALLOW:
    			return false;
    		case self::ALLOW_EVERYBODY:
    			return true;
    		case self::ALLOW_REGISTERED:
    			if ($user) {
    				return true;
    			}
    			return false;
    		default:
    			throw new ZiboException('Could not check if the user is allowed: Invalid flag provided');
    	}
    }

    /**
     * Gets the options for the allow new topics field
     * @return array
     */
    public static function getAllowOptions(Translator $translator = null) {
    	if (!$translator) {
    		$translator = I18n::getInstance()->getTranslator();
    	}

        return array(
            ForumBoardModel::DISALLOW => $translator->translate(self::TRANSLATION_DISALLOW),
            ForumBoardModel::ALLOW_EVERYBODY => $translator->translate(self::TRANSLATION_ALLOW_EVERYBODY),
            ForumBoardModel::ALLOW_REGISTERED => $translator->translate(self::TRANSLATION_ALLOW_REGISTERED),
        );
    }

}