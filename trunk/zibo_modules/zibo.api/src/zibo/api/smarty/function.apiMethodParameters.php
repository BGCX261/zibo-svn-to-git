<?php

/**
 * Get a API string for the parameters of a method
 * @param array $params Array with the parameters for this method.
 *
 * <ul>
 * <li>method: a reflection class instance</li>
 * <li>url: url to the detail of a class</li>
 * <li>namespace: current namespace (optional)</li>
 * <li>classes: current classes (optional)</li>
 * </ul>
 * @param Smarty $smarty The Smarty engine
 * @return string
 */
function smarty_function_apiMethodParameters($params, &$smarty) {
    if (!isset($params['method'])) {
        throw new Exception('No method parameter provided');
    }
    if (!isset($params['url'])) {
        throw new Exception('No url parameter provided');
    }

    $url = $params['url'];
    $method = $params['method'];
    $methodDoc = $method->getDoc();

    $parameters = $method->getParameters();
    if (!$parameters) {
        return '()';
    }

    $namespace = null;
    if (isset($params['namespace'])) {
        $namespace = $params['namespace'];
    }

    $classes = null;
    if (isset($params['classes'])) {
        $classes = $params['classes'];
    }

    $numberDefaultParameters = 0;
    $html = '';
    foreach ($parameters as $parameter) {
        $parameterName = '$' . $parameter->getName();
        $parameterDoc = $methodDoc->getParameter($parameterName);
        $type = $parameterDoc->getType();

        $parameterHtml = $html ? ', ' : '';

        if ($type) {
            $apiTypeParams = array(
                'type' => $type,
                'url' => $url,
                'namespace' => $namespace,
                'classes' => $classes
            );
            $parameterHtml .= smarty_function_apiType($apiTypeParams, $smarty) . ' ';
        }

        $parameterHtml .= $parameterName;

        if ($parameter->isDefaultValueAvailable()) {
            $defaultValue = $parameter->getDefaultValue();
            if (is_string($defaultValue)) {
                $defaultValue = "'" . $defaultValue . "'";
            } elseif (is_bool($defaultValue)) {
                $defaultValue = $defaultValue ? 'true' : 'false';
            } elseif (is_null($defaultValue)) {
                $defaultValue = 'null';
            }
            $parameterHtml = ($html ? ' ' : '') . '[' . $parameterHtml . ' = ' . $defaultValue;
            $numberDefaultParameters++;
        }

        $html .= $parameterHtml;
    }

    $html .= str_repeat(']', $numberDefaultParameters);

    return '(' . $html . ')';
}