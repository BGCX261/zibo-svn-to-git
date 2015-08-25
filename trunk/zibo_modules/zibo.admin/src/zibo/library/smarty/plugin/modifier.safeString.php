<?php

use zibo\library\String;

function smarty_modifier_safeString($string) {
    return String::safeString($string);
}