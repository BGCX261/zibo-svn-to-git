<?php

namespace zibo\library\html\form\field;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Option field implementation
 */
class OptionField extends AbstractArrayField {

    /**
     * Gets the HTML of this option field
     * @return string
     */
    public function getHtml() {
        $html = '';

        if ($this->options) {
            $selected = $this->getDisplayValue();

            if ($this->emptyKey !== null) {
                $isSelected = $this->isValueSelected($this->emptyKey, $this->emptyValue, $selected);
                $html .= $this->getHtmlOption($this->emptyKey, $this->emptyValue, $isSelected);
            }

            foreach ($this->options as $key => $value) {
                $html .= '<div class="option">' . $this->getOption($key, $value, $selected) . '</div>';
            }
        } else {
            $isSelected = $this->defaultValue == $this->value;

            $key = $this->defaultValue;
            if ($this->keyDecorator) {
                $key = $this->keyDecorator->decorate($key);
            }

            if ($this->isMultiple() && is_array($this->value)) {
                $isSelected = isset($this->value[$key]);
            }

            $html .= $this->getHtmlOption($key, null, $isSelected);
        }

        return $html;
    }

    /**
     * Gets the HTML for a specific option in this field
     * @param string $option key of the option
     * @return string
     * @throws zibo\ZiboException when no options set for this field
     * @throws zibo\ZiboException when the provided option could not be found
     */
    public function getOptionHtml($option) {
        $html = '';

        if (!$this->options) {
            throw new ZiboException('No options set');
        }

        if (!isset($this->options[$option])) {
            throw new ZiboException($option . ' is not in the option list');
        }

        return $this->getOption($option, $this->options[$option], $this->getDisplayValue());
    }

    /**
     * Decorates the option values and gets the HTML of the option
     * @param mixed $key name of the option before decorating
     * @param mixed $value value for the option before decorating
     * @param mixed $selected selected value
     * @return string
     */
    protected function getOption($key, $value, $selected) {
        $isSelected = false;

        $this->processValue($key, $value, $isSelected, $selected);

        return $this->getHtmlOption($key, $value, $isSelected);
    }

    /**
     * Gets the HTML of a option
     * @param string $key name of the option
     * @param string $value value for the option
     * @param boolean $isSelected flag to see if this option is selected
     * @return string
     */
    protected function getHtmlOption($key, $value, $isSelected) {
        $idSuffix = ucfirst(String::safeString($key));
        $id = $this->getId() . $idSuffix;

        $html = '<input' .
            $this->getAttributeHtml('type', $this->isMultiple() ? 'checkbox' : 'radio') .
            $this->getNameHtml() .
            $this->getAttributeHtml('value', $key) .
            ($isSelected ? $this->getAttributeHtml('checked', 'checked') : '') .
            $this->getAttributeHtml('id', $id) .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            ' />';

        if (!$value) {
            return $html;
        }

        $html .= '<label';

        if ($id) {
            $html .= $this->getAttributeHtml('for', $id);
        }

        $html .= '>' . $value . '</label>';

        return $html;
    }

}