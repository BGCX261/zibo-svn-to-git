<?php

use zibo\library\String;

function smarty_function_head($params, &$smarty) {
    $baseUrl = $smarty->get_template_vars('_baseUrl') . '/';
    $title = $smarty->get_template_vars('_title');
    $pageTitle = $smarty->get_template_vars('_pageTitle');
    $meta = $smarty->get_template_vars('_meta');
    $scripts = $smarty->get_template_vars('_scripts');
    $inlineScripts = $smarty->get_template_vars('_inlineScripts');
    $custom = $smarty->get_template_vars('_custom');
    $styles = $smarty->get_template_vars('_styles');
    $stylesIE = $smarty->get_template_vars('_stylesIE');

    $html = '';
    $html .= smarty_function_head_addMeta($meta);
    $html .= smarty_function_head_addTitle($title, $pageTitle);
    $html .= smarty_function_head_addStyles($baseUrl, $styles, $stylesIE);
    $html .= smarty_function_head_addJavascripts($baseUrl, $scripts, $inlineScripts);

    foreach ($custom as $customCode) {
        $html .= $customCode . "\n";
    }

    return $html;
}

function smarty_function_head_addMeta($meta) {
    $html = "\n\t\t" . '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";

    if (!is_array($meta)) {
        return $html;
    }

    foreach ($meta as $metaElement) {
        $html .= "\t\t" . $metaElement->getHtml() . "\n";
    }

    return $html;
}

function smarty_function_head_addTitle($title, $pageTitle) {
    if (!$title && !$pageTitle) {
        return '';
    }

    $html = "\t\t" . '<title>';
    if ($pageTitle) {
        $html .= $pageTitle;
        if ($title) {
            $html .= ' - ' . $title;
        }
    } else {
        $html .= $title;
    }
    $html .= '</title>' . "\n\n";

    return $html;
}

function smarty_function_head_addStyles($baseUrl, $styles, $stylesIE) {
    $html = '';
    if ($styles) {
        foreach ($styles as $style) {
            $html .= "\t\t" . '<link rel="stylesheet" type="text/css" href="' . smarty_function_head_get_url($style, $baseUrl) . '" />' . "\n";
        }
    }

    if ($stylesIE) {
        foreach ($stylesIE as $condition => $style) {
            $html .= "\t\t" . '<!--[if ' . $condition . ']><link rel="stylesheet" type="text/css" href="' . smarty_function_head_get_url($style, $baseUrl) . '" media="screen"><![endif]-->' . "\n";
        }
    }

    $html .= "\n";

    return $html;
}

function smarty_function_head_addJavascripts($baseUrl, array $scripts = null, array $inlineScripts = null) {
    $html = '';

    if ($scripts) {
        foreach ($scripts as $script) {
            $html .= "\t\t" . '<script type="text/javascript" src="' . smarty_function_head_get_url($script, $baseUrl) . '"></script>' . "\n";
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

function smarty_function_head_get_url($url, $baseUrl) {
    if (String::looksLikeUrl($url)) {
        return $url;
    }

    return $baseUrl . $url;
}