<?php

namespace zibo\library\orm\definition\field\extended;

use zibo\library\database\manipulation\condition\SimpleCondition;
use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\ScalarExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\statement\UpdateStatement;
use zibo\library\orm\definition\ModelTable;

/**
 * Automatic field to keep the date when the data was updated
 */
class DateModifiedField extends AbstractAutomaticField {

    /**
     * Name of the modified date field
     * @var string
     */
    const NAME = 'dateModified';

    /**
     * Get the name of this field
     * @return string
     */
    public function getName() {
        return self::NAME;
    }

    /**
     * Save the date modified field
     * @param mixed $data the model data which is being saved
     * @param string $fieldName name of the field which has to be saved
     * @param int $id primary key to the data
     * @return null
     */
    public function processSaveField($data, $fieldName, $id) {
        if (!$id) {
            $id = $data->id;
        }

        $condition = new SimpleCondition(new FieldExpression(ModelTable::PRIMARY_KEY), new ScalarExpression($id));

        $statement = new UpdateStatement();
        $statement->addTable(new TableExpression($this->model->getName()));
        $statement->addValue(new FieldExpression(self::NAME), new ScalarExpression(time()));
        $statement->addCondition($condition);

        $connection = $this->model->getMeta()->getConnection();
        $connection->executeStatement($statement);

        $this->model->clearCache();
    }

    /**
     * Save the model data, update the modified date field when the data is an existing data object
     * @param mixed $data data object of the model which
     * @return null
     */
    public function processSaveData($data) {
        if (!$data->id) {
            return;
        }

        $data->dateModified = time();
    }

}