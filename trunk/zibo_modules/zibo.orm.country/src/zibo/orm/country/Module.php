<?php

namespace zibo\orm\country;

use zibo\library\i18n\I18n;
use zibo\library\orm\model\ContinentModel;
use zibo\library\orm\model\CountryModel;
use zibo\library\orm\ModelManager;

/**
 * Installer for the Continent/Country model of the ORM module
 */
class Module {

    /**
     * Installs or updates the continents and countries from the data directory into the model.
     * @return null
     */
    public function install() {
        $modelManager = ModelManager::getInstance();
        $continentModel = $modelManager->getModel(ContinentModel::NAME);
        $countryModel = $modelManager->getModel(CountryModel::NAME);

        $locales = I18n::getInstance()->getLocaleCodeList();

        $continents = $continentModel->installContinents($locales);
        $countryModel->installCountries($locales, $continents);
    }

}