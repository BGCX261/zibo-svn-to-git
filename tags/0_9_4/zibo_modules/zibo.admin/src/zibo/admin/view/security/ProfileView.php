<?php

namespace zibo\admin\view\security;

use zibo\admin\form\ProfileForm;
use zibo\admin\view\BaseView;

/**
 * View to show an the profile form
 */
class ProfileView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'admin/security/profile';

    /**
     * Constructs a new security view
     * @param zibo\admin\table\SecurityTable $table
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