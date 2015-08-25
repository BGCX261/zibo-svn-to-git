<?php

namespace zibo\dashboard\view;

use zibo\core\View;

class StaticView implements View {

    private $content;

    public function __construct($content) {
        $this->content = $content;
    }

    public function render($return = true) {
        if ($return) {
            return $this->content;
        }

        echo $this->content;
    }

}