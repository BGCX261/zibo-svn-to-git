<?php

namespace zibo\library\orm\model;

use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\exception\OrmException;
use zibo\library\String;

/**
 * Model for the localized fields of a model
 */
class LocalizedModel extends SimpleModel {

    /**
     * Suffix for the localized models
     * @var string
     */
    const MODEL_SUFFIX = 'Localized';

    /**
     * Field name of the unlocalized data
     * @var string
     */
    const FIELD_DATA = 'dataId';

    /**
     * Field name of the locale field
     * @var string
     */
    const FIELD_LOCALE = 'dataLocale';

    /**
     * Saves the localized data
     * @param mixed $data Localized data object
     * @return null
     */
    protected function saveData($data) {
        if (!empty($data->id)) {
            parent::saveData($data);
        }

        $fieldData = self::FIELD_DATA;
        $fieldLocale = self::FIELD_LOCALE;

        $data->id = $this->getLocalizedId($data->$fieldData, $data->$fieldLocale);

        parent::saveData($data);
    }

    /**
     * Gets the id of the localized data
     * @param integer $id Primary key of the unlocalized data
     * @param string $locale Locale code of the localized data
     * @return null|integer The primary key of the localized data if found, null otherwise
     */
    public function getLocalizedId($id, $locale) {
        $query = $this->createLocalizedQuery($id, $locale, 0);
        $query->setFields('{' . ModelTable::PRIMARY_KEY . '}');

        $data = $query->queryFirst();

        if ($data != null) {
            return $data->id;
        }

        return null;
    }

    /**
     * Gets the ids of the localized data
     * @param integer $id Primary key of the unlocalized data
     * @return array Array with the locale code as key and the primary key of the localized data as value
     */
    public function getLocalizedIds($id) {
        $query = $this->createQuery(0);
        $query->setFields('{' . ModelTable::PRIMARY_KEY . '}, {' . self::FIELD_LOCALE . '}');
        $query->addCondition('{' . self::FIELD_DATA . '} = %1%', $id);

        $result = $query->query();

        $ids = array();
        foreach ($result as $data) {
            $ids[$data->dataLocale] = $data->id;
        }

        return $ids;
    }

    /**
     * Gets the localized data
     * @param integer $id Primary key of the unlocalized data
     * @param string $locale Locale code of the localized data
     * @param integer $recursiveDepth Depth for the recursive relations
     * @return null|mixed The localized data if found, null otherwise
     */
    public function getLocalizedData($id, $locale, $recursiveDepth = 1, $fields = null) {
        $query = $this->createLocalizedQuery($id, $locale, $recursiveDepth);

        if ($fields) {
            $query->setFields($fields);
        }

        return $query->queryFirst();
    }

    /**
     * Deletes the localized data
     * @param integer $id Primary key of the unlocalized data
     * @return null
     * @throws zibo\ZiboException when the provided id is empty or invalid
     */
    public function deleteLocalizedData($id) {
        if (String::isEmpty($id)) {
            throw new OrmException('Provided id is empty');
        }

        $query = $this->createQuery(0);
        $query->setFields('{' . ModelTable::PRIMARY_KEY . '}');
        $query->addCondition('{' . self::FIELD_DATA . '} = %1%', $id);

        $result = $query->query();

        $this->delete($result);
    }

    /**
     * Creates a query for a specific data object and locale
     * @param integer $id Primary key of the data
     * @param string $locale Locale code for the localized data
     * @param integer $recursiveDepth Depth for the recursive relations
     * @return zibo\library\orm\query\ModelQuery
     * @throws zibo\ZiboException when the id is empty or invalid
     * @throws zibo\ZiboException when the locale is empty or invalid
     */
    private function createLocalizedQuery($id, $locale, $recursiveDepth = 1) {
        if (String::isEmpty($id)) {
            throw new OrmException('Provided id is empty');
        }
        if (String::isEmpty($locale)) {
            throw new OrmException('Provided locale code is empty');
        }

        $query = $this->createQuery($recursiveDepth);
        $query->addCondition('{' . self::FIELD_DATA . '} = %1% AND {' . self::FIELD_LOCALE . '} = %2%', $id, $locale);

        return $query;
    }

}