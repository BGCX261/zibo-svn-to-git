<?php

namespace zibo\admin\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\form\ProfileForm;
use zibo\admin\model\profile\AccountProfileHook;
use zibo\admin\view\security\ProfileView;

use zibo\core\Zibo;

use zibo\library\security\exception\UnauthorizedException;
use zibo\library\security\SecurityManager;
use zibo\library\validation\exception\ValidationException;

/**
 * Controller to view and change the profile of the current user
 */
class ProfileController extends AbstractController {

    /**
     * Event to prepare the profile form
     * @var string
     */
    const EVENT_PREPARE_FORM = 'profile.form.prepare';

    /**
     * Translation key of the title
     * @var string
     */
    const TRANSLATION_TITLE = 'security.title.profile';

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

        Zibo::getInstance()->runEvent(self::EVENT_PREPARE_FORM, $form);

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

        $translator = $this->getTranslator();

        $view = new ProfileView($form);
        $view->setPageTitle($translator->translate(self::TRANSLATION_TITLE));

        $this->response->setView($view);
    }

}