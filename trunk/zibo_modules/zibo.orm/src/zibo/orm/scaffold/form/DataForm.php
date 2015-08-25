<?php

namespace zibo\orm\scaffold\form;

use zibo\library\html\form\SubmitCancelForm;

/**
 * Generic form for model data
 */
class DataForm extends SubmitCancelForm {

    /**
     * Translation key for the save button
     * @var string
     */
    const TRANSLATION_SAVE = 'button.save';

    /**
     * Data object
     * @var mixed
     */
    protected $data;

    /**
     * Constructs a new data form
     * @param string $action URL where this form will point to
     * @param string $name Name of the form
     * @param mixed $data Data object to preset the form
     * @return null
     */
    public function __construct($action, $name, $data) {
        parent::__construct($action, $name, self::TRANSLATION_SAVE);

        $this->data = $data;
    }

    /**
     * Gets the data object of this form
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Sets the submitted values to the provided data object
     * @return null
     * @throws zibo\ZiboException when the form is not submitted
     */
    public function isSubmitted() {
        if ($this->isSubmitted !== null) {
            return $this->isSubmitted;
        }

        if (!parent::isSubmitted()) {
            return false;
        }

        $this->setFormValuesToData();

        return true;
    }

    /**
     * Sets the fields of the submitted form to the data object
     * @return null
     */
    protected function setFormValuesToData() {

    }

}