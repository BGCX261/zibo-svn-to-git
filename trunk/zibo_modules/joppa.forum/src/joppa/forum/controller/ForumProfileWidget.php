<?php

namespace joppa\forum\controller;

use joppa\controller\JoppaWidget;

use joppa\forum\model\ForumProfileModel;
use joppa\forum\view\ForumProfileView;
use joppa\forum\Module;

use joppa\model\content\ContentFacade;

use zibo\library\validation\ValidationException;

use \Exception;

/**
 * Controller to display the profiles of the forum users
 */
class ForumProfileWidget extends JoppaWidget {

	/**
	 * Action to view the detail of a profile
	 * @var string
	 */
	const ACTION_DETAIL = 'detail';

	/**
	 * Translation key for the name of this widget
	 * @var string
	 */
	const TRANSLATION_NAME = 'joppa.forum.title.widget.profile';

	/**
	 * Hook with the ORM module
	 * @var array
	 */
	public $useModels = array(
        ForumProfileModel::NAME,
    );

    /**
     * Constructs a new forum widget
     * @return null
     */
	public function __construct() {
		parent::__construct(self::TRANSLATION_NAME, Module::ICON);
	}

	/**
	 * Action to get the index of the forum profiles
	 * @return null
	 */
	public function indexAction() {

	}

	/**
	 * Action to display the detail of a forum profile
	 * @param integer $id Id of the profile
	 * @return null
	 */
	public function detailAction($id) {
		$profile = $this->models[ForumProfileModel::NAME]->findById($id);
		if (!$profile) {
			$this->setError404();
			return;
		}

		$view = new ForumProfileView($profile);
		$this->response->setView($view);
	}

}