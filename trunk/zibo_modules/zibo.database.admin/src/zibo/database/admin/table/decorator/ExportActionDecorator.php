<?php

namespace zibo\database\admin\table\decorator;

use zibo\library\html\table\decorator\ActionDecorator;

/**
 * Decorator for the action to export the database of a connection
 */
class ExportActionDecorator extends ActionDecorator {

    /**
     * Translation key for the export button
     * @var string
     */
    const TRANSLATION_EXPORT = 'database.action.connection.export';

    /**
     * Constructs a new export action decorator
     * @param string $href Base URL for the export action, the name of the connection will be added to this string
     * @return null
     */
	public function __construct($href) {
	    parent::__construct($href, self::TRANSLATION_EXPORT);
	}

    /**
     * Gets the href attribute for the anchor
     * @param mixed $value Value of the cell
     * @return string Href attribute for the anchor
     */
    protected function getHrefFromValue($value) {
        if (!$value->isConnectable()) {
            $this->setWillDisplay(false);
        }

        return $this->href . $value->getName();
    }

}