<?php

namespace zibo\admin\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\table\SecurityTable;
use zibo\admin\view\security\SecurityView;

use zibo\core\Zibo;

use zibo\library\security\SecurityManager;

use \Exception;

/**
 * Controller to view and change the security settings
 */
class SecurityController extends AbstractController {

    /**
     * Translation key of the title
     * @var string
     */
    const TRANSLATION_TITLE = 'security.title';

    /**
     * Translation key for the error message when saving permissions of a role
     * @var string
     */
    const TRANSLATION_ERROR_PERMISSIONS_ROLE = 'security.error.role.permissions.save';

    /**
     * Translation key for the error message when saving the allowed routes of a role
     * @var string
     */
    const TRANSLATION_ERROR_ROUTES_ROLE = 'security.error.routes.allowed.save';

    /**
     * Translation key for the error message when saving the denied routes
     * @var string
     */
    const TRANSLATION_ERROR_ROUTES_DENIED = 'security.error.routes.denied.save';

    /**
     * Translation key for the information message when the security model is successfully saved
     * @var string
     */
    const TRANSLATION_SAVED = 'security.message.model.saved';

    /**
     * Action to view and change the security settings
     * @return null
     */
    public function indexAction() {
        $table = new SecurityTable($this->request->getBasePath());

        $form = $table->getForm();
        if ($form->isSubmitted() && $form->getValue(SecurityTable::FIELD_SAVE)) {
            $this->processForm($table);

            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $translator = $this->getTranslator();

        $view = new SecurityView($table);
        $view->setPageTitle($translator->translate(self::TRANSLATION_TITLE));

        $this->response->setView($view);
    }

    /**
     * Saves the submitted security table to the security model
     * @param zibo\admin\table\SecurityTable $table
     * @return null
     */
    private function processForm(SecurityTable $table) {
        $zibo = Zibo::getInstance();
        $securityManager = SecurityManager::getInstance();
        $errorsOccured = false;

        $roles = $table->getRoles();
        foreach ($roles as $role) {
            try {
                $permissions = $table->getPermissions($role->getRoleName());
                $securityManager->setAllowedPermissionsToRole($role, $permissions);
            } catch (Exception $exception) {
                $errorsOccured = true;
                $zibo->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
                $this->addError(self::TRANSLATION_ERROR_PERMISSIONS_ROLE, array('role' => $role->getRoleName()));
            }

            try {
                $routes = $table->getAllowedRoutes($role->getRoleName());
                $securityManager->setAllowedRoutesToRole($role, $routes);
            } catch (Exception $exception) {
                $errorsOccured = true;
                $zibo->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
                $this->addError(self::TRANSLATION_ERROR_ROUTES_ROLE, array('role' => $role->getRoleName()));
            }
        }

        try {
            $routes = $table->getDeniedRoutes();
            $securityManager->setDeniedRoutes($routes);
        } catch (Exception $exception) {
            $errorsOccured = true;
            $zibo->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
            $this->addError(self::TRANSLATION_ERROR_ROUTES_DENIED);
        }

        if (!$errorsOccured) {
            $this->addInformation(self::TRANSLATION_SAVED);
        }
    }

}