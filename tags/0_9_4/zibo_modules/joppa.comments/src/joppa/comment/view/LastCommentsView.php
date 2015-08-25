<?php

namespace joppa\widget\view;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the last comments
 */
class LastCommentsView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/comment/last';

	/**
	 * Constructs a new view for the last comments
	 * @param array $comments
	 * @return null
	 */
    public function __construct(array $comments) {
        parent::__construct(self::TEMPLATE);

        $this->set('comments', $comments);
    }

}