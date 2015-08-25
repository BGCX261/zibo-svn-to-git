<?php

namespace zibo\library\html\form\field;

use zibo\library\orm\model\ContinentModel;
use zibo\library\orm\ModelManager;

/**
 * Form field to select a continent
 */
class ContinentField extends ListField {

    /**
     * Initializes this field, fetches the continents and sets them to this field
     * @return null
     */
    protected function init() {
        parent::init();

        $continentModel = ModelManager::getInstance()->getModel(ContinentModel::NAME);
        $continents = $continentModel->getDataList();

        $this->setOptions($continents);
    }

}