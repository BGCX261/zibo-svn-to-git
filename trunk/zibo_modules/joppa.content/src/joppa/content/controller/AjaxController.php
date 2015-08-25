<?php

namespace joppa\content\controller;

use joppa\content\form\ContentOverviewPropertiesForm;

use zibo\core\controller\AbstractController;
use zibo\core\view\JsonView;
use zibo\core\Zibo;

use zibo\library\orm\ModelManager;

use \Exception;

/**
 * Helper of a content widget
 */
class AjaxController extends AbstractController {

	/**
	 * Name of the fields action
	 * @var string
	 */
	const ACTION_FIELDS = 'fields';

	/**
	 * Name of the order fields action
	 * @var string
	 */
	const ACTION_ORDER_FIELDS = 'orderFields';

	/**
	 * Route to this controller
	 * @var string
	 */
	const ROUTE = 'ajax/joppa/content';

    public function fieldsAction($modelName) {
        $fields = array();

        if ($modelName !== '0') {
	        try {
	        	$fields = ContentOverviewPropertiesForm::getModelFieldOptions(ModelManager::getInstance(), $modelName);
	        } catch (Exception $exception) {
	        	Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
	        }
        }

        $view = new JsonView(array('fields' => $fields));
        $this->response->setView($view);
    }

    public function orderFieldsAction($modelName, $recursiveDepth) {
        $fields = array();

        if ($modelName !== '0') {
            try {
                $fields = ContentOverviewPropertiesForm::getModelFieldOptions(ModelManager::getInstance(), $modelName, true, false, $recursiveDepth);
            } catch (Exception $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString());
            }
        }

        $view = new JsonView(array('fields' => $fields));
        $this->response->setView($view);
    }

}