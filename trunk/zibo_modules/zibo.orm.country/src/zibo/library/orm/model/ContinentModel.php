<?php

namespace zibo\library\orm\model;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Continent model
 */
class ContinentModel extends SimpleModel {

    /**
     * Name of this model
     * @var string
     */
    const NAME = 'Continent';

    /**
     * Configuration key for the path to the data directory of the continents
     * @var string
     */
    const CONFIG_PATH_DATA = 'orm.path.continents';

    /**
     * Gets a data list of continents
     * @param string $locale
     * @return array Array with the id of a continent as key and the name as value
     */
    public function getDataList($locale = null) {
        $query = $this->createQuery(0, $locale, ModelQuery::INCLUDE_UNLOCALIZED_FETCH);
        $query->addOrderBy('{name} ASC');

        $list = array();

        $continents = $query->query();
        foreach ($continents as $continent) {
            $list[$continent->id] = $continent->name;
        }

        return $list;
    }

    /**
     * Gets all the continents
     * @param integer $recursiveDepth
     * @param string $locale
     * @param boolean $includeUnlocalized
     * @return array
     */
    public function getContinents($recursiveDepth = 0, $locale = null, $includeUnlocalized = true) {
        $query = $this->createQuery($recursiveDepth, $locale, $includeUnlocalized);
        $query->addOrderBy('{name} ASC');
        return $query->query();
    }

    /**
     * Installs the continents from the data directory. The data directory is defined in the orm.path.continents configuration
     * @param array $locales Array with locale codes
     * @return array Array with the continent code as key and the continent data as value
     */
    public function installContinents(array $locales) {
        $path = $this->getDataPath();

        $query = $this->createQuery(0);
        $continents = $query->query('code');

        $transactionStarted = $this->startTransaction();
        try {
            foreach ($locales as $locale) {
                $file = new File($path, $locale . '.ini');
                if (!$file->exists()) {
                    continue;
                }

                $continentNames = parse_ini_file($file->getPath(), false, INI_SCANNER_RAW);

                foreach ($continentNames as $continentCode => $continentName) {
                    if (array_key_exists($continentCode, $continents)) {
                        $continent = $continents[$continentCode];
                    } else {
                        $continent = $this->createData();
                        $continent->code = $continentCode;

                        $continents[$continentCode] = $continent;
                    }

                    $continent->name = $continentName;
                    $continent->dataLocale = $locale;

                    $this->save($continent);
                }
            }


            $this->commitTransaction($transactionStarted);
        } catch (Exception $e) {
            $this->rollbackTransaction($transactionStarted);

            throw $e;
        }

        return $continents;
    }

    /**
     * Gets the path of the continent data from the Zibo configuration
     * @return string
     * @throws zibo\ZiboException when no valid path was found in the configuration
     */
    private function getDataPath() {
        $path = Zibo::getInstance()->getConfigValue(self::CONFIG_PATH_DATA);
        if (String::isEmpty($path)) {
            throw new ZiboException('No valid data path found in configuration ' . self::CONFIG_PATH_DATA);
        }

        return $path;
    }

}