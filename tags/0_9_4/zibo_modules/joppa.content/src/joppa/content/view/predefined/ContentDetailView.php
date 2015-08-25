<?php

namespace joppa\content\view\predefined;

use joppa\content\view\AbstractContentDetailView;

/**
 * View for a simple content list
 */
class ContentDetailView extends AbstractContentDetailView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/content/detail';

    /**
     * Constructs a new content view
     * @param array $result Array with Content objects
     * @param joppa\content\model\ContentProperties $properties Properties for the view
     * @return null
     */
    public function __construct() {
    	parent::__construct(self::TEMPLATE);
    }

}