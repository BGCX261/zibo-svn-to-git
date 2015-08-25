<?php

namespace joppa\forum\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\i18n\I18n;

/**
 * Decorator for a ranking
 */
class ForumRankingDecorator implements Decorator {

	/**
	 * Translation key for the needed posts label
	 * @var string
	 */
	const TRANSLATION_POSTS = 'joppa.forum.label.manager.ranking.info';

	/**
	 * URL where the name of the ranking will point to
	 * @var string
	 */
	private $action;

	/**
	 * Instance of the translator
	 * @var zibo\library\i18n\translation\Translator;
	 */
	private $translator;

	/**
	 * Constructs a new ForumRanking decorator
	 * @param string $action URL where the name of the ranking will point to
	 * @return null
	 */
	public function __construct($action = null) {
		$this->action = $action;
		$this->translator = I18n::getInstance()->getTranslator();
	}

	/**
	 * Decorates the cell containing a ForumRanking
	 * @param Cell $cell
	 * @param Row $row
	 * @param integer $rowNumber
	 * @param array $remainingValues
	 * @return null
	 */
	public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
		$ranking = $cell->getValue();

		$value = $ranking->name;

		if ($this->action) {
			$anchor = new Anchor($value, $this->action . $ranking->id);
			$value = $anchor->getHtml();
		}

		$value .= '<div class="info">';
        $value .= $this->translator->translate(self::TRANSLATION_POSTS, array('posts' => $ranking->numPosts, 'stars' => $ranking->stars));
		$value .= '</div>';

        $cell->setValue($value);
	}

}