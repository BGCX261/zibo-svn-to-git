<?php

namespace zibo\spider\form;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\SubmitCancelForm;
use zibo\library\validation\validator\MinMaxValidator;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to ask for the URL to crawl, advanced settings included
 */
class SpiderForm extends SubmitCancelForm {

    /**
     * Name of the form
     * @var string
     */
    const NAME = 'formSpider';

    /**
     * Name of the URL field
     * @var string
     */
    const FIELD_URL = 'url';

    /**
     * Name of the delay field
     * @var string
     */
    const FIELD_DELAY = 'delay';

    /**
     * Name of the ignore field
     * @var string
     */
    const FIELD_IGNORE = 'ignore';

    /**
     * Translation for the submit button
     * @var string
     */
    const TRANSLATION_SUBMIT = 'spider.button.crawl';

    /**
     * Translation for the cancel button
     * @var string
     */
    const TRANSLATION_CANCEL = 'spider.button.stop';

    /**
     * Constructs a new spider form
     * @param string $action URL where this form will point to
     * @param string $url URL to set in the form
     * @return null
     */
    public function __construct($action, $url = null, $delay = 100) {
        parent::__construct($action, self::NAME, self::TRANSLATION_SUBMIT, self::TRANSLATION_CANCEL);

        $fieldFactory = FieldFactory::getInstance();

        $urlField = $fieldFactory->createField(FieldFactory::TYPE_WEBSITE, self::FIELD_URL, $url);
        $urlField->addValidator(new RequiredValidator());

        $delayField = $fieldFactory->createField(FieldFactory::TYPE_STRING, self::FIELD_DELAY, $delay);
        $delayField->addValidator(new MinMaxValidator(array(MinMaxValidator::OPTION_MINIMUM => 0, MinMaxValidator::OPTION_MAXIMUM => 999999)));

        $ignoreField = $fieldFactory->createField(FieldFactory::TYPE_TEXT, self::FIELD_IGNORE);

        $this->addField($urlField);
        $this->addField($delayField);
        $this->addField($ignoreField);
    }

    /**
     * Gets the URL of this form
     * @return string
     */
    public function getUrl() {
        return $this->getValue(self::FIELD_URL);
    }

    /**
     * Sets the provided URL to this form
     * @param string $url
     * @return null
     */
    public function setUrl($url) {
        $this->setValue(self::FIELD_URL, $url);
    }

    /**
     * Gets the delay of this form
     * @return float
     */
    public function getDelay() {
        return $this->getValue(self::FIELD_DELAY);
    }

    /**
     * Sets the provided delay to this form
     * @param float $delay
     * @return null
     */
    public function setDelay($delay) {
        $this->setValue(self::FIELD_DELAY, $delay);
    }

    /**
     * Gets the ignore regular expressions of this form
     * @return string
     */
    public function getIgnore() {
        return $this->getValue(self::FIELD_IGNORE);
    }

    /**
     * Sets the provided ignore regular expressions to this form
     * @param string $ignore A regular expression per line
     * @return null
     */
    public function setIgnore($ignore) {
        $this->setValue(self::FIELD_IGNORE, $ignore);
    }

}