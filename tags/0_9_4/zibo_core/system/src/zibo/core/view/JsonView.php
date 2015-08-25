<?php

namespace zibo\core\view;

use zibo\core\View;

/**
 * View for a JSON response
 */
class JsonView implements View {

    /**
     * Value to be encoded to JSON
     * @var mixed
     */
    private $value;

    /**
     * Options for the json_encode function
     * @var int
     */
    private $options;

    /**
     * Constructs a new JSON view
     * @param mixed $value Value to be encoded to JSON
     * @param int $options Options for the json_encode function
     * @return null
     */
    public function __construct($value, $options = 0) {
        $this->value = $value;
        $this->options = $options;
    }

    /**
     * Renders this view
     * @param boolean $return Flag to see whether to return the rendered view or to send it to the client
     * @return null|string
     */
    public function render($return = true) {
        $encoded = json_encode($this->value, $this->options);

        if ($return) {
            return $encoded;
        }

        echo $encoded;
    }


}