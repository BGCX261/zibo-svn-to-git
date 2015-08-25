<?php

namespace zibo\library\html\form\field;

/**
 * List field implementation
 */
class ListField extends AbstractArrayField {

    /**
     * Internal flag to see if there are groups set
     * @var boolean
     */
    protected $hasGroupedOptions = false;

    /**
     * Flag to set if this list field will show the selected options as the first options
     * @var boolean
     */
    protected $showSelectedOptionsFirst = false;

    /**
     * Sets if this list field will show the selected options as the first options
     * @param boolean $flag
     * @return null
     */
    public function setShowSelectedOptionsFirst($flag) {
        $this->showSelectedOptionsFirst = $flag;
    }

    /**
     * Checks whether this field will show the selected options as the first options
     * @return boolean
     */
    public function willShowSelectedOptionsFirst() {
        return $this->showSelectedOptionsFirst;
    }

    /**
     * Sets the options of this list field
     * @param array $options options for this field, or for the group if provided
     * @param string $group name of the option group, the options provided are only for this group (optional)
     * @return null
     */
    public function setOptions(array $options, $group = null) {
        if ($group != null) {
            $this->options[$group] = $options;
            $this->hasGroupedOptions = true;
        } else {
            $this->options = $options;
            $this->hasGroupedOptions = false;
        }
    }

    /**
     * Gets the HTML of this list field
     * @return string
     */
    public function getHtml() {
        $html = '<select' .
            $this->getNameHtml() .
            ($this->isMultiple() ? ' multiple="multiple"' : '') .
            $this->getIdHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            $this->getIsDisabledHtml() . '>';

        $selected = $this->getDisplayValue();

        if ($this->hasGroupedOptions) {
            foreach ($this->options as $groupName => $options) {
                $html .= '<optgroup label="' . htmlspecialchars($groupName) . '">';
                $html .= $this->getHtmlOptions($options, $selected);
                $html .= '</optgroup>';
            }
        } else {
            if ($this->emptyKey !== null) {
                $isSelected = $this->isValueSelected($this->emptyKey, $this->emptyValue, $selected);
                $html .= $this->getHtmlOption($this->emptyKey, $this->emptyValue, $isSelected);
            }

            $html .= $this->getHtmlOptions($this->options, $selected);
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * Gets the HTML for an array of options
     * @param array $options array of options, will be decorated
     * @param mixed $selected value of the selected field(s)
     * @return string
     */
    protected function getHtmlOptions($options, $selected) {
        $htmlSelected = '';
        $html = '';

        foreach ($options as $key => $value) {
            $isSelected = false;

            $this->processValue($key, $value, $isSelected, $selected);

            if ($this->showSelectedOptionsFirst && $isSelected) {
                $htmlSelected .= $this->getHtmlOption($key, $value, $isSelected);
            } else {
                $html .= $this->getHtmlOption($key, $value, $isSelected);
            }
        }

        return $htmlSelected . $html;
    }

    /**
     * Gets the HTML of a option
     * @param string $value The actual value of the field
     * @param string $label The label of the field
     * @param boolean $isSelected Flag to see if this option is selected or not
     * @return string
     */
    protected function getHtmlOption($value, $label, $isSelected) {
        return "\t" . '<option value="' . htmlspecialchars($value) . '"' . ($isSelected ? ' selected="selected"' : '') . '>' . htmlspecialchars($label, ENT_NOQUOTES) . '</option>' . "\n";
    }

}