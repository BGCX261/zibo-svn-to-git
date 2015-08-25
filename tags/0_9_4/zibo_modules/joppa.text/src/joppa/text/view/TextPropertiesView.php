<?php

namespace joppa\text\view;

use joppa\text\form\TextPropertiesForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View for the properties of the text widget
 */
class TextPropertiesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/text/properties';

	/**
	 * Path to the style of this view
	 * @var string
	 */
	const STYLE = 'web/styles/joppa/text.css';

	/**
	 * Constructs a new text properties view
	 * @param joppa\text\form\TextPropertiesForm $form The form to edit the text
	 * @param array $history Array with LogData containing the history of the text
	 * @param string $historyUrl Action to view a text from the history
	 * @param string $currentVersion The current version of the text
	 * @return null
	 */
	public function __construct(TextPropertiesForm $form, array $history, $historyUrl, $currentVersion) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);
		$this->set('history', $history);
		$this->set('historyUrl', $historyUrl);
		$this->set('currentVersion', $currentVersion);

		$this->addStyle(self::STYLE);

		$this->addInlineJavascript('$("#textHistoryMore").click(function() { $("ul.historyMore").slideToggle("fast"); return false; });');
	}

}