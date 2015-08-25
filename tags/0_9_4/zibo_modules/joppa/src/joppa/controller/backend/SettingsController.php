<?php

namespace joppa\controller\backend;

use joppa\form\SettingsForm;
use joppa\model\NodeSettingModel;
use joppa\view\SettingsView;

use zibo\admin\controller\AbstractController;

use zibo\core\Zibo;

use \Exception;

/**
 * Controller to edit the global Joppa settings
 */
class SettingsController extends AbstractController {

	/**
	 * Action to process and show the Joppa settings form
	 * @return null
	 */
	public function indexAction() {
		$zibo = Zibo::getInstance();
		$basePath = $this->request->getBasePath();

		$isPublished = $zibo->getConfigValue(NodeSettingModel::CONFIG_PUBLISH_DEFAULT, NodeSettingModel::DEFAULT_PUBLISH);

		$form = new SettingsForm($basePath, $isPublished);
		if ($form->isSubmitted()) {
			$isPublished = $form->isPublished();

    		try {
                $zibo->setConfigValue(NodeSettingModel::CONFIG_PUBLISH_DEFAULT, $isPublished);
			} catch (Exception $e) {
				$zibo->runEvent(Zibo::EVENT_LOG, $e->getMessage(), $e->getTraceAsString());
				$this->addError(self::TRANSLATION_ERROR, array('error' => $e-getMessage()));
			}
		}

		$view = new SettingsView($form);
		$this->response->setView($view);
	}

}