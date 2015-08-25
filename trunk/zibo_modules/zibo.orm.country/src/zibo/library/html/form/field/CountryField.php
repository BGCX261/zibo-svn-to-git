<?php

namespace zibo\library\html\form\field;

use zibo\library\html\form\field\decorator\CountryNameDecorator;
use zibo\library\orm\model\ContinentModel;
use zibo\library\orm\model\CountryModel;
use zibo\library\orm\ModelManager;
use zibo\library\Structure;

use zibo\orm\country\Module;
use zibo\orm\scaffold\form\field\decorator\IdDecorator;

/**
 * Form field to select a country
 */
class CountryField extends ListField {

    /**
     * Initializes this field, fetches the countries and sets them to this field
     * @return null
     */
    protected function init() {
        parent::init();

        $model = ModelManager::getInstance()->getModel(ContinentModel::NAME);
        $continents = $model->getContinents(1);

        foreach ($continents as $continent) {
            $this->setOptions($continent->countries, $continent->name);
        }

        $this->setKeyDecorator(new IdDecorator());
        $this->setValueDecorator(new CountryNameDecorator());
    }

    /**
     * Add a empty value to the option
     * @param string $value label of the option
     * @param string $key key of the option
     * @return null
     */
    public function addEmpty($value = '---', $key = 0) {
        $model = ModelManager::getInstance()->getModel(CountryModel::NAME);

        $country = $model->createData(false);
        $country->id = $key;
        $country->name = $value;

        $this->options = Structure::merge(array('---' => array($key => $country)), $this->options);
    }

}