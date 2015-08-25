<?php

namespace zibo\library\orm\model;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\String;

use zibo\ZiboException;

use \Exception;

/**
 * Country model
 */
class CountryModel extends SimpleModel {

    /**
     * Name of this model
     * @var string
     */
    const NAME = 'Country';

    /**
     * Configuration key for the path to the data directory of the countries
     * @var string
     */
    const CONFIG_PATH_DATA = 'orm.path.countries';

    /**
     * Gets all the countries
     * @param boolean $recursive
     * @param string $locale
     * @param boolean $includeUnlocalized
     * @return array
     */
    public function getCountries($recursive = 0, $locale = null, $includeUnlocalized = true) {
        $query = $this->createQuery($recursive, $locale, $includeUnlocalized);
        $query->addOrderBy('{name} ASC');
        return $query->query();
    }

    /**
     * Installs the countries from the data directory. The data directory is defined in the orm.path.countries configuration
     * @param array $locales Array with locale codes
     * @param array $continents Array with the installed continents
     * @return null
     */
    public function installCountries(array $locales, array $continents) {
        $path = $this->getDataPath();

        $continentCountryCodes = $this->getContinentCountryCodes();

        $query = $this->createQuery(0);
        $countries = $query->query('code');

        $transactionStarted = $this->startTransaction();
        try {
            foreach ($locales as $locale) {
                $file = new File($path, $locale . '.ini');
                if (!$file->exists()) {
                    continue;
                }

                $countryNames = $this->readCountries($file);

                foreach ($countryNames as $countryCode => $countryName) {
                    if (array_key_exists($countryCode, $countries)) {
                        $country = $countries[$countryCode];
                    } else {
                        $country = $this->createData();
                        $country->code = $countryCode;
                        $country->continent = $this->getContinentForCountry($countryCode, $continents, $continentCountryCodes);

                        $countries[$countryCode] = $country;
                    }

                    $country->name = $countryName;
                    $country->dataLocale = $locale;

                    $this->save($country);
                }
            }

            $this->commitTransaction($transactionStarted);
        } catch (Exception $e) {
            $this->rollbackTransaction($transactionStarted);

            throw $e;
        }
    }

    /**
     * Gets the id of the continent for the provided country
     * @param string $countryCode Code of the country
     * @param array $continents Array with the continent code as key and continent data objects as value
     * @param array $continentCountryCodes Array with the continent code as key and an array with country codes as value
     * @return integer The primary key of the continent if found, 0 otherwise
     */
    private function getContinentForCountry($countryCode, array $continents, array $continentCountryCodes) {
        foreach ($continentCountryCodes as $continentCode => $continentCountries) {
            if (in_array($countryCode, $continentCountries) && isset($continents[$continentCode])) {
                return $continents[$continentCode]->id;
            }
        }

        return null;
    }

    /**
     * Reads the countries from the provided file
     * @param zibo\library\filesystem\File $file
     * @return array Array with the country code as key and the name as value
     */
    private function readCountries(File $file) {
        $countries = array();

        $content = $file->read();

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line) {
                continue;
            }

            list($code, $name) = explode('=', $line, 2);

            $code = trim($code);
            $name = trim($name);

            $name = ltrim(rtrim($name, '"'), '"');

            $countries[$code] = $name;
        }

        return $countries;
    }

    /**
     * Gets the path of the country data from the Zibo configuration
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

    /**
     * Gets an array with an overview of the continents and their countries
     * @return array Array with the code of the continent as key and an array with the codes of
     *               the countries as value
     */
    private function getContinentCountryCodes() {
        return array(
            'AFRICA' => array (
                'AO', 'BF', 'BI', 'BJ', 'BW', 'CF', 'CG', 'CI', 'CM', 'CV', 'DJ', 'DZ', 'EG', 'EH',
                'ER', 'ET', 'GA', 'GH', 'GM', 'GN', 'GQ', 'GW', 'KE', 'KM', 'LS', 'LR', 'LY', 'MA',
                'ML', 'MG', 'MR', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'RE', 'SL', 'ST', 'RW', 'SC',
                'SD', 'SH', 'SN', 'SO', 'SZ', 'TD', 'TG', 'TN', 'TZ', 'UG', 'YT', 'ZA', 'ZM', 'ZR',
                'ZW',
            ),
            'ANTARCTICA' => array (
                'AQ', 'BV', 'GS', 'HM', 'TF',
            ),
            'ASIA' => array (
                'AE', 'AF', 'BD', 'BH', 'BN', 'BT', 'CN', 'HK', 'ID', 'IL', 'IN', 'IO', 'IQ', 'IR',
                'JO', 'JP', 'KG', 'KH', 'KP', 'KR', 'KW', 'KZ', 'LA', 'LB', 'LK', 'MM', 'MN', 'MO',
                'MV', 'MY', 'NP', 'OM', 'PH', 'PK', 'QA', 'SA', 'SG', 'SU', 'SY', 'TH', 'TJ', 'TM',
                'TP', 'TW', 'UZ', 'VN', 'YE',
            ),
            'EUROPE' => array (
                'AD', 'AL', 'AM', 'AT', 'AZ', 'BA', 'BE', 'BG', 'BY', 'CH', 'CS', 'CY', 'CZ', 'DE',
                'DK', 'EE', 'ES', 'FI', 'FO', 'FR', 'FX', 'GB', 'GE', 'GI', 'GR', 'HR', 'HU', 'IE',
                'IS', 'IT', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'MK', 'MT', 'NL', 'NO', 'PL', 'PT',
                'RO', 'RU', 'SE', 'SJ', 'SK', 'SM', 'SI', 'TR', 'UA', 'UK', 'VA', 'YU',
            ),
            'NORTH_AMERICA' => array (
                'AG', 'AI', 'AN', 'AW', 'BB', 'BM', 'BS', 'BZ', 'CA', 'CR', 'CU', 'DM', 'DO', 'GD',
                'GL', 'GP', 'GT', 'HN', 'HT', 'JM', 'KN', 'KY', 'LC', 'MQ', 'MS', 'MX', 'NI', 'PA',
                'PM', 'PR', 'TT', 'TC', 'SV', 'UM', 'US', 'VC', 'VG', 'VI',
            ),
            'OCEANIA' => array (
                'AS', 'AU', 'CC', 'CK', 'CX', 'FJ', 'FM', 'GU', 'KI', 'MH', 'MP', 'NC', 'NF', 'NR',
                'NU', 'NZ', 'PW', 'PF', 'PG', 'PN', 'SB', 'TK', 'TO', 'TV', 'VU', 'WF', 'WS',
            ),
            'SOUTH_AMERICA' => array (
                'AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'PE', 'PY', 'SR', 'UY', 'VE',
            ),
        );
    }

}