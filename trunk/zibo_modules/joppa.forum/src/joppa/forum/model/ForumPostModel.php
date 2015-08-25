<?php

namespace joppa\forum\model;

use zibo\core\Zibo;

use zibo\library\emoticons\EmoticonParser;
use zibo\library\orm\model\ExtendedModel;

class ForumPostModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'ForumPost';

	/**
	 * Configuration key for the emoticons of the forum
	 * @var string
	 */
	const CONFIG_EMOTICONS = 'forum.emoticons';

	/**
	 * The emoticon parser for the posts
	 * @var zibo\library\emoticons\EmoticonParser
	 */
	private $emoticonParser;

    /**
     * Gets the last posts of the forum
     * @param integer $postsPerPage The number of posts per page
     * @param integer $page The current page number
     * @return array Array with ForumPostData objects
     */
	public function getLastPosts($postsPerPage = 7, $page = 1) {
		$query = $this->createQuery();
		$query->addOrderBy('{dateAdded} DESC');

		if ($page && $postsPerPage) {
            $offset = ($page - 1) * $postsPerPage;
            $query->setLimit($postsPerPage, $offset);
        }

		return $query->query();
	}

    /**
     * @deprecated
     */
	public function getLastPostsForProfile($idProfile, $number = 7) {
		$query = $this->createQuery();
		$query->addCondition('{author} = %1%', $idProfile);
		$query->addOrderBy('{dateAdded} DESC');
		$query->setLimit($number);
		$posts = $query->query();

		return $posts;
	}

    /**
     * Gets the posts for the provided topic
     * @param integer $idTopic Id of the topic
     * @param integer $page Number of the page
     * @param integer $postsPerPage Number of posts on a page
     * @return array
     */
	public function getPostsForTopic($idTopic, $page = 0, $postsPerPage = 0) {
		$query = $this->createQuery();
		$query->removeFields('{topic}');
		$query->addCondition('{topic} = %1%', $idTopic);
		$query->addOrderBy('{dateAdded} ASC');

		if ($page && $postsPerPage) {
			$offset = ($page - 1) * $postsPerPage;
			$query->setLimit($postsPerPage, $offset);
		}

		$posts = $query->query();

		if ($postsPerPage == 1) {
			return array_shift($posts);
		}

		return $posts;
	}

	/**
	 * Gets the total number of posts
	 * @param integer $idTopic If provided, this method will only count the posts of the topic
	 * @return integer Total number of posts
	 */
	public function countPosts($idTopic = 0) {
		$query = $this->createQuery();
		if ($idTopic) {
            $query->addCondition('{topic} = %1%', $idTopic);
		}

		return $query->count();
	}

	/**
	 * @deprecated
	 */
	public function getFirstPostForTopic($idTopic) {
		$query = $this->createQuery();
		$query->addCondition('{topic} = %1%', $idTopic);
		$query->addOrderBy('{dateAdded} ASC');
		return $query->queryFirst();
	}

	/**
	 * Gets the id of the last post in the provided topic
	 * @param integer $idTopic Id of the topic
	 * @return integer The id of the last topic, 0 if there are no topics
	 */
	public function getLastPostIdForTopic($idTopic) {
		$query = $this->createQuery(0);
		$query->addCondition('{topic} = %1%', $idTopic);
		$query->addOrderBy('{dateAdded} DESC');

		$post = $query->queryFirst();

		if ($post) {
			return $post->id;
		}

		return 0;
	}

	/**
	 * @deprecated
	 */
	public function getLastPostForBoard($idBoard) {
		$query = $this->createQuery();
		$query->addCondition('{topic.board} = %1%', $idBoard);
		$query->addOrderBy('{dateAdded} DESC');
		return $query->queryFirst();
	}

	/**
	 * Saves a post
	 * @param joppa\forum\model\data\ForumPostData $data Post to save
	 * @return null
	 */
	protected function saveData($data) {
		if (!$data->id) {
			$topicModel = $this->getModel(ForumTopicModel::NAME);

			$data->topicPostNumber = $topicModel->getNewPostNumber($data->topic);
		}

		if ($data->author && !is_numeric($data->author) && $data->author->id) {
			$data->author = $data->author->id;
		}

		parent::saveData($data);

		if ($data->author) {
            $profileModel = $this->getModel(ForumProfileModel::NAME);
            $profileModel->addPost($data->author);
		}
	}

	/**
	 * Deletes a post
	 * @param integer|joppa\forum\model\data\ForumPostData $data Id of the post to delete or the post itself
	 * @return joppa\forum\model\data\ForumPostData The deleted post
	 */
	protected function deleteData($data) {
		$data = parent::deleteData($data);

		// substract a post from the author's total posts
		if ($data->author) {
            $profileModel = $this->getModel(ForumProfileModel::NAME);
            $profileModel->removePost($data->author);
		}

		// update the number of posts and the last post of the topic
		$topicModel = $this->getModel(ForumTopicModel::NAME);

		$topic = $topicModel->createData(false);
		$topic->id = $data->topic->id;
		$topic->numPosts = $data->topic->numPosts - 1;
		$topic->lastPost = $this->getLastPostIdForTopic($topic->id);

		if (!$topic->lastPost) {
			$topicModel->delete($topic);

			return $data;
		}

		$topicModel->save($topic);

		// update the number of posts and the last post of the board
        $boardModel = $this->getModel(ForumBoardModel::NAME);

        $query = $boardModel->createQuery();
        $query->setFields('{id}, {numPosts}');
        $query->addCondition('{id} = %1%', $data->topic->board);

        $board = $query->queryFirst();

        $board->numPosts--;
        $board->lastTopic = $topicModel->getLastTopicIdForBoard($board->id);

        $boardModel->save($board);

        // resync the topics post numbers
        $query = $this->createQuery();
        $query->setFields('{id}, {topicPostNumber}');
        $query->addCondition('{topicPostNumber} > %1% and {topic} = %2%', $data->topicPostNumber, $data->topic->id);
        $query->addOrderBy('{topicPostNumber}');

        $topicPostNumber = $data->topicPostNumber;
        $posts = $query->query();
        foreach ($posts as $post) {
            $post->topicPostNumber = $topicPostNumber;
            $this->save($post, 'topicPostNumber');

            $topicPostNumber++;
        }

		return $data;
	}

	/**
	 * Gets the emoticon parser for the message of the posts
	 * @return zibo\library\emoticons\EmoticonParser
	 */
	public function getEmoticonParser() {
		if (!$this->emoticonParser) {
			$emoticons = $this->getEmoticons();
			$this->emoticonParser = new EmoticonParser($emoticons);
		}
		return $this->emoticonParser;
	}

	/**
	 * Gets the emoticons for the forum from the Zibo configuration
	 * @return null|array Null of no emoticons are defined for the forum. An array with the
	 *                    emoticon string as key and the URL to the image as value
	 */
	private function getEmoticons() {
		$configEmoticons = Zibo::getInstance()->getConfigValue(self::CONFIG_EMOTICONS);
		if (is_null($configEmoticons)) {
			return null;
		}

		$emoticons = array();

		foreach ($configEmoticons as $emoticon) {
			$emoticons[$emoticon['name']] = $emoticon['image'];
		}

		return $emoticons;
	}

}