<?php

namespace joppa\content\view;

use joppa\content\form\ContentOverviewPropertiesForm;

use zibo\jquery\Module as JQuery;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the properties of a content widget
 */
abstract class AbstractContentPropertiesView extends SmartyView {

	/**
	 * Path to the javascript file for this view
	 * @var string
	 */
	const SCRIPT = 'web/scripts/joppa/widget/content.backend.js';

	/**
	 * Path to the css file for this view
	 * @var string
	 */
	const STYLE = 'web/styles/joppa/widget/content.css';

	/**
	 * Constructs a new properties view
	 * @param string $template Path to the template
	 * @return null
	 */
	public function __construct($template) {
		parent::__construct($template);

		$this->addStyle(self::STYLE);

		$this->addJavascript(JQuery::SCRIPT_JQUERY_UI);
		$this->addJavascript(self::SCRIPT);
	}

}