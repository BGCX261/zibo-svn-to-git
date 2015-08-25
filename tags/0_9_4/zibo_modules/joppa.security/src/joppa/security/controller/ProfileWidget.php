<?php

namespace joppa\security\controller;

use joppa\security\view\ProfileWidgetView;

use zibo\admin\controller\ProfileController;
use zibo\admin\form\ProfileForm;
use zibo\admin\model\AccountProfileHook;

use zibo\core\Zibo;

use zibo\library\security\exception\UnauthorizedException;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;
use zibo\library\widget\controller\AbstractWidget;

/**
 * Widget to view and change the profile of the current user
 */
class ProfileWidget extends AbstractWidget {

    /**
     * Path to the icon of this widget
     * @var string
     */
    const ICON = 'web/images/joppa/widget/profile.png';

    /**
     * Translation key for the name of this widget
     * @var string
     */
    const TRANSLATION_NAME = 'joppa.security.widget.profile.name';

    /**
     * Constructs a new password reset widget
     */
    public function __construct() {
        parent::__construct(self::TRANSLATION_NAME, self::ICON);
    }

    /**
     * Action to view and change the profile
     * @return null
     */
    public function indexAction() {
        $user = SecurityManager::getInstance()->getUser();
        if (!$user) {
            throw new UnauthorizedException();
        }

        $form = new ProfileForm($this->request->getBasePath(), $user);
        $form->addHook(new AccountProfileHook());

        Zibo::getInstance()->runEvent(ProfileController::EVENT_PREPARE_FORM, $form);

        if ($form->isSubmitted()) {
            try {
                $form->validate();
                $form->processSubmit($this);

                if (!$this->response->getView() && !$this->response->willRedirect()) {
                    $this->response->setRedirect($this->request->getBasePath());
                }
                return;
            } catch (ValidationException $exception) {
                $form->setValidationException($exception);
            }
        }

        $view = new ProfileWidgetView($form);

        $this->response->setView($view);
    }

}