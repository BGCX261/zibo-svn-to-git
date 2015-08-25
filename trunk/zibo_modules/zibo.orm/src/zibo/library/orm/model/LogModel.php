<?php

namespace zibo\library\orm\model;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;
use zibo\library\orm\definition\field\extended\DateAddedField;
use zibo\library\orm\definition\field\extended\VersionField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\ModelManager;
use zibo\library\orm\query\ModelQuery;
use zibo\library\security\SecurityManager;
use zibo\library\String;
use zibo\library\Structure;

use zibo\ZiboException;

use \Exception;

/**
 * Model for logging model actions
 */
class LogModel extends ExtendedModel {

    /**
     * Name of the log model
     * @var string
     */
    const NAME = 'ModelLog';

    /**
     * Name of the insert action
     * @var string
     */
    const ACTION_INSERT = 'insert';

    /**
     * Name of the update action
     * @var string
     */
    const ACTION_UPDATE = 'update';

    /**
     * Name of the delete action
     * @var string
     */
    const ACTION_DELETE = 'delete';

    /**
     * Separator between log values
     * @var string
     */
    const VALUE_SEPARATOR = ', ';

    /**
     * Hook to perform extra initialization when constructing the model
     * @return null
     */
    protected function init() {
        $this->addAutomaticField(new DateAddedField($this));
    }

    /**
     * Gets the data object as it was on the provided date
     * @param string $modelName Name of the data model
     * @param int $id Primary key of the data
     * @param int $date Timestamp of the date
     * @return mixed Data
     */
    public function getDataByDate($modelName, $id, $date, $recursiveDepth = 1, $locale = null) {
        $query = $this->createQuery(1, $locale);
        $query->addCondition('{dataModel} = %1% AND {dataId} = %2%', $modelName, $id);
        $query->addCondition('{dateAdded} <= %1%', $date);
        $query->addOrderBy('{dataVersion} ASC');

        return $this->getDataByQuery($modelName, $id, $query, $recursiveDepth);
    }

    /**
     * Gets the data object as it was at the provided version
     * @param string $modelName Name of the data model
     * @param int $id Primary key of the data
     * @param int $version Previous version of the data
     * @return mixed Data
     */
    public function getDataByVersion($modelName, $id, $version, $recursiveDepth = 1, $locale = null) {
        $query = $this->createQuery(1, $locale);
        $query->addCondition('{dataModel} = %1% AND {dataId} = %2%', $modelName, $id);
        $query->addCondition('{dataVersion} <= %1%', $version);
        $query->addOrderBy('{dataVersion} ASC');

        return $this->getDataByQuery($modelName, $id, $query, $recursiveDepth);
    }

    /**
     * Gets the data object for the logs in the provided query
     * @param string $modelName Name of the data model
     * @param int $id Primary key of the data
     * @param zibo\library\orm\query\ModelQuery $query of the data logs
     * @return mixed Data
     */
    protected function getDataByQuery($modelName, $id, ModelQuery $query, $recursiveDepth) {
        $dataModel = $this->getModel($modelName);
        $dataMeta = $dataModel->getMeta();

        if (!$dataMeta->isLogged()) {
            return $dataModel->findById($id);
        }

        $logs = $query->query();

        if (!$logs) {
            throw new ZiboException('No logs for ' . $modelName . ' ' . $id);
        }

        $dataDate = 0;
        $dataFields = array();
        foreach ($logs as $log) {
            foreach ($log->changes as $change) {
                $dataFields[$change->fieldName] = $change->newValue;
            }

            $dataDate = $log->dateAdded;
        }

        $data = $dataModel->createData(false);
        $data->id = $id;

        foreach ($dataFields as $fieldName => $value) {
            $data->$fieldName = $value;

            if (!$dataMeta->hasField($fieldName)) {
                continue;
            }

            $field = $dataMeta->getField($fieldName);
            if (!($field instanceof RelationField)) {
                continue;
            }

            $fieldModelName = $field->getRelationModelName();

            if ($field instanceof HasManyField) {
                if (!$data->$fieldName) {
                    $data->$fieldName = array();
                } else {
                    $ids = explode(self::VALUE_SEPARATOR, $data->$fieldName);

                    $values = array();
                    foreach ($ids as $id) {
                        $id = trim($id);

                        if (!$id) {
                            continue;
                        }

                        if ($recursiveDepth) {
                            $values[$id] = $this->getDataByDate($fieldModelName, $id, $dataDate, $recursiveDepth - 1);
                        } else {
                            $values[$id] = $id;
                        }
                    }

                    $data->$fieldName = $values;
                }
                continue;
            }

            if (!$data->$fieldName) {
                $data->$fieldName = null;
            } else {
                if ($recursiveDepth) {
                    $data->$fieldName = $this->getDataByDate($fieldModelName, $data->$fieldName, $dataDate, $recursiveDepth - 1);
                }
            }
        }

        if ($dataMeta->isLocalized()) {
            $locale = $query->getLocale();

            $localizedModel = $dataMeta->getLocalizedModel();
            $localizedId = $localizedModel->getLocalizedId($data->id, $locale);

            if ($localizedId) {
                try {
                    $localizedData = $this->getDataByDate($localizedModel->getName(), $localizedId, $dataDate, $recursiveDepth);

                    $localizedFields = $dataMeta->getLocalizedFields();
                    foreach ($localizedFields as $fieldName => $field) {
                        if (isset($localizedData->$fieldName)) {
                            $data->$fieldName = $localizedData->$fieldName;
                        }
                    }

                    $data->dataLocale = $locale;
                } catch (Exception $exception) {
                    Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
                }
            }
        }

        return $data;
    }

    /**
     * Gets the changes of the provided model
     * @param string $modelName Name of the data model
     * @param int $id Primary key of the data
     * @param int $version
     * @param string $locale
     * @param int $since
     * @return array
     */
    public function getChanges($modelName, $id = null, $version = null, $locale = null, $since = null) {
        $manager = ModelManager::getInstance();
        $model = $manager->getModel($modelName);
        $meta = $model->getMeta();

        $query = $this->createQuery();
        $query->setOperator('OR');

        $condition = '';

        if ($since) {
            $condition .= '{dateAdded} >= %4%';
        }

        $condition .= ($condition ? ' AND ' : '') . '{dataModel} = %1%';

        if ($id) {
            $condition .= ' AND {dataId} = %2%';
        }
        if ($version) {
            $condition .= ' AND {dataVersion} = %3%';
        }

        $query->addCondition($condition, $modelName, $id, $version, $since);

        if ($meta->isLocalized()) {
            if ($locale == null) {
                $locale = Locale::getLocale();
            }

            $localizedModel = $meta->getLocalizedModel();
            $localizedId = null;

            $condition = '{dataModel} = %1%';

            if ($id) {
                $localizedId = $localizedModel->getLocalizedId($id, $locale);
                $condition .= ' AND {dataId} = %2%';
            }

            $query->addCondition($condition, $localizedModel->getName(), $localizedId);
        }

        $query->addOrderBy('{dateAdded} DESC');
        $query->addOrderBy('{dataVersion} DESC');

        return $query->query();
    }

    /**
     * Gets the log for a data object
     * @param string $modelName Name of the data model
     * @param integer $id Primary key of the data
     * @param integer $version If provided, the log of this version will be retrieved, else all logs
     * @param string $locale
     * @return array Array with LogData objects
     */
    public function getLog($modelName, $id, $version = null, $locale = null) {
        $logs = $this->getChanges($modelName, $id, $version, $locale);

        $logs = array_reverse($logs);

        $versions = array();
        foreach ($logs as $log) {
            $id = $log->dateAdded;

            if (!$log->changes && $log->dataModel != $modelName) {
                continue;
            }

            if ($log->dataModel != $modelName) {
                if (!isset($versions[$id])) {
                    if (isset($versions[$id - 1])) {
                        $versions[$id - 1]->changes = Structure::merge($versions[$id - 1]->changes, $log->changes);
                        continue;
                    } else {
                        $versions[$id] = $log;
                    }
                }
            } elseif (!isset($versions[$id])) {
                $versions[$id] = $log;
                continue;
            }

            if ($versions[$id]->dataModel != $modelName && $log->dataModel == $modelName) {
                $versions[$id]->dataModel = $modelName;
                $versions[$id]->dataId = $log->dataId;
                $versions[$id]->dataVersion = $log->dataVersion;
            }

            $versions[$id]->changes = Structure::merge($versions[$id]->changes, $log->changes);
        }

        foreach ($versions as $id => $version) {
            $changes = $version->changes;
            foreach ($changes as $index => $change) {
                if ($change->fieldName == VersionField::NAME || $change->fieldName == LocalizedModel::FIELD_LOCALE || $change->fieldName == LocalizedModel::FIELD_DATA) {
                    unset($changes[$index]);
                }
            }

            $version->changes = $changes;

            $versions[$id] = $version;
        }

        return array_reverse($versions);
    }

    /**
     * Logs a insert action for the provided data
     * @param string $modelName Name of the data model
     * @param mixed $data New data object
     * @param array $newValues Array with the field name as key and the new value as value
     * @return null
     */
    public function logInsert($modelName, $data, $newValues) {
        $log = $this->createLog($modelName, $data, true);
        $log->action = self::ACTION_INSERT;

        $logChangeModel = $this->getModel(LogChangeModel::NAME);

        foreach ($newValues as $fieldName => $newValue) {
            $change = $logChangeModel->createData();
            $change->fieldName = $fieldName;
            $change->newValue = $this->createLogValue($newValue);

            $log->changes[] = $change;
        }

        $this->save($log);
    }

    /**
     * Logs a update action for the provided data
     * @param string $modelName Name of the data model
     * @param mixed $data Updated data object
     * @param array $newValues Array with the field name as key and the new value as value
     * @param array $oldData Current data object from the model
     * @return null
     */
    public function logUpdate($modelName, $data, $newValues, $oldData) {
        $log = $this->createLog($modelName, $data);
        $log->action = self::ACTION_UPDATE;

        $logChangeModel = $this->getModel(LogChangeModel::NAME);

        foreach ($newValues as $fieldName => $newValue) {
            if (isset($oldData->$fieldName)) {
                $oldValue = $this->createLogValue($oldData->$fieldName);
            } else {
                $oldValue = null;
            }

            $newValue = $this->createLogValue($newValue);

            if ($oldValue == $newValue || (!$oldValue && !$newValue)) {
                continue;
            }

            $change = $logChangeModel->createData();
            $change->fieldName = $fieldName;
            $change->oldValue = $oldValue;
            $change->newValue = $newValue;

            $log->changes[] = $change;
        }

        $this->save($log);
    }

    /**
     * Logs a delete action for the provided data
     * @param string $modelName Name of the data model
     * @param mixed $data Data object
     * @return null
     */
    public function logDelete($modelName, $data) {
        $log = $this->createLog($modelName, $data);
        $log->action = self::ACTION_DELETE;

        $this->save($log);
    }

    /**
     * Creates a log data object based on the provided data
     * @param string $modelName Name of the data model
     * @param mixed $data Data object
     * @param boolean $isNew Flag to see if this is a new data object
     * @return mixed Log data object
     */
    private function createLog($modelName, $data, $isNew = false) {
        $primaryKey = ModelTable::PRIMARY_KEY;
        $versionField = VersionField::NAME;

        $log = $this->createData();

        $log->dataModel = $modelName;
        $log->dataId = $data->$primaryKey;

        if (isset($data->$versionField)) {
            $log->dataVersion = $data->$versionField;
        } elseif ($isNew) {
            $log->dataVersion = 0;
        } else {
            $log->dataVersion = $this->getNewVersionForData($modelName, $data->$primaryKey);
        }

        $user = SecurityManager::getInstance()->getUser();
        if ($user != null) {
            $user = $user->getUserName();
        }
        $log->user = $user;

        return $log;
    }

    /**
     * Creates a log value from a model value.
     *
     * Related data objects will be replaced by their id's. A hasMany relation will be translated into a
     * string with the related object id's separated by the VALUE_SEPARATOR constant.
     * @param mixed $value
     * @return string Provided value in a string format
     */
    private function createLogValue($value) {
        if (is_null($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return $value;
        }

        if (is_object($value)) {
            $primaryKey = ModelTable::PRIMARY_KEY;
            return $value->$primaryKey;
        }

        $logValues = array();
        foreach ($value as $v) {
            $logValues[] = $this->createLogValue($v);
        }

        sort($logValues);

        return implode(self::VALUE_SEPARATOR, $logValues);
    }

    /**
     * Gets a new version for a data object
     * @param string $modelName Name of the data model
     * @param int $id Primary key of the data
     * @return int New version for the data object
     */
    private function getNewVersionForData($modelName, $id) {
        $query = $this->createQuery(0);
        $query->setFields('{dataVersion}');
        $query->addCondition('{dataModel} = %1% AND {dataId} = %2%', $modelName, $id);
        $query->addOrderBy('{dataVersion} DESC');

        $lastLog = $query->queryFirst();

        if (!$lastLog) {
            return 0;
        }
        return $lastLog->dataVersion + 1;
    }

}