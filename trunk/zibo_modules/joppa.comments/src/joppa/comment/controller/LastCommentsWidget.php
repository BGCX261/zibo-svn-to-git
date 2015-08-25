<?php

namespace joppa\comment\controller;

use joppa\model\content\ContentFacade;

use joppa\comment\model\CommentModel;
use joppa\comment\view\LastCommentsView;

use zibo\library\widget\controller\AbstractWidget;

/**
 * Widget to show the last comments
 */
class LastCommentsWidget extends AbstractWidget {

    /**
     * Path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/comments.last.png';

    /**
	 * Translation key for the name of this widget
	 * @var string
	 */
    const TRANSLATION_NAME = 'joppa.comment.widget.name.last';

    /**
     * Hook with the ORM module
     * @var string
     */
    public $useModels = CommentModel::NAME;

    /**
     * Constructs a new last comments widget
     * @return null
     */
    public function __construct() {
    	parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Action to show the last comments
     * @return null
     */
    public function indexAction() {
    	$contentFacade = ContentFacade::getInstance();

        $comments = $this->models[CommentModel::NAME]->getLatestComments(5);
        foreach ($comments as $comment) {
        	$content = $contentFacade->getContent($comment->objectType, $comment->objectId);
            $comment->title = $content->title;
            $comment->url = $content->url . '#comment' . $comment->id;
        }

        $view = new LastCommentsView($comments);
        $this->response->setView($view);
    }

}