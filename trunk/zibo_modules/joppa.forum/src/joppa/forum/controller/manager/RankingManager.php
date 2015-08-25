<?php

namespace joppa\forum\controller\manager;

use joppa\forum\model\ForumRankingModel;
use joppa\forum\table\decorator\ForumRankingDecorator;
use joppa\forum\table\decorator\ForumRankingStarsDecorator;

use zibo\library\html\table\decorator\ZebraDecorator;

use zibo\orm\manager\controller\ScaffoldManager;

/**
 * Manager for the forum rankings
 */
class RankingManager extends ScaffoldManager {

	/**
	 * Translation key for this manager
	 * @var unknown_type
	 */
    const TRANSLATION_NAME = 'joppa.forum.title.manager.ranking';

    /**
     * Translation key for the add button and title
     * @var string
     */
    const TRANSLATION_ADD = 'joppa.forum.button.ranking.add';

    /**
     * Translation key for the ranking order
     * @var string
     */
    const TRANSLATION_RANKING = 'joppa.forum.label.manager.ranking';

    /**
     * Translation key for the posts order
     * @var string
     */
    const TRANSLATION_POSTS = 'joppa.forum.label.manager.ranking.posts';

    /**
     * Translation key for the stars order
     * @var string
     */
    const TRANSLATION_STARS = 'joppa.forum.label.manager.ranking.stars';

    /**
     * Constructs a new forum ranking manager
     * @return null
     */
    public function __construct() {
    	$translator = $this->getTranslator();

    	$icon = null;
    	$isReadOnly = false;
    	$search = true;
    	$order = array(
    	   $translator->translate(self::TRANSLATION_POSTS) => array(
    	       'ASC' => '{numPosts} ASC, {stars} ASC, {name} ASC',
    	       'DESC' => '{numPosts} DESC, {stars} DESC, {name} ASC',
    	   ),
    	   $translator->translate(self::TRANSLATION_STARS) => array(
    	       'ASC' => '{stars} ASC, {numPosts} ASC, {name} ASC',
    	       'DESC' => '{stars} DESC, {numPosts} DESC, {name} ASC',
    	   ),
    	   $translator->translate(self::TRANSLATION_RANKING) => array(
    	       'ASC' => '{name} ASC',
    	       'DESC' => '{name} DESC',
    	   ),
    	);
    	$pagination = false;

        parent::__construct(ForumRankingModel::NAME, self::TRANSLATION_NAME, $icon, $isReadOnly, $search, $order, $pagination);

        $this->translationAdd = self::TRANSLATION_ADD;
    }

    /**
     * Gets a data table for the model
     * @param string $formAction URL where the table form will point to
     * @return zibo\library\html\table\ExtendedTable
     */
    protected function getTable($formAction) {
    	$urlEdit = $this->request->getBasePath() . '/' . self::ACTION_EDIT . '/';

    	$table = parent::getTable($formAction);
    	$table->addDecorator(new ZebraDecorator(new ForumRankingDecorator($urlEdit)));
    	$table->addDecorator(new ForumRankingStarsDecorator());

    	return $table;
    }

}