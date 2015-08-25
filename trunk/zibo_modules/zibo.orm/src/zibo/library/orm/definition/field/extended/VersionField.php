<?php

namespace zibo\library\orm\definition\field\extended;

use zibo\library\database\manipulation\condition\SimpleCondition;
use zibo\library\database\manipulation\expression\FieldExpression;
use zibo\library\database\manipulation\expression\MathematicalExpression;
use zibo\library\database\manipulation\expression\ScalarExpression;
use zibo\library\database\manipulation\expression\TableExpression;
use zibo\library\database\manipulation\statement\UpdateStatement;
use zibo\library\orm\definition\ModelTable;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

/**
 * Automatic field to keep a version of a record
 */
class VersionField extends AbstractAutomaticField {

    /**
     * Name of the version field
     * @var string
     */
    const NAME = 'version';

    /**
     * Translation key for the validation error
     * @var string
     */
    const TRANSLATION_VALIDATION_ERROR = 'orm.error.model.version';

    /**
     * Get the name of this field
     * @return string
     */
    public function getName() {
        return self::NAME;
    }

    /**
     * Hook to set the default value for this automatic field to the data
     * @param mixed $data data object of the model
     * @return null
     */
    public function createData($data) {
        $data->version = 0;
    }

    /**
     * Validate the version of the model
     * @param zibo\library\validation\exception\ValidationException $validationException exception to add possible validation errors
     * @param mixed $data data object of the model
     * @return null
     */
    public function validateData(ValidationException $validationException, $data) {
        if (empty($data->id)) {
            return;
        }

        $currentVersion = $this->findVersionById($data->id);
        if ($data->version == $currentVersion) {
            $data->version = $data->version + 1;
            return;
        }

        $error = new ValidationError(
            self::TRANSLATION_VALIDATION_ERROR,
            'Your data is outdated. You are trying to save version %yourVersion% over version %currentVersion%. Try updating your data first.',
            array('yourVersion' => $data->version, 'currentVersion' => $currentVersion)
        );
        $validationException->addErrors(self::NAME, array($error));
    }

    /**
     * Get the current version of a data object
     * @param int $id primary key of the data
     * @return int the current version of the data object
     */
    private function findVersionById($id) {
        $query = $this->model->createQuery(0);
        $query->setFields('{' . self::NAME . '}');
        $query->addCondition('{' . ModelTable::PRIMARY_KEY . '} = %1%', $id);

        $data = $query->queryFirst();

        if (!$data) {
            return 0;
        }

        return $data->version;
    }

    /**
     * Add 1 to the version field
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

        $versionExpression = new FieldExpression(self::NAME);
        $mathExpression = new MathematicalExpression();
        $mathExpression->addExpression($versionExpression);
        $mathExpression->addExpression(new ScalarExpression(1));

        $statement = new UpdateStatement();
        $statement->addTable(new TableExpression($this->model->getName()));
        $statement->addValue($versionExpression, $mathExpression);
        $statement->addCondition($condition);

        $connection = $this->model->getMeta()->getConnection();
        $connection->executeStatement($statement);

        $this->model->clearCache();
    }

}