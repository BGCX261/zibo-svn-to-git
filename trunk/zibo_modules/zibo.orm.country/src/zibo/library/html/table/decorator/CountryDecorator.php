<?php

namespace zibo\library\html\table\decorator;

use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;

use zibo\library\orm\model\data\CountryData;
use zibo\library\orm\ModelManager;

use zibo\orm\country\Module;

/**
 * Table decorator to decorate country values
 */
class CountryDecorator extends ValueDecorator {

    /**
     * The country model to lookup countries if needed
     * @var zibo\library\orm\model\CountryModel
     */
    private $model;

    /**
     * Retrieves the country of the value and gets the name of it. When the provided value is a number,
     * the country will be looked up by id. When the provided value is a string, the country will
     * be looked up by code.
     * @param mixed $value The value to decorate
     * @return null|string The name of the country if the country could be retrieved from the value,
     * null otherwise
     */
    protected function decorateValue($value) {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            $model = $this->getModel();
            $country = $model->findById($value, false);
        } elseif (is_string($value)) {
            $model = $this->getModel();
            $country = $model->findBy('code', strtoupper($value), false);
        } else {
            $country = $value;
        }

        if (!$country || !($country instanceof CountryData)) {
            return null;
        }

        return $country->name;
    }

    /**
     * Gets the country model
     * @return zibo\library\orm\model\CountryModel
     */
    private function getCountryModel() {
        if (!$this->model) {
            $this->model = ModelManager::getInstance()->getModel(Module::MODEL_COUNTRY);
        }

        return $this->model;
    }

}