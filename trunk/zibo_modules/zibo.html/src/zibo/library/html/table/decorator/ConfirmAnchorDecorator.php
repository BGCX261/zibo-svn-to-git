<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;

/**
 * Abstract decorator to create an anchor with confirmation message from a cell value
 */
abstract class ConfirmAnchorDecorator extends AnchorDecorator {

    /**
     * The confirmation message
     * @var string
     */
    protected $message;

    /**
     * Constructs a new anchor decorator with confirmation message
     * @param string $href Base href attribute for the anchor
     * @param string $message The confirmation message
     * @return null
     */
    public function __construct($href, $message = null) {
        parent::__construct($href);

        $this->message = $message;
    }

    /**
     * Adds the confirmation message to the anchor
     * @param zibo\library\html\Anchor $anchor Generated anchor for the cell
     * @param mixed $value Value of the cell
     * @return null
     */
    protected function processAnchor(Anchor $anchor, $value) {
        $message = $this->processMessage($value);

        if ($message) {
            $anchor->setAttribute('onclick', 'return confirm(\'' . $message . '\');');
        }
    }

    /**
     * Hook to process the message with the value of the cell
     * @param mixed $value Value of the cell
     * @return string|null The message to use for the confirmation, null for no confirmation
     */
    protected function processMessage($value) {
        return $this->message;
    }

}