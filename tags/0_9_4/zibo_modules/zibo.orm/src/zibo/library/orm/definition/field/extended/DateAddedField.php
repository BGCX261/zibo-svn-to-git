<?php

namespace zibo\library\orm\definition\field\extended;

/**
 * Automatic field to keep the date it was created
 */
class DateAddedField extends AbstractAutomaticField {

    /**
     * Name of the added date field
     * @var string
     */
    const NAME = 'dateAdded';

    /**
     * Get the name of this field
     * @return string
     */
    public function getName() {
        return self::NAME;
    }

    /**
     * Set the date added value to the data object when the object is a new one
     * @param mixed $data data object of the model which is to be saved
     * @return null
     */
    public function processSaveData($data) {
        if (!empty($data->id)) {
            return;
        }

        if (!empty($data->dateAdded)) {
            return;
        }

        $data->dateAdded = time();
    }

}