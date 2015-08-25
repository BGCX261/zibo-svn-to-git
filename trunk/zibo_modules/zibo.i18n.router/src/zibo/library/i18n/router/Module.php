<?php

namespace zibo\library\i18n\router;

use zibo\core\Zibo;

/**
 * The module of the i18n router
 */
class Module {

    /**
     * Initializes the module
     * @return null
     */
    public function initialize() {
        Zibo::getInstance()->registerEventListener(Zibo::EVENT_PRE_ROUTE, array($this, 'preRoute'));
    }

    /**
     * Registers the i18n router in Zibo
     * @return
     */
    public function preRoute() {
        $zibo = Zibo::getInstance();

        $i18nRouter = new I18nRouter();

        $router = $zibo->getRouter();
        if ($router) {
            $defaultController = $router->getDefaultController();
            $defaultAction = $router->getDefaultAction();

            if ($defaultController) {
                $i18nRouter->setDefaultAction($defaultController, $defaultAction);
            }
        }

        $zibo->setRouter($i18nRouter);
    }

}