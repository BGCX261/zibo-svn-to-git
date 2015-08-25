<?php

namespace zibo\library\orm;

use zibo\library\database\DatabaseManager;
use zibo\library\orm\model\Model;

use \Exception;

/**
 * Definer of models in the database
 */
class ModelDefiner {

    /**
     * Definer for database tables
     * @var zibo\library\database\definition\definer\Definer
     */
    private $databaseDefiner;

    /**
     * Gets a list of unused tables
     * @param array $models Array with the used models
     * @return array Array with table names
     */
    public function getUnusedTables(array $models) {
        $connection = DatabaseManager::getInstance()->getConnection();
        $definer = $connection->getDefiner();

        $tables = $definer->getTableList();

        foreach ($tables as $index => $table) {
            if (array_key_exists($table, $models)) {
                unset($tables[$index]);
            }
        }

        return $tables;
    }

    /**
     * Creates or alters the tables in the database of the provided models
     * @param array $models Array with Model objects
     * @return null
     */
    public function defineModels(array $models) {
        $connection = DatabaseManager::getInstance()->getConnection();
        $definer = $connection->getDefiner();

        $isTransactionStarted = $connection->startTransaction();
        try {
            $tables = array();

            foreach ($models as $model) {
                $table = $this->getDatabaseTable($model);

                $definer->defineTable($table);

                $tables[] = $table;
            }

            foreach ($tables as $table) {
                $definer->defineForeignKeys($table);
            }

            $connection->commitTransaction($isTransactionStarted);
        } catch (Exception $exception) {
            $connection->rollbackTransaction($isTransactionStarted);
            throw $exception;
        }
    }

    /**
     * Gets the database table definition of the provided model
     * @param zibo\library\orm\model\Model $model
     * @return zibo\library\database\definition\Table
     */
    private function getDatabaseTable(Model $model) {
        $meta = $model->getMeta();

        $modelTable = $meta->getModelTable();

        $databaseTable = $modelTable->getDatabaseTable();

        return $databaseTable;
    }

}