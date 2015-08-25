<?php

namespace zibo\admin\view\system;

use zibo\core\View;

/**
 * Configuration view
 */
class ConfigurationView implements View {

    /**
     * The configuration to display
     * @var array
     */
    private $configuration;

    /**
     * Constructs a new configuration view
     * @param array $configuration Array with the configuration to display
     * @return null
     */
    public function __construct(array $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Renders this view
     * @param boolean $return flag to return or output the rendered view
     * @return null|string
     */
    public function render($return = true) {
        $output = '';

        foreach ($this->configuration as $key => $value) {
            $output .= str_pad($key, 50) . ' : ' . $value . "\n";
        }

        if (!$return) {
            echo $output;
        }

        return $output;
    }

}