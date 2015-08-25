<?php

namespace joppa\security\view;

use zibo\admin\form\ProfileForm;

use zibo\library\smarty\view\SmartyView;

/**
 * View to show the password reset widget
 */
class ProfileWidgetView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/security/profile/index';

	/**
	 * Constructs a new password reset view
	 * @param zibo\admin\form\ProfileForm $form Form of the view
	 * @return null
	 */
	public function __construct(ProfileForm $form) {
		parent::__construct(self::TEMPLATE);

		$subviewNames = array();

        $subviews = $form->getHookViews();
        foreach ($subviews as $index => $subview) {
            $subviewName = 'profileHook' . $index;
            $subviewNames[] = $subviewName;

            $this->setSubview($subviewName, $subview);
        }

        $this->set('form', $form);
        $this->set('subviewNames', $subviewNames);
	}

}