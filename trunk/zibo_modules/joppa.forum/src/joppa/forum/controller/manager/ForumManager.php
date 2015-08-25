<?php

namespace joppa\forum\controller\manager;

use zibo\manager\model\AbstractManager;

/**
 * Main controller of the forum manager
 */
class ForumManager extends AbstractManager {

    /**
     * Translation key for the name of the manager
     * @var string
     */
	const TRANSLATION_NAME = 'joppa.forum.title.manager';

	/**
	 * Path to the icon of the forum manager
	 * @var string
	 */
	const ICON = 'web/images/forum/icon.png';

	/**
	 * Class name of the forum ranking manager
	 * @var string
	 */
	const CONTROLLER_RANKING = 'joppa\\forum\\controller\\manager\\RankingManager';

	/**
	 * Class name of the forum structure manager
	 * @var string
	 */
	const CONTROLLER_STRUCTURE = 'joppa\\forum\\controller\\manager\\StructureManager';

	/**
	 * Action to the ranking controller
	 * @var string
	 */
	const ACTION_RANKING = 'ranking';

	/**
	 * Action to the structure controller
	 * @var string
	 */
	const ACTION_STRUCTURE = 'structure';

	/**
	 * Path to the javascript of the manager
	 * @var string
	 */
	const SCRIPT_MANAGER = 'web/scripts/forum/manager.js';

	/**
	 * Constructs a new forum manager
	 * @return null
	 */
	public function __construct() {
		parent::__construct(self::TRANSLATION_NAME, self::ICON);
	}

	/**
	 * Gets the actions of this manager
	 * @return array Array with the path of the action as key and the title as value
	 */
	public function getActions() {
		$translator = $this->getTranslator();

		return array(
			self::ACTION_STRUCTURE => $translator->translate(StructureManager::TRANSLATION_NAME),
			self::ACTION_RANKING => $translator->translate(RankingManager::TRANSLATION_NAME),
		);
	}

	/**
	 * Action to dispatch the ranking manager
	 * @return zibo\core\Request Request to the ranking manager
	 */
	public function rankingAction() {
		return $this->forward(self::CONTROLLER_RANKING);
	}

	/**
	 * Action to dispatch the structure manager
	 * @return zibo\core\Request Request to the structure manager
	 */
	public function structureAction() {
		return $this->forward(self::CONTROLLER_STRUCTURE);
	}

}