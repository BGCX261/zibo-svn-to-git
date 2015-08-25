<?php

use zibo\library\String;

function smarty_function_scripts($params, &$smarty) {
    $baseUrl = $smarty->get_template_vars('_baseUrl') . '/';

    $scripts = $smarty->get_template_vars('_scripts');
    $inlineScripts = $smarty->get_template_vars('_inlineScripts');

    $html = '';

    if ($scripts) {
        foreach ($scripts as $script) {
            $html .= "\t\t" . '<script type="text/javascript" src="' . smarty_function_scripts_get_url($script, $baseUrl) . '"></script>' . "\n";
        }
    }

    if ($inlineScripts) {
        $html .= "\t\t" . '<script type="text/javascript">' . "\n";
        $html .= "\t\t\t$(function() {\n";

        foreach ($inlineScripts as $script) {
            $html .= "\t\t\t\t" . $script . "\n";
        }

        $html .= "\t\t\t});\n";
        $html .= "\t\t" . '</script>' . "\n";
    }

    return $html;
}

function smarty_function_scripts_get_url($url, $baseUrl) {
    if (String::looksLikeUrl($url)) {
        return $url;
    }

    return $baseUrl . $url;
}