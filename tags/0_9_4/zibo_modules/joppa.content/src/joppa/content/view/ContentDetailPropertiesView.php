<?php

namespace joppa\content\view;

use joppa\content\form\ContentDetailPropertiesForm;

/**
 * View for the properties of the content detail widget
 */
class ContentDetailPropertiesView extends AbstractContentPropertiesView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/content/detail.properties';

	/**
	 * Constructs a new properties view
	 * @param joppa\content\form\ContentDetailPropertiesForm $form
	 * @return null
	 */
	public function __construct(ContentDetailPropertiesForm $form, $fieldsAction) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);

        $this->addInlineJavascript('joppaContentInitializeDetailProperties("' . $fieldsAction . '");');
	}

}