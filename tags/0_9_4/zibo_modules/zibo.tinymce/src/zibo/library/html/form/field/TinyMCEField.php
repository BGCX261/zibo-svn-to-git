<?php

namespace zibo\library\html\form\field;

use zibo\core\view\HtmlView;
use zibo\core\Zibo;

use zibo\library\Boolean;

use zibo\ZiboException;

/**
 * TinyMCE text field
 */
class TinyMCEField extends TextField {

    /**
     * Configuration prefix for the TinyMCE parameters
     * @var string
     */
	const CONFIG_TINYMCE = 'tinymce';

	/**
	 * Route to the dynamic images script
	 * @var string
	 */
    const ROUTE_IMAGES = '/tinymce/images';

    /**
     * Route to the dynamic links script
     * @var string
     */
    const ROUTE_LINKS = '/tinymce/links';

    /**
     * Route to the TinyMCE javascript
     * @var string
     */
	const SCRIPT_TINYMCE = '/web/tinymce/tiny_mce.js';

	/**
	 * Route to the JQuery javascript for TinyMCE
	 * @var string
	 */
	const SCRIPT_TINYMCE_JQUERY = 'web/tinymce/jquery.tinymce.js';

	/**
	 * Parameters for the TinyMCE field
	 * @var array
	 */
	private $tinymceParams;

	/**
     * Constructs a new TinyMCE field
     * @param string $name name of the field
     * @param mixed $defaultValue value for the initialization of the field
     * @param boolean $isDisabled flag to enable or disable the field
     * @return null
	 */
    public function __construct($name, $defaultValue = null, $isDisabled = false) {
    	parent::__construct($name, $defaultValue, $isDisabled);

    	$zibo = Zibo::getInstance();
		$zibo->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, 'preResponse'));
		$this->tinymceParams = $zibo->getConfigValue(self::CONFIG_TINYMCE, array());
	}

	/**
     * Sets a parameter for TinyMCE
     * @param string $key name of the parameter
     * @param string $value value for the parameter
     * @return null
	 */
	public function setTinyMCEParameter($key, $value) {
		$this->tinymceParams[$key] = $value;
	}

	/**
     * Gets a TinyMCE parameter
     * @param string $key name of the parameter
     * @param mixed $default default value for when the parameter is not set
     * @return string|mixed the value of the parameter or the provided default value if the parameter is not set
	 */
	public function getTinyMCEParameter($key, $default = null) {
		if (isset($this->tinymceParams[$key])) {
			return $this->tinymceParams[$key];
		}
		return $default;
	}

	/**
	 * Gets all the TinyMCE parameters
	 * @return array
	 */
	public function getTinyMCEParameters() {
		return $this->tinymceParams;
	}

	/**
	 * Adds the TinyMCE javascripts to the view
	 * @return null
	 */
	public function preResponse() {
		$response = Zibo::getInstance()->getResponse();
		$view = $response->getView();

		if (!($view instanceof HtmlView)) {
			return;
		}

		$view->addJavascript(self::SCRIPT_TINYMCE_JQUERY);
		$view->addInlineJavascript($this->getInitializationScript());
	}

	/**
	 * Gets the inline javascript for this TinyMCE field
	 * @return string
	 */
	private function getInitializationScript() {
		$request = Zibo::getInstance()->getRequest();
		$baseUrl = $request->getBaseUrl();

		$script = "\n$('#" . $this->getId() . "').tinymce({\n";
        $script .= "\tscript_url : '" . $baseUrl . self::SCRIPT_TINYMCE . "',\n";
        $script .= "\texternal_image_list_url : '" . $baseUrl . self::ROUTE_IMAGES . "',\n";
        $script .= "\texternal_link_list_url : '" . $baseUrl . self::ROUTE_LINKS . "',\n";
		foreach ($this->tinymceParams as $key => $value) {
			try {
				Boolean::getBoolean($value);
				$script .= "\t" . $key . ' : ' . $value . ",\n";
			} catch (ZiboException $e) {
				$script .= "\t" . $key . " : '" . $value . "',\n";
			}
		}
        $script = substr($script, 0, -2) . "\n";
        $script .= "});\n";

        return $script;
	}

}