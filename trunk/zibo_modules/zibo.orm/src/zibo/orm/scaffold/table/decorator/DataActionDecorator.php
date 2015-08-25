<?php

namespace zibo\orm\scaffold\table\decorator;

use zibo\library\html\table\decorator\ActionDecorator;

/**
 * Action decorator for a orm data object
 */
class DataActionDecorator extends ActionDecorator {

    /**
     * Constructs a new data action decorator
     * @param string $href Base URL for the href attribute
     * @param string $label Translation key for the label
     * @param string $message Translation key for the confirmation message
     */
    public function __construct($href, $label, $message = null) {
        parent::__construct($href, $label, $message);
    }

    /**
     * Gets the href attribute for the provided value
     * @param mixed $value Value of the cell
     * @return string
     */
    protected function getHrefFromValue($value) {
        if (is_object($value)) {
            return $this->href . $value->id;
        }

        if (is_numeric($value)) {
            return $this->href . $value;
        }

        $this->setWillDisplay(false);
    }

}