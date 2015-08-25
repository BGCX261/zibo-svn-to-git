<?php

namespace joppa\content\view;

use joppa\content\form\ContentOverviewPropertiesForm;

/**
 * View for the properties of the content overview widget
 */
class ContentOverviewPropertiesView extends AbstractContentPropertiesView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/content/overview.properties';

	/**
	 * Constructs a new properties view
	 * @param joppa\content\form\ContentOverviewPropertiesForm $form
	 * @return null
	 */
	public function __construct(ContentOverviewPropertiesForm $form, $fieldsAction, $orderFieldsAction) {
		parent::__construct(self::TEMPLATE);

		$this->set('form', $form);

		$this->addInlineJavascript('joppaContentInitializeOverviewProperties("' . $fieldsAction . '", "' . $orderFieldsAction . '");');
	}

}