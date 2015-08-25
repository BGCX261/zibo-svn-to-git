<?php

namespace zibo\database\admin\table\decorator;

use zibo\library\i18n\I18n;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

use \Exception;

/**
 * Decorator for the status of a connection
 */
class StatusDecorator implements Decorator {

    /**
     * Style class for a failed connection
     * @var string
     */
	const STYLE_FAILURE = 'failure';

	/**
	 * Style class for a successful connection
	 * @var string
	 */
	const STYLE_SUCCESS = 'success';

	/**
	 * Translation key for the failure message
	 * @var string
	 */
	const TRANSLATION_FAILURE = "database.label.connection.failure";

	/**
	 * Translation key for the success message
	 * @var string
	 */
	const TRANSLATION_SUCCESS = "database.label.connection.success";

	/**
	 * Translator
	 * @var zibo\library\i18n\translation\Translator
	 */
	private $translator;

	/**
	 * Constructs a new connection status decorator
	 * @return null
	 */
	public function __construct() {
        $this->translator = I18n::getInstance()->getTranslator();
	}

	/**
	 * Decorates a connection with the status thereof
	 * @param zibo\library\html\table\Cell $cell The cell to decorate
	 * @param zibo\library\html\table\Row $row The row of the cell to decorate
	 * @param integer $rowNumber Number of the current row
	 * @param array $remainingValues Array containing the values of the remaining rows of the table
	 * @return null
	 */
	public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
		$connection = $cell->getValue();

		if ($connection->isConnectable()) {
			$this->setSuccessValue($cell);
		} else {
			$this->setFailureValue($cell, $connection->getConnectException());
		}
	}

	/**
     * Sets a success message to the provided cell
     * @param zibo\library\html\table\Cell $cell Cell to set a success message to
     * @return null
	 */
	private function setSuccessValue(Cell $cell) {
		$value = $this->translator->translate(self::TRANSLATION_SUCCESS);

		$this->setValue($cell, $value, self::STYLE_SUCCESS);
	}

	/**
     * Sets a failure message to the provided cell
     * @param zibo\library\html\table\Cell $cell Cell to set a failure message to
     * @return null
	 */
	private function setFailureValue(Cell $cell, Exception $e) {
		$value = $this->translator->translate(self::TRANSLATION_FAILURE, array('message' => $e->getMessage()));

		$this->setValue($cell, $value, self::STYLE_FAILURE);
	}

	/**
     * Sets a value to the provided cell
     * @param zibo\library\html\table\Cell $cell Cell to set a value to
     * @param string $value Value to set
     * @param string $style Style class for the cell
     * @return null
	 */
	private function setValue(Cell $cell, $value, $style) {
		$cell->appendToClass($style);
		$cell->setValue('<span>' . $value . '</span>');
	}

}