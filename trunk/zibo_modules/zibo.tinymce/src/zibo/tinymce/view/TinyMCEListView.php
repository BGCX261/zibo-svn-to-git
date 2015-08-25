<?php

namespace zibo\tinymce\view;

use zibo\core\View;

/**
 * View to display a javascript list, used by the dynamic lists of TinyMCE
 */
class TinyMCEListView implements View {

    /**
     * Name of the variable
     * @var string
     */
	private $varName;

	/**
	 * Values for the variable
	 * @var array
	 */
	private $list;

	/**
     * Constructs a new view for a dynamic list of TinyMCE
     * @param string $varName name of the variable
     * @param array $list values for the variable
     * @return null
	 */
	public function __construct($varName, array $list) {
		$this->varName = $varName;
		$this->list = $list;
	}

	/**
     * Renders this view
     * @param boolean $return true to return the rendered value, false to write it to the output
     * @return null|string
	 */
	public function render($return = true) {
		if (empty($this->list)) {
			return 'var ' . $this->varName . ' = new Array();';
		}

        $script = "var " . $this->varName . " = new Array(\n";
        foreach ($this->list as $value => $label) {
        	$script .= "\t[\"" . $label . '", "' . $value . "\"],\n";
        }
        $script = substr($script, 0, -2) . "\n);\n";

        if ($return) {
            return $script;
        }

        echo $script;
	}

}