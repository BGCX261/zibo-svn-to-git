<?php

namespace joppa\forum\controller;

use zibo\library\widget\controller\AbstractWidget;

use joppa\forum\form\ForumSearchForm;
use joppa\forum\model\ForumPostModel;
use joppa\forum\model\ForumProfileModel;
use joppa\forum\view\ForumPreviewWidgetPropertiesView;
use joppa\forum\view\ForumPreviewWidgetView;
use joppa\forum\Module;

use joppa\model\content\ContentFacade;
use joppa\model\NodeModel;

/**
 * Widget to perform a search in the forum
 */
class ForumSearchWidget extends AbstractWidget {

	/**
	 * Translation key for the name of this widget
	 * @var string
	 */
	const TRANSLATION_NAME = 'joppa.forum.title.widget.search';

	/**
	 * Hook with the ORM module. Defines the models to be loaded in to this widget
	 * @var array
	 */
	public $useModels = array(ForumPostModel::NAME, NodeModel::NAME);

	/**
	 * Constructs a new forum preview widget
	 * @return null
	 */
	public function __construct() {
		parent::__construct(self::TRANSLATION_NAME, Module::ICON);
	}

	/**
	 * Action to show the preview of the forum
	 * @return null
	 *
	 * @todo what about hidden boards?
	 */
	public function indexAction() {
		$nodesForum = $this->models[NodeModel::NAME]->getNodesForWidget('joppa', 'forum');
		if (!$nodesForum) {
			return;
		}

		$nodeForum = array_shift($nodesForum);

		$urlForum = $this->request->getBaseUrl() . '/' . $nodeForum->getRoute();

		$numPosts = $this->getNumberOfPosts();
		$posts = $this->models[ForumPostModel::NAME]->getLastPosts($numPosts);

		$contentFacade = ContentFacade::getInstance();
		foreach ($posts as $post) {
            $post->topic->firstPost = $this->models[ForumPostModel::NAME]->getPostsForTopic($post->topic->id, 1, 1);

			$post->url = $contentFacade->getUrl(ForumPostModel::NAME, $post);

			if ($post->author) {
				$post->author->url = $contentFacade->getUrl(ForumProfileModel::NAME, $post->author);
			}
		}

		$view = new ForumPreviewWidgetView($posts, $urlForum);
		$this->response->setView($view);
	}

	/**
	 * Gets the preview of the properties of this widget
	 * @return string
	 */
	public function getPropertiesPreview() {
        $numPosts = $this->getNumberOfPosts();

		$translator = $this->getTranslator();
		return $translator->translate(self::TRANSLATION_POSTS) . ': ' . $numPosts;
	}

    /**
     * Action to edit the properties of this widget
     * @return null
     */
    public function propertiesAction() {
        $numPosts = $this->getNumberOfPosts();

        $form = new ForumPreviewWidgetPropertiesForm($this->request->getBasePath(), $numPosts);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($this->request->getBaseUrl());
                return false;
            }

            try {
                $form->validate();

                $this->properties->setWidgetProperty(self::PROPERTY_POSTS, $form->getPosts());

                $this->response->setRedirect($this->request->getBaseUrl());

                return true;
            } catch (ValidationException $e) {
            }
        }

        $view = new ForumPreviewWidgetPropertiesView($form);
        $this->response->setView($view);

        return false;
    }

    /**
     * Gets the number of posts to display
     * @return integer
     */
    private function getNumberOfPosts() {
    	return $this->properties->getWidgetProperty(self::PROPERTY_POSTS, self::DEFAULT_POSTS);
    }

}