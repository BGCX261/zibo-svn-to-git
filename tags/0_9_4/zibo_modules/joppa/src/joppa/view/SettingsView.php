<?php

namespace joppa\view;

use joppa\form\SettingsForm;

use zibo\admin\view\BaseView;

/**
 * View for the Joppa settings
 */
class SettingsView extends BaseView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/settings';

    /**
     * Construct this view
     * @param joppa\form\SettingsForm $form
     */
    public function __construct(SettingsForm $form) {
        parent::__construct(self::TEMPLATE);

        $this->set('form', $form);
    }

}