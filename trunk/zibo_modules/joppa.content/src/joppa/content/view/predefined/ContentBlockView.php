<?php

namespace joppa\content\view\predefined;

use joppa\content\view\AbstractContentOverviewView;

/**
 * View for a simple content list
 */
class ContentBlockView extends AbstractContentOverviewView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/content/block';

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