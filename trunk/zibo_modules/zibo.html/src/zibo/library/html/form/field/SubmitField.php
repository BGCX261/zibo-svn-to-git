<?php

namespace zibo\library\html\form\field;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Implementation of a submit button
 */
class SubmitField extends AbstractArrayField {

    /**
     * Style class for a submit button
     * @var string
     */
    const CLASS_SUBMIT = 'submit';

    /**
     * Add the submit class to this field
     * @return null
     */
    protected function init() {
        $this->appendToClass(self::CLASS_SUBMIT);
    }

    /**
     * Gets the HTML of this submit button
     * @return string
     * @throws zibo\ZiboException when no options set for this field and the field has multiple values
     */
    public function getHtml() {
        if ($this->isMultiple) {
            throw new ZiboException('This submit field has multiple values, please use getOptionHtml instead of getHtml');
        }

        return $this->getSubmitHtml($this->getId(), $this->getDisplayValue());
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

        $idSuffix = ucfirst(String::safeString($option));
        $id = $this->getId() . $idSuffix;
        $value = $this->options[$option];
        $isSelected = false;
        $selected = null;

        $this->processValue($option, $value, $isSelected, $selected);

        return $this->getSubmitHtml($id, $value, $option);
    }

    /**
     * Gets the HTML of a submit button
     * @param string $id Id of the button
     * @param string $value Value for the label of the button
     * @param string $option Key for the value of the option
     * @return string The HTML of the button
     */
    protected function getSubmitHtml($id, $value, $option = null) {
        return '<input type="submit"' .
            $this->getNameHtml($option) .
            $this->getAttributeHtml('value', $value) .
            $this->getAttributeHtml('id', $id) .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            $this->getIsDisabledHtml() .
            ' />';
    }

}