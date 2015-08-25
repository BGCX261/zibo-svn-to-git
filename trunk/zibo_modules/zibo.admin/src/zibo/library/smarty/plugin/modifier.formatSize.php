<?php

use zibo\library\filesystem\Formatter;

function smarty_modifier_formatSize($string) {
    return Formatter::formatSize($string);
}