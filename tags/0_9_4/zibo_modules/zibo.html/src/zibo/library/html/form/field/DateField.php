<?php

namespace zibo\library\html\form\field;

use zibo\core\view\HtmlView;
use zibo\core\Zibo;

use zibo\jquery\Module as JQueryModule;

use zibo\library\i18n\I18n;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

use \Exception;

/**
 * Date field implementation
 */
class DateField extends AbstractField {

    /**
     * Hook to perform extra initialization for this field when constructing a new instance
     * @return null
     */
    protected function init() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(Zibo::EVENT_PRE_RESPONSE, array($this, 'preResponse'));
    }

    /**
     * Get the HTML of this field
     * @return string
     */
    public function getHtml() {
        // the datepicker requires that the input element has an id
        // so if there is no id yet, we generate one
        if ($this->getId() === null) {
            $this->setId(uniqid('', true));
        }

        $locale = I18n::getInstance()->getLocale();
        $dateFormat = $locale->getDateFormat();

        $translator = I18n::getInstance()->getTranslator();
        $labelParams = array('example' => $locale->formatDate(time()), 'format' => $dateFormat);

        $html = '';
        $html .= '<span>' . $translator->translate('label.date.description', $labelParams) . '</span>';
        $html .= '<input type="text"' . $this->getIdHtml() . $this->getNameHtml();

        $displayValue = $this->getDisplayValue();
        if (is_numeric($displayValue) && !empty($displayValue)) {
            $displayValue = $locale->formatDate($displayValue);
        }

        if (!empty($displayValue)) {
            $html .= $this->getAttributeHtml('value', $displayValue);
        }

        $html .= $this->getClassHtml() . $this->getAttributesHtml() . $this->getIsDisabledHtml() . ' />';

        return $html;
    }

    /**
     * Get the value from the request
     * @param string $name name of the request value
     * @return mixed value of this field
     * @throws zibo\library\validation\exception\ValidationException when a invalid date is entered
     */
    protected function getRequestValue($name = null) {
        $value = parent::getRequestValue($name);

        if (!$value) {
            return $value;
        }

        try {
            $locale = I18n::getInstance()->getLocale();
            $value = $locale->parseDate($value);
            $value = mktime(0, 0, 0, date('m', $value), date('d', $value), date('Y', $value));
        } catch (Exception $e) {
            $error = new ValidationError('error.date.format', '%value% is not in the right format', array('value' => $value));
            $exception = new ValidationException();
            $exception->addErrors($this->getName(), array($error));
            throw $exception;
        }

        return $value;
    }

    /**
     * Add javascript and style, needed for this date field, to the view. If no HtmlView is set to the response, nothing will be done.
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
     * Get the inline javascript to initialize this date field
     * @return string
     */
    private function getInitializationScript() {
        $locale = I18n::getInstance()->getLocale();

        $dateFormatConverter = new DatepickerFormatConverter();
        $dateFormat = $locale->getDateFormat();
        $dateFormat = $dateFormatConverter->convertFormatFromPhp($dateFormat);

        $script = "\n$('#" . $this->getId() . "').datepicker({\n";
        $script .= "\tchangeMonth: true,\n";
        $script .= "\tchangeYear: true,\n";
        $script .= "\tdateFormat: '" . $dateFormat . "'\n";
        $script .= "});\n";

        return $script;
    }

}