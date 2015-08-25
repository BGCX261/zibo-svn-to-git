<?php

namespace joppa\forum\model;

use joppa\forum\model\data\ForumPostData;

use zibo\library\orm\model\ExtendedModel;

use zibo\ZiboException;

use \Exception;

/**
 * Model of the forum topics
 */
class ForumTopicModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'ForumTopic';

    /**
     * Gets a topic without it's posts
     * @param integer $idTopic Id of the topic
     * @param integer $recursiveDepth Number of levels to fetch
     * @return null|joppa\forum\model\data\ForumBoardData
     */
    public function getTopic($idTopic) {
        $query = $this->createQuery(0);
        $query->removeFields('{posts}, {firstPost}, {lastPost}');
        $query->addCondition('{id} = %1%', $idTopic);

        $topic = $query->queryFirst();
        if (!$topic) {
        	return null;
        }

        $boardModel = $this->getModel(ForumBoardModel::NAME);
        $categoryModel = $this->getModel(ForumCategoryModel::NAME);

        $topic->board = $boardModel->findById($topic->board, 0);
        $topic->board->category = $categoryModel->findById($topic->board->category, 0);

        return $topic;
    }

	/**
	 * Gets the topics of a board
	 * @param integer $idBoard Id of the board
	 * @param integer $page Number of the page to get
	 * @param integer $topicsPerPage Number of topics per page
	 * @return array Array with ForumTopicData objects
	 */
	public function getTopicsForBoard($idBoard, $page = 1, $topicsPerPage) {
		$offset = ($page - 1) * $topicsPerPage;

		$query = $this->createQuery();
		$query->removeFields('{posts}');
		$query->addCondition('{board} = %1%', $idBoard);
		$query->addOrderBy('{isSticky} DESC, {lastPost.dateAdded} DESC');
		$query->setLimit($topicsPerPage, $offset);

		$topics = $query->query();

        $profileModel = $this->getModel(ForumProfileModel::NAME);

		foreach ($topics as $topic) {
			if ($topic->firstPost->author) {
	            $topic->firstPost->author = $profileModel->findById($topic->firstPost->author);
			}
			if ($topic->lastPost->author) {
	            $topic->lastPost->author = $profileModel->findById($topic->lastPost->author);
			}
		}

		return $topics;
	}

	/**
	 * Gets the last topic of a board
	 * @param integer $idBoard Id of the board
	 * @return null|joppa\forum\model\data\ForumTopicData
	 */
	public function getLastTopicForBoard($idBoard) {
        $query = $this->createQuery(1);
        $query->setFields('{id}, {lastPost}');
        $query->addCondition('{board.id} = %1%', $idBoard);
        $query->addCondition('{board.lastTopic} = {id}', $idBoard);

        $topic = $query->queryFirst();

        if (!$topic) {
        	return;
        }

        if ($topic->lastPost->author) {
        	$profileModel = $this->getModel(ForumProfileModel::NAME);

        	$topic->lastPost->author = $profileModel->findById($topic->lastPost->author);
        }

        return $topic;
	}

	/**
	 * Gets the id of the last topic of a board
	 * @param integer $idBoard Id of the board
	 * @return null|integer Id of the last topic
	 */
    public function getLastTopicIdForBoard($idBoard) {
        $query = $this->createQuery(1);
        $query->setFields('{id}, {lastPost}');
        $query->addCondition('{board.id} = %1%', $idBoard);
        $query->addOrderBy('{lastPost.dateAdded} DESC');

        $topic = $query->queryFirst();

        if (!$topic) {
            return null;
        }

        return $topic->id;
    }

	/**
	 * Count the topics in a board
	 * @param integer $idBoard Id of the board
	 * @return integer Number of topics in the board
	 */
	public function countTopicsForBoard($idBoard) {
		$query = $this->createQuery();
		$query->addCondition('{board} = %1%', $idBoard);

		return $query->count();
	}

	/**
	 * Creates a new topic
	 * @param integer $idBoard Id of the board
	 * @param joppa\forum\model\data\ForumPostData $post Data of the first post
	 * @return joppa\forum\model\data\ForumTopicData Data of the new topic
	 */
	public function createTopic($idBoard, ForumPostData $post) {
		$boardModel = $this->getModel(ForumBoardModel::NAME);

		$query = $boardModel->createQuery(0);
		$query->setFields('{id}, {numTopics}, {numPosts}');
		$query->addCondition('{id} = %1%', $idBoard);

		$board = $query->queryFirst();
		if (!$board) {
			throw new Exception('The provided board does not exist');
		}

		$topic = $this->createData();
		$topic->board = $board->id;
		$topic->posts = array($post);

		$transactionStarted = $this->startTransaction();
		try {
			$this->save($topic);

			$saveTopic = $this->createData(false);
			$saveTopic->id = $topic->id;
			$saveTopic->firstPost = $post->id;
			$saveTopic->lastPost = $post->id;

			$this->save($saveTopic);

			$topic->firstPost = $post;
			$topic->lastPost = $post;

			$board->numTopics++;
			$board->numPosts++;
			$board->lastTopic = $topic->id;
			$boardModel->save($board);

			$this->commitTransaction($transactionStarted);
		} catch (Exception $exception) {
			$this->rollbackTransaction($transactionStarted);
			throw $exception;
		}

		return $topic;
	}

	/**
	 * Replies to a topic
	 * @param integer $idTopic Id of the topic to reply on
	 * @param joppa\forum\model\data\ForumPostData $post Data of the reply post
	 * @return joppa\forum\model\data\ForumTopicData The topic on which has been replied
	 */
    public function replyTopic($idTopic, ForumPostData $post) {
        $postModel = $this->getModel(ForumPostModel::NAME);
        $boardModel = $this->getModel(ForumBoardModel::NAME);

        $query = $this->createQuery(0);
        $query->setFields('{id}, {board}, {numPosts}');
        $query->addCondition('{id} = %1%', $idTopic);

        $topic = $query->queryFirst();
        if (!$topic) {
            throw new ZiboException('Could not find topic with id ' . $idTopic);
        }

        $query = $boardModel->createQuery(0);
        $query->setFields('{id}, {numPosts}');
        $query->addCondition('{id} = %1%', $topic->board);

        $board = $query->queryFirst();
        if (!$board) {
            throw new ZiboException('Could not find board with id ' . $topic->board);
        }

        $post->topic = $topic->id;

        $transactionStarted = $this->startTransaction();
        try {
            $postModel->save($post);

            $topic->numPosts++;
            $topic->lastPost = $post->id;
            $topic->views = array();

            $this->save($topic);

            $board->numPosts++;
            $board->lastTopic = $idTopic;

            $boardModel->save($board);

            $this->commitTransaction($transactionStarted);
        } catch (Exception $exception) {
            $this->rollbackTransaction($transactionStarted);
            throw $exception;
        }

        return $topic;
    }

    /**
     * Registers a view to the topic
     * @param integer $idTopic Id of the topic
     * @param integer $idProfile Id of the profile who is viewing
     * @return null
     */
    public function viewTopic($idTopic, $idProfile = null) {
    	$query = $this->createQuery(1);
    	$query->setFields('{id}, {numViews}');
    	$query->addCondition('{id} = %1%', $idTopic);

    	if ($idProfile) {
    		$query->addFields('{views}');
    	}

    	$topic = $query->queryFirst();
        if (!$topic) {
            throw new Exception('Could not find topic with id ' . $idTopic);
        }

        $topic->numViews++;

        if ($idProfile) {
            $topic->views = array();
            foreach ($topic->views as $profile) {
                $topic->views[$profile->id] = $profile->id;
            }

            $topic->views[$idProfile] = $idProfile;
        }

        $this->save($topic);
    }

	/**
	 * Move a topic to another board
	 * @param integer $idTopic Id of the topic to move
	 * @param integer $idNewBoard Id of the new board for the topic
	 * @return null
	 * @throws Exception when the topic or the board could not be found
	 */
	public function moveTopic($idTopic, $idNewBoard) {
		$boardModel = $this->getModel(ForumBoardModel::NAME);

		$query = $this->createQuery(0);
		$query->setFields('{id}, {board}');
		$query->addCondition('{id} = %1%', $idTopic);

		$topic = $query->queryFirst();
		if ($topic == null) {
			throw new Exception('Could not find the topic to move');
		}

		$query = $boardModel->createQuery(0);
		$query->setFields('{id}, {numTopics}, {numPosts}');
		$query->addCondition('{id} = %1%', $idNewBoard);

		$newBoard = $query->queryFirst();
		if ($newBoard == null) {
			throw new Exception('Could not find the board to move the topic to');
		}

		$query = $boardModel->createQuery(0);
		$query->setFields('{id}, {numTopics}, {numPosts}');
		$query->addCondition('{id} = %1%', $topic->board);
		$oldBoard = $query->queryFirst();

		$transactionStarted = $this->startTransaction();
		try {
			$topic->board = $newBoard->id;

			$this->save($topic, 'board');

			$newBoard->numPosts += $topic->numPosts;
			$newBoard->numTopics++;
			$newBoard->lastTopic = $this->getLastTopicIdForBoard($newBoard->id);

			$boardModel->save($newBoard);

			$oldBoard->numPosts -= $topic->numPosts;
			$oldBoard->numTopics--;
			$oldBoard->lastTopic = $this->getLastTopicIdForBoard($oldBoard->id);

			$boardModel->save($oldBoard);

			$this->commitTransaction($transactionStarted);
		} catch (Exception $e) {
			$this->rollbackTransaction($transactionStarted);
			throw $e;
		}
	}

    /**
     * Sticky or unsticky the provided topic
     * @param integer $idTopic Id of the topic
     * @return boolean New sticky state of the provided topic
     */
	public function sticky($idTopic) {
		$query = $this->createQuery(0);
		$query->setFields('{id}, {isSticky}');
		$query->addCondition('{id} = %1%', $idTopic);

		$topic = $query->queryFirst();
		if (!$topic) {
			throw new Exception('Could not find the provided topic');
		}

		$topic->isSticky = !$topic->isSticky;

		$this->save($topic, 'isSticky');

		return $topic->isSticky;
	}

    /**
     * Gets the number for a new post
     * @param integer|joppa\forum\model\data\ForumTopicData $topic Id of the topic or the topic itself
     * @return integer Number for a new post
     */
	public function getNewPostNumber($topic) {
		$idTopic = $this->getPrimaryKey($topic);

		$postModel = $this->getModel(ForumPostModel::NAME);

		$numPosts = $postModel->countPosts($idTopic);

		return $numPosts + 1;
	}

	/**
	 * Deletes a topic
	 * @param integer|joppa\forum\model\data\ForumTopicData $data Id of the topic or the topic
	 * @return joppa\forum\model\data\ForumTopicData The deleted topic
	 */
	protected function deleteData($data) {
		$data = parent::deleteData($data);

		$boardModel = $this->getModel(ForumBoardModel::NAME);

		$board = $boardModel->createData(false);
		$board->id = $data->board->id;
		$board->numTopics = $data->board->numTopics - 1;
		$board->lastTopic = $this->getLastTopicIdForBoard($data->board->id);

		$boardModel->save($board);

		return $data;
	}

}