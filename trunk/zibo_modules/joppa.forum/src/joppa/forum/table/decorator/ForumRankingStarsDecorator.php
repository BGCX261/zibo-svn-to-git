<?php

namespace joppa\forum\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

/**
 * Decorator for a ranking
 */
class ForumRankingStarsDecorator implements Decorator {

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

		$value = $ranking->getStarsHtml();

        $cell->setValue($value);
	}

}