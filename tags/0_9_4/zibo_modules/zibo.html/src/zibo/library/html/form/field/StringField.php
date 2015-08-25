<?php

namespace zibo\library\html\form\field;

use zibo\core\view\HtmlView;
use zibo\core\Zibo;

use zibo\jquery\Module as JQueryModule;

/**
 * String field implementation
 */
class StringField extends AbstractField {

    /**
     * The source for the autocomplete function
     * @var string|array
     */
    protected $autoCompleteSource;

    /**
     * The minimum length before auto completing
     * @var integer
     */
    protected $autoCompleteMinLength;

    /**
     * Gets the HTML of this string field
     * @return string
     */
    public function getHtml() {
        return '<input type="text"' .
            $this->getIdHtml() .
            $this->getNameHtml() .
            $this->getDisplayValueHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            $this->getIsDisabledHtml() .
            ' />';
    }

    /**
     * Enables auto completion on this field
     * @param string|array $source URL or array for the auto completion values
     * @param integer $minLength Number of characters to be types before starting the auto completion
     * @return null
     */
    public function setAutoComplete($source, $minLength = null) {
        if (!$this->autoCompleteSource) {
            Zibo::getInstance()->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, 'preResponse'));
        }

        $this->autoCompleteSource = $source;
        $this->autoCompleteMinLength = $minLength;

        $this->setAttribute('autocomplete', 'off');
    }

    /**
     * Add javascript and style, needed for the auto completion of this field. If no HtmlView is set to the response, nothing will be done.
     * @return null
     */
    public function preResponse() {
        $response = Zibo::getInstance()->getResponse();
        $view = $response->getView();
        if (!($view instanceof HtmlView)) {
            return;
        }

        $view->addJavascript(JQueryModule::SCRIPT_JQUERY);
        $view->addJavascript(JQueryModule::SCRIPT_JQUERY_UI);
        $view->addStyle(JQueryModule::STYLE_JQUERY_UI);
        $view->addInlineJavascript($this->getInitializationScript());
    }

    /**
     * Get the inline javascript to initialize the autocompletion of this field
     * @return string
     */
    protected function getInitializationScript() {
        $id = $this->getId();

        $script = '';

        if (is_array($this->autoCompleteSource)) {
            // create the autocompletion array
            $source = 'autoComplete' . ucwords($id);

            $script .= "\nvar " . $source . " [\n";

            foreach ($this->autoCompleteSource as $data) {
                $script .= "\t'" . $data . "',\n";
            }

            $script = substr($script, 0, -2) . "\n];";
        } else {
            $source = '"' . $this->autoCompleteSource . '"';
        }

        $script .= "$('#" . $this->getId() . "').autocomplete({\n";

        if ($this->autoCompleteMinLength) {
            $script .= "\t\t\t\t\tminLength: " . $this->autoCompleteMinLength . ",\n";
        }

        $script .= "\t\t\t\t\tsource: " . $source . "\n";

        $script .= "\t\t\t\t});";

        return $script;
    }

}