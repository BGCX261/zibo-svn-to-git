<?php

use zibo\library\html\form\field\OptionField;
use zibo\library\html\form\Form;

/**
 * Get the HTML for a variable type.
 * @param array $params Array with the parameters for this method.
 *
 * <ul>
 * <li>type: type of the variable or class name</li>
 * <li>url: url to the detail of a class</li>
 * <li>method: link to this method of the class (optional)</li>
 * <li>link: variable name to set the generated link to (optional)</li>
 * <li>html: variable name to set the result to (optional)</li>
 * <li>namespace: current namespace (optional)</li>
 * <li>classes: current classes (optional)</li>
 * </ul>
 * @param Smarty $smarty The Smarty engine
 * @return string
 */
function smarty_function_apiType($params, &$smarty) {
    if (!isset($params['type'])) {
        throw new Exception('No type parameter provided');
    }
    if (!isset($params['url'])) {
        throw new Exception('No url parameter provided');
    }

    $type = $params['type'];
    $classAction = $params['url'];
    $label = $type;

    if (isset($params['method'])) {
        $label = $params['method'];
    }

    if (isset($params['method']) || isset($params['html']) || isset($params['link'])) {
        $url = smarty_function_apiType_getTypeLink($type, $classAction);

        if ($url && isset($params['method'])) {
            $url .= '#method' . $params['method'];
        }

        if (isset($params['link'])) {
            $smarty->assign($params['link'], $url);
            return;
        }

        $html = smarty_function_apiType_getTypeHtml($label, $url);

        if (isset($params['html'])) {
            $smarty->assign($params['html'], $html);
            return;
        }

        return $html;
    }

    $namespace = null;
    if (isset($params['namespace'])) {
        $namespace = $params['namespace'];
    }

    $classes = null;
    if (isset($params['classes'])) {
        $classes = $params['classes'];
    }

    $types = explode('|', $type);

    $html = '';
    foreach ($types as $type) {
        if ($html) {
            $html .= ' | ';
        }

        $url = smarty_function_apiType_getTypeLink($type, $classAction, $namespace, $classes);
        $html .= smarty_function_apiType_getTypeHtml($type, $url);
    }

    return $html;
}

/**
 * Get the URL to a type, if available
 * @param string $type type to get the URL for
 * @param string $classAction base URL to a class detail view
 * @param string $namespace the current namespace
 * @param array $classes the current classes
 * @return string
 */
function smarty_function_apiType_getTypeLink($type, $classAction, $namespace = null, array $classes = null) {
    if ($type[0] == '\\') {
        $type = substr($type, 1);
    }

    $suffix = null;

    if (strpos($type, '::') !== false) {
        list($type, $method) = explode('::', $type, 2);
        $suffix = '#method' . $method;
    }

    if (strpos($type, '\\') !== false) {
        return $classAction . str_replace('\\', '/', $type) . $suffix;
    }

    if ($namespace == null || $classes == null) {
        return null;
    }

    $typeKey = array_search($type, $classes);
    if ($typeKey === false) {
        return null;
    }

    return $classAction . $namespace . '/' . $type . $suffix;
}

/**
 * Get the HTML anchor if the URL is set, the type if no URL set
 * Enter description here...
 * @param string $type
 * @param string $url
 * @return string
 */
function smarty_function_apiType_getTypeHtml($type, $url) {
    if ($url) {
        return '<a href="' . $url . '">' . $type . '</a>';
    }

    return $type;
}