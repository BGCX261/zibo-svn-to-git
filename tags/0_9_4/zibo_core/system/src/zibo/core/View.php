<?php

namespace zibo\core;

/**
 * Creates output to send back as a response
 */
interface View {

    /**
     * Render the view
     * @param boolean $return true to return the rendered view, false to send it to the client
     * @return mixed null when provided $return is set to true; the rendered output when the provided $return is set to false
     */
    public function render($return = true);

}