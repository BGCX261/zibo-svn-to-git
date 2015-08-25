<?php


namespace zibo\library\html\form\field;

use zibo\library\i18n\I18n;

/**
 * Boolean field implementation
 */
class BooleanField extends AbstractField {

    /**
     * Gets the HTML of this boolean field
     * @return string
     */
    public function getHtml() {
        $html = '<select' .
            $this->getNameHtml() .
            $this->getIdHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            $this->getIsDisabledHtml() . '>';

        $selected = $this->getDisplayValue();

        $translator = I18n::getInstance()->getTranslator();
        $html .= $this->getHtmlOption('', '---', $selected);
        $html .= $this->getHtmlOption('0', $translator->translate('label.no'), $selected);
        $html .= $this->getHtmlOption('1', $translator->translate('label.yes'), $selected);

        $html .= '</select>';

        return $html;
    }

    /**
     * Gets the HTML of a option
     * @param string $key value for the option
     * @param string $value label for the option
     * @param string $selected selected value
     * @return string
     */
    private function getHtmlOption($key, $value, $selected) {
        return "\t" . '<option value="' . $key . '"' . ($selected == $key ? ' selected="selected"' : '') . '>' . htmlspecialchars($value, ENT_NOQUOTES) . '</option>' . "\n";
    }

    /**
     * Process the request and update the value of this field if found in the request
     * @return null
     */
    public function processRequest() {
        if (!isset($_REQUEST[$this->name])) {
            return;
        }
        $value = $_REQUEST[$this->name];
        if ($value == '1') {
            $this->setValue(true);
        } elseif ($value == '0') {
            $this->setValue(false);
        } else {
            $this->setValue(null);
        }
    }

}