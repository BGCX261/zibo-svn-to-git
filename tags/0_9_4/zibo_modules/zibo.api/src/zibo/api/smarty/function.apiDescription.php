<?php

/**
 * Get a description with a optional prefix and suffix
 * @param array $params Array with the parameters for this method.
 *
 * <ul>
 * <li>description: description to parse</li>
 * <li>prefix: prefix for the description</li>
 * <li>suffix: suffix for the description</li>
 * <li>url: url to the detail of a class</li>
 * <li>namespace: current namespace (optional)</li>
 * <li>classes: current classes (optional)</li>
 * </ul>*
 *
 * @param Smarty $smarty The Smarty engine
 * @return string The provided description with the prefix and suffix if the description is not empty
 */
function smarty_function_apiDescription($params, &$smarty) {
    if (!isset($params['description']) || !$params['description']) {
        return '';
    }

    $classAction = null;
    if (isset($params['url'])) {
        $classAction = $params['url'];
    }

    $namespace = null;
    if (isset($params['namespace'])) {
        $namespace = $params['namespace'];
    }

    $classes = null;
    if (isset($params['classes'])) {
        $classes = $params['classes'];
    }

    $description = $params['description'];

    $description = smarty_function_apiDescription_parseLinks($description, $classAction, $namespace, $classes);

    $prefix = '';
    if (isset($params['prefix'])) {
        $prefix = $params['prefix'];
    };

    $suffix = '';
    if (isset($params['suffix'])) {
        $suffix = $params['suffix'];
    };

    return $prefix . $description . $suffix;
}

/**
 * Parse the links tags in the description
 *
 * <p>A link tag has the folling format {@link url|method|class [label]}</p>
 * @param string $description the description to parse
 * @param string $classAction base URL to the detail page of a class
 * @param string $namespace the current namespace
 * @param array $classes the current class names
 * @return string the parsed description
 *
 * @todo implement this method
 */
function smarty_function_apiDescription_parseLinks($description, $classAction, $namespace = null, array $classes = null) {

//    $regex = '';
//
//    if (!$classAction) {
//        return $description;
//    }
//
//    $url = smarty_function_apiType_getTypeLink($url, $classAction, $namespace, $classes);
//    $html .= smarty_function_apiType_getTypeHtml($label, $url);

    return $description;
}