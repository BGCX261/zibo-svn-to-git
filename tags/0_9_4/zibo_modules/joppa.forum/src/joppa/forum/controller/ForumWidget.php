<?php

namespace joppa\forum\controller;

use joppa\controller\JoppaWidget;

use joppa\forum\form\ForumPostForm;
use joppa\forum\form\ForumWidgetPropertiesForm;

use joppa\forum\model\ForumBoardModel;
use joppa\forum\model\ForumCategoryModel;
use joppa\forum\model\ForumPostModel;
use joppa\forum\model\ForumProfileModel;
use joppa\forum\model\ForumTopicModel;
use joppa\forum\model\ForumRankingModel;
use joppa\forum\view\ForumBoardView;
use joppa\forum\view\ForumCategoryView;
use joppa\forum\view\ForumIndexView;
use joppa\forum\view\ForumPostFormView;
use joppa\forum\view\ForumTopicView;
use joppa\forum\view\ForumWidgetPropertiesView;
use joppa\forum\Module;

use joppa\model\content\ContentFacade;

use zibo\library\validation\exception\ValidationException;

use \Exception;

/**
 * Controller of the forum widget. This is the controller of the main forum frontend.
 */
class ForumWidget extends JoppaWidget {

	/**
	 * Action to view a category
	 * @var string
	 */
	const ACTION_CATEGORY = 'category';

	/**
	 * Action to view a board
	 * @var string
	 */
	const ACTION_BOARD = 'board';

	/**
	 * Action to view a topic
	 * @var string
	 */
	const ACTION_TOPIC = 'topic';

	/**
	 * Action to view a post
	 * @var string
	 */
	const ACTION_POST = 'post';

	/**
	 * Action to add a new topic
	 * @var string
	 */
	const ACTION_ADD = 'add';

	/**
	 * Action to reply to a topic
	 * @var string
	 */
	const ACTION_REPLY = 'reply';

	/**
	 * Action to delete a topic
	 * @var string
	 */
	const ACTION_DELETE = 'delete';

	/**
	 * Action to view the lasts posts of the forum
	 * @var string
	 */
	const ACTION_LAST = 'last';

    /**
     * Name of the topics per page field
     * @var string
     */
    const PROPERTY_TOPICS_PER_PAGE = 'page.topics';

    /**
     * Name of the posts per page field
     * @var string
     */
    const PROPERTY_POSTS_PER_PAGE = 'page.posts';

    /**
     * Default value for the topics per page property
     * @var integer
     */
    const DEFAULT_TOPICS_PER_PAGE = 15;

    /**
     * Default value for the posts per page property
     * @var integer
     */
    const DEFAULT_POSTS_PER_PAGE = 10;

	/**
	 * Translation key for the name of this widget
	 * @var string
	 */
	const TRANSLATION_NAME = 'joppa.forum.title.widget';

	/**
	 * Translation key for the not allowed to view error message
	 * @var string
	 */
	const TRANSLATION_ERROR_ALLOW_VIEW = 'joppa.forum.error.allow.view';

	/**
	 * Translation key for the add topic title
	 * @var string
	 */
	const TRANSLATION_TOPIC_ADD = 'joppa.forum.button.topic.add';

	/**
	 * Translation key for the reply topic title
	 * @var string
	 */
	const TRANSLATION_TOPIC_REPLY = 'joppa.forum.button.topic.reply';

	const TRANSLATION_BOARD_NOT_FOUND = 'joppa.forum.error.board.not.found';
	const TRANSLATION_TOPIC_NOT_FOUND = 'joppa.forum.error.topic.not.found';
	const TRANSLATION_LAST_POSTS = 'joppa.forum.title.last.posts';

	/**
	 * Hook with the ORM module
	 * @var array
	 */
	public $useModels = array(
        ForumBoardModel::NAME,
        ForumCategoryModel::NAME,
        ForumPostModel::NAME,
        ForumProfileModel::NAME,
        ForumTopicModel::NAME,
        ForumRankingModel::NAME,
    );

    /**
     * Constructs a new forum widget
     * @return null
     */
	public function __construct() {
		parent::__construct(self::TRANSLATION_NAME, Module::ICON);
	}

    /**
     * Gets the names of the possible request parameters of this widget
     * @return array
     */
    public function getRequestParameters() {
        return array(
            self::ACTION_CATEGORY,
            self::ACTION_BOARD,
            self::ACTION_TOPIC,
            self::ACTION_ADD,
            self::ACTION_REPLY,
            self::ACTION_LAST,
        );
    }

	/**
	 * Action to get the index of the forum
	 * @return null
	 */
	public function indexAction() {
		$basePath = $this->request->getBasePath() . '/';
		$categoryAction = $basePath . self::ACTION_CATEGORY . '/';
		$boardAction = $basePath . self::ACTION_BOARD . '/';
		$topicAction = $basePath . self::ACTION_TOPIC . '/';

		$contentFacade = ContentFacade::getInstance();

		$categories = $this->models[ForumCategoryModel::NAME]->getCategories();
		foreach ($categories as $category) {
			foreach ($category->boards as $board) {
				foreach ($board->moderators as $moderator) {
					$moderator->url = $contentFacade->getUrl(ForumProfileModel::NAME, $moderator);
				}
			}
		}

		$view = new ForumIndexView($categories, $categoryAction, $boardAction, $topicAction);
		$this->response->setView($view);
	}

	/**
	 * Action to get display the boards of a category
	 * @param integer $id Id of the category
	 * @return null
	 */
	public function categoryAction($id) {
		$category = $this->models[ForumCategoryModel::NAME]->findById($id, 0);
		if (!$category) {
			$this->setError404();
			return;
		}

		$basePath = $this->request->getBasePath() . '/';
		$categoryAction = $basePath . self::ACTION_CATEGORY. '/';
		$boardAction = $basePath . self::ACTION_BOARD . '/';
		$topicAction = $basePath . self::ACTION_TOPIC . '/';

		$contentFacade = ContentFacade::getInstance();

		$category->boards = $this->models[ForumBoardModel::NAME]->getBoardsForCategory($category->id);
		foreach ($category->boards as $board) {
			foreach ($board->moderators as $moderator) {
				$moderator->url = $contentFacade->getUrl(ForumProfileModel::NAME, $moderator);
			}
		}

		$this->addBreadcrumb($categoryAction . $category->id, $category->name);

		$view = new ForumCategoryView($category, $boardAction, $topicAction);
		$this->response->setView($view);
	}

	/**
	 * Action to view a board
	 * @param integer $idBoard Id of the board
	 * @param integer $page Number of page
	 * @return null
	 *
	 * @todo make routers for all urls
	 */
	public function boardAction($idBoard, $page = null) {
		$board = $this->models[ForumBoardModel::NAME]->getBoard($idBoard, 1);
		if (!$board) {
			$this->setError404();
			return;
		}

		$profile = $this->getProfile();

		if (!$board->isViewAllowed($profile)) {
			$this->addError(self::TRANSLATION_ERROR_ALLOW_VIEW, array('object' => $board->name));
			return;
		}

		$basePath = $this->request->getBasePath() . '/';
		$categoryAction = $basePath . self::ACTION_CATEGORY. '/';
		$boardAction = $basePath . self::ACTION_BOARD . '/';
		$topicAction = $basePath . self::ACTION_TOPIC . '/';

		$topicsPerPage = $this->getTopicsPerPage();
		$postsPerPage = $this->getPostsPerPage();
        $pages = ceil($board->numTopics / $topicsPerPage);
		$page = $this->getPage($page, $pages);

		$pageAction = $boardAction . $board->id . '/%page%';
		$topicAddAction = null;
		$topicStickyAction = null;
		$topicDeleteAction = null;

		if ($board->isNewTopicAllowed($profile)) {
			$topicAddAction = $basePath . self::ACTION_ADD . '/' . $board->id;
		}
//		$profile = $this->models['Profile']->getProfileForUser(false);
//		if ($profile) {
//			if ($this->isModerator($board, $profile)) {
//				$topicAddAction = $basePath . '/' . self::ACTION_ADD . '/' . $id;
//				$topicStickyAction = $basePath . '/sticky/';
//				$topicDeleteAction = $basePath . '/deleteTopic/';
//			} elseif ($board->allowNewTopics) {
//				$topicAddAction = $basePath . '/' . self::ACTION_ADD . '/' . $id;
//			}
//		}


		$topics = $this->models[ForumTopicModel::NAME]->getTopicsForBoard($board->id, $page, $topicsPerPage);
		foreach ($topics as $topic) {
			$topic->pages = ceil($topic->numPosts / $postsPerPage);
		}

        $this->addBreadcrumb($categoryAction . $board->category->id, $board->category->name);
        $this->addBreadcrumb($boardAction . $board->id, $board->name);

		$view = new ForumBoardView($pages, $page, $pageAction, $topics, $topicAction, $topicAddAction, $topicStickyAction, $topicDeleteAction);
		$this->response->setView($view);
	}

	public function topicAction($id, $page = null) {
		$topic = $this->models[ForumTopicModel::NAME]->getTopic($id);
		if (!$topic) {
            $this->setError404();
			return;
		}

		$idProfile = null;

		$profile = $this->getProfile();
		if ($profile) {
			$idProfile = $profile->id;
		}

        if (!$topic->board->isViewAllowed($profile)) {
            $this->addError(self::TRANSLATION_ERROR_ALLOW_VIEW, array('object' => $topic->board->name));
            return;
        }

        $this->models[ForumTopicModel::NAME]->viewTopic($topic->id, $idProfile);

		$postsPerPage = $this->getPostsPerPage();
		$page = $this->getPage($page);
		$pages = ceil($topic->numPosts / $postsPerPage);

		$topic->firstPost = $this->models[ForumPostModel::NAME]->getPostsForTopic($id, 1, 1);
		$topic->posts = $this->models[ForumPostModel::NAME]->getPostsForTopic($topic->id, $page, $postsPerPage);

		$rankings = $this->models[ForumRankingModel::NAME]->getRankings();
		foreach ($topic->posts as $post) {
			if ($post->author) {
				$post->author->setRanking($rankings);
			}
		}

		$basePath = $this->request->getBasePath() . '/';
		$topicAction = $basePath . self::ACTION_TOPIC . '/' . $topic->id;

		$this->addBreadcrumb($basePath . self::ACTION_CATEGORY . '/' . $topic->board->category->id, $topic->board->category->name);
		$this->addBreadcrumb($basePath . self::ACTION_BOARD . '/' . $topic->board->id, $topic->board->name);
		$this->addBreadcrumb($topicAction, $topic->firstPost->subject);

		$pageAction = $topicAction . '/%page%';
		$postAddAction = null;
		$postEditAction = null;
		$isModerator = false;

		if ($topic->board->isNewPostAllowed($profile)) {
			$postAddAction = $this->request->getBasePath() . '/' . self::ACTION_REPLY . '/' . $id;
			$postEditAction = $this->request->getBasePath() . '/edit/';
		}

		if ($profile) {
			$isModerator = $this->models[ForumBoardModel::NAME]->isProfileModerator($profile->id, $topic->board->id);
		}

		$emoticonParser = $this->models[ForumPostModel::NAME]->getEmoticonParser();

		$view = new ForumTopicView($pages, $page, $pageAction, $topic->posts, $emoticonParser, $postAddAction, $postEditAction, $profile, $isModerator);
		$this->response->setView($view);
	}

	/**
	 * Action to view the last posts of the forum
	 * @param integer $page Number of the page
	 * @return null
	 */
    public function lastAction($page = 1) {
    	$postsPerPage = $this->getPostsPerPage();
    	$page = $this->getPage($page);
    	$pages = ceil($this->models[ForumPostModel::NAME]->countPosts() / $postsPerPage);

        $posts = $this->models[ForumPostModel::NAME]->getLastPosts($postsPerPage, $page);

        $contentFacade = ContentFacade::getInstance();

        $rankings = $this->models[ForumRankingModel::NAME]->getRankings();

        foreach ($posts as $post) {
            $post->url = $contentFacade->getUrl(ForumPostModel::NAME, $post);

        	if ($post->author) {
                $post->author->setRanking($rankings);
                $post->author->url = $contentFacade->getUrl(ForumProfileModel::NAME, $post->author);
        	}
        }

        $emoticonParser = $this->models[ForumPostModel::NAME]->getEmoticonParser();

        $translator = $this->getTranslator();
        $title = $translator->translate(self::TRANSLATION_LAST_POSTS);

        $lastAction = $this->request->getBasePath() . '/' . self::ACTION_LAST;
        $pageAction = $lastAction . '/%page%';

        $this->addBreadcrumb($lastAction, $title);

        $view = new ForumTopicView($pages, $page, $pageAction, $posts, $emoticonParser);
        $view->setTitle($title);

        $this->response->setView($view);
    }

    /**
     * Action to add a new topic to the provided board
     * @param integer $idBoard Id of the board
     * @return null
     */
	public function addAction($idBoard) {
        $board = $this->models[ForumBoardModel::NAME]->getBoard($idBoard, 1);
        if (!$board) {
            $this->setError404();
            return;
        }

        $profile = $this->getProfile();

        if (!$board->isNewTopicAllowed($profile)) {
            $this->setError404();
            return;
        }

		$emoticonParser = $this->models[ForumPostModel::NAME]->getEmoticonParser();

		$post = $this->models[ForumPostModel::NAME]->createData(false);

		$form = new ForumPostForm($this->request->getBasePath() . '/' . self::ACTION_ADD . '/' . $idBoard, $post, $emoticonParser);
		$preview = null;
		if ($form->isSubmitted()) {
			if ($form->isCancelled()) {
				$this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_BOARD . '/' . $board->id);
				return;
			}

			try {
				$post = $form->getPost();

				if ($form->isPreview()) {
					$preview = $post;
				} else {
					$post->author = $profile;

					$topic = $this->models[ForumTopicModel::NAME]->createTopic($idBoard, $post);

					$this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_TOPIC . '/' . $topic->id);
					return;
				}
			} catch (ValidationException $e) {
				$form->setValidationException($e);
			}
		}

		$translator = $this->getTranslator();

		$basePath = $this->request->getBasePath() . '/';

		$this->addBreadcrumb($basePath . self::ACTION_CATEGORY . '/' . $board->category->id, $board->category->name);
		$this->addBreadcrumb($basePath . self::ACTION_BOARD . '/' . $board->id, $board->name);
		$this->addBreadcrumb($basePath . self::ACTION_ADD . '/' . $board->id, $translator->translate(self::TRANSLATION_TOPIC_ADD));

		$view = new ForumPostFormView($form, self::TRANSLATION_TOPIC_ADD, $preview);
		$this->response->setView($view);
	}

	public function editAction($postId) {
		$profile = $this->models['Profile']->getProfileForUser(false);
		$post = $this->models['ForumPost']->findById($postId);
		$topic = $post->topic;
		$isModerator = $this->models['ForumBoard']->isProfileModerator($profile->id, $topic->board);

		if (!$profile || !$post || ($post->author->id != $profile->id && !$isModerator)) {
			$this->response->setRedirect($this->request->getBasePath());
			return;
		}

		$emoticonParser = $this->models['ForumPost']->getEmoticonParser();
		$form = new ForumPostForm($this->request->getBasePath() . '/edit/' . $post->id, $post, $emoticonParser);
		$preview = null;
		if ($form->isSubmitted()) {
			if ($form->getValue(ForumPostForm::FIELD_CANCEL)) {
				$this->response->setRedirect($this->request->getBasePath() . '/topic/' . $topic->id . '#post' . $post->id);
				return;
			}

			try {
				$post = $form->getPost();
				if ($form->isPreview()) {
					$preview = $post;
				} else {
					$post->authorModified = $profile->id;

					$this->models[ForumPostModel::NAME]->save($post);

					$post = $this->models[ForumPostModel::NAME]->findById($post->id);

					$url = ObjectManager::getInstance()->getUrl(ForumPostModel::NAME, $post);
					$this->response->setRedirect($this->request->getBaseUrl() . '/' . $url);

					return;
				}
			} catch (ValidationException $e) {
				$form->setValidationException($e);
			}
		}

		$emoticonParser = $this->models['ForumPost']->getEmoticonParser();
		$view = new ForumPostFormView($form, 'joppa.forum.button.post.add', $preview, $emoticonParser);
		$this->response->setView($view);
	}

	public function replyAction($idTopic, $idQuotePost = null) {
        $topic = $this->models[ForumTopicModel::NAME]->getTopic($idTopic);
        if (!$topic) {
            $this->setError404();
            return;
        }

        $idProfile = null;

        $profile = $this->getProfile();
        if ($profile) {
            $idProfile = $profile->id;
        }

        if (!$topic->board->isNewPostAllowed($profile)) {
            $this->addError(self::TRANSLATION_ERROR_ALLOW_VIEW, array('object' => $topic->board->name));
            return;
        }

		$contentFacade = ContentFacade::getInstance();

		$post = $this->models[ForumPostModel::NAME]->createData(false);

		$topicPost = $this->models[ForumPostModel::NAME]->getFirstPostForTopic($topic->id);
		$post->subject = 'RE: ' . $topicPost->subject;

		if ($idQuotePost) {
			$quotePost = $this->models[ForumPostModel::NAME]->findById($idQuotePost, 0);
			if ($quotePost) {
                $quotePost->topic = $topic;
				$quoteUrl = $contentFacade->getUrl(ForumPostModel::NAME, $quotePost);
				$post->message = '[quote=' . $quoteUrl . ']' . $quotePost->message . '[/quote]';
			}
		}

		$emoticonParser = $this->models[ForumPostModel::NAME]->getEmoticonParser();

		$form = new ForumPostForm($this->request->getBasePath() . '/' . self::ACTION_REPLY . '/' . $topic->id, $post, $emoticonParser);
		$preview = null;
		if ($form->isSubmitted()) {
			if ($form->isCancelled()) {
				$this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_TOPIC . '/' . $topic->id);
				return;
			}

			try {
				$post = $form->getPost();

				if ($form->isSubmitted()) {
					$post->author = $idProfile;

					$topic = $this->models[ForumTopicModel::NAME]->replyTopic($topic->id, $post);

					$post->topic = $topic;

					$this->response->setRedirect($contentFacade->getUrl(ForumPostModel::NAME, $post));
					return;
				} elseif ($form->getValue(ForumPostForm::FIELD_PREVIEW)) {
					$preview = $post;
				}
			} catch (ValidationException $e) {
				$form->setValidationException($e);
			}
		}

		$translator = $this->getTranslator();

		$board = $this->models[ForumBoardModel::NAME]->findById($topicPost->topic->board, 0);
		$category = $this->models[ForumCategoryModel::NAME]->findById($board->category, 0);

		$this->addBreadcrumb($this->request->getBasePath(), $category->name);
		$this->addBreadcrumb($this->request->getBasePath() . '/' . self::ACTION_BOARD .'/' . $board->id, $board->name);
		$this->addBreadcrumb($this->request->getBasePath() . '/' . self::ACTION_TOPIC . '/' . $topicPost->topic->id, $topicPost->subject);
		$this->addBreadcrumb($this->request->getBasePath() . '/' . self::ACTION_REPLY . '/' . $topicPost->topic->id, $translator->translate(self::TRANSLATION_TOPIC_REPLY));

		$view = new ForumPostFormView($form, self::TRANSLATION_TOPIC_REPLY, $preview, $emoticonParser);
		$this->response->setView($view);
	}

    public function stickyAction($idTopic) {
        $topic = $this->models['ForumTopic']->findById($idTopic);
        if (!$topic || !$this->isModerator($topic->board)) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $this->models['ForumTopic']->sticky($topic);

        $this->response->setRedirect($this->request->getBasePath() . '/board/' . $topic->board->id);
    }

    /**
     * Action to delete an item from the forum
     * @param string $type Type of the item to delete (topic or post)
     * @param integer $id Id of the item
     * @return null
     */
    public function deleteAction($type, $id) {
    	$profile = $this->getProfile();

    	switch ($type) {
    		case 'topic':
    			$this->deleteTopic($id, $profile);
    			break;
    		case 'post':
    			$this->deletePost($id, $profile);
    			break;
    	}

    	$this->setError404();
    }

    /**
     * Deletes a topic from the forum
     * @param integer $id Id of the topic
     * @param joppa\forum\model\data\ForumProfileData $profile The profile of the current user
     * @return null
     */
    private function deleteTopic($id, $profile = null) {
        $topic = $this->models[ForumTopicModel::NAME]->findById($id);

        if (!$topic) {
            $this->setError404();
            return;
        }

        if (!$profile || !$this->models[ForumBoardModel::NAME]->isProfileModerator($profile->id, $topic->board->id)) {
            return;
        }

        $this->models[ForumTopicModel::NAME]->delete($topic);

        $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_BOARD . '/' . $topic->board->id);
    }

    /**
     * Deletes a post from the forum
     * @param integer $id Id of the post
     * @param joppa\forum\model\data\ForumProfileData $profile The profile of the current user
     * @return null
     */
    private function deletePost($id, $profile = null) {
        $post = $this->models[ForumPostModel::NAME]->findById($id);

        if (!$post) {
            $this->setError404();
            return;
        }

        if (!$profile || $this->models[ForumBoardModel::NAME]->isProfileModerator($profile->id, $post->topic->board)) {
            return;
        }

        $this->models[ForumTopicModel::NAME]->delete($topic);

        $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_BOARD . '/' . $topic->board->id);
    }

    /**
     * Action to edit the properties of this widget
     * @return null
     */
    public function propertiesAction() {
    	$topicsPerPage = $this->getTopicsPerPage();
    	$postsPerPage = $this->getPostsPerPage();

    	$form = new ForumWidgetPropertiesForm($this->request->getBasePath(), $topicsPerPage, $postsPerPage);
    	if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
            	$form->validate();

                $this->setTopicsPerPage($form->getTopicsPerPage());
                $this->setPostsPerPage($form->getPostsPerPage());

                $this->response->setRedirect($this->request->getBaseUrl());

                return true;
            } catch (ValidationException $e) {
            }
    	}

    	$view = new ForumWidgetPropertiesView($form);
    	$this->response->setView($view);

    	return false;
    }

    /**
     * Gets a valid page number
     * @param mixed $page Value claiming to be a page number
     * @param integer $maxPage Maximum number of pages
     * @return integer A valid page number
     */
	private function getPage($page, $maxPage = null) {
		if (!($page && is_numeric($page) && $page > 0)) {
			$page = 1;
		}

		if ($maxPage && $page > $maxPage) {
			$page = 1;
		}

		return $page;
	}

    /**
     * Gets the profile of the current user
     * @return joppa\forum\model\data\ForumProfileData|null
     */
    private function getProfile() {
    	$user = $this->getUser();
    	if (!$user) {
    		return null;
    	}

    	return $this->models[ForumProfileModel::NAME]->getForumProfileForUser($user->id);
    }

    /**
     * Gets the number of topics per page
     * @return integer
     */
    public function getTopicsPerPage() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_TOPICS_PER_PAGE, self::DEFAULT_TOPICS_PER_PAGE);
    }

    /**
     * Sets the number of topics per page
     * @param integer $topicsPerPage
     * @return null
     */
    private function setTopicsPerPage($topicsPerPage) {
    	$this->properties->setWidgetProperty(self::PROPERTY_TOPICS_PER_PAGE, $topicsPerPage);
    }

    /**
     * Gets the number of posts per page
     * @return integer
     */
    public function getPostsPerPage() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_POSTS_PER_PAGE, self::DEFAULT_POSTS_PER_PAGE);
    }

    /**
     * Sets the number of posts per page
     * @param integer $postsPerPage
     * @return null
     */
    private function setPostsPerPage($postsPerPage) {
        $this->properties->setWidgetProperty(self::PROPERTY_POSTS_PER_PAGE, $postsPerPage);
    }

}