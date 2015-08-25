<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\orm\builder\definition\BuilderTable;
use zibo\library\orm\definition\field\PropertyField;
use zibo\library\orm\model\Model;
use zibo\library\orm\ModelManager;

/**
 * Decorator for a model
 */
class ModelDecorator implements Decorator {

    /**
     * The action behind the model name
     * @var string
     */
    private $action;

    /**
     * Translator needed for the model information
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new model decorator
     * @param string $action URL for the anchor behind the model name
     * @return null
     */
    public function __construct($action = null) {
        $this->action = $action;
        $this->translator = I18n::getInstance()->getTranslator();
    }

    /**
     * Decorates the cell
     * @param zibo\library\html\table\Cell $cell Cell of the value to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array containing the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $model = $cell->getValue();
        if (!($model instanceof Model)) {
            return;
        }

        $modelName = $model->getName();

        if ($this->action) {
            $anchor = new Anchor($modelName, $this->action . $modelName);
            $value = $anchor->getHtml();
        } else {
            $value = $modelName;
        }

        $info = $this->getRelationInfo($model) . $this->getUnlinkedModelsInfo($modelName);

        if ($info) {
            $value .= '<div class="info">' . $info . '</div>';
        }

        $cell->setValue($value);
    }

    /**
     * Gets the general relation information of the provided model
     * @param zibo\library\orm\model\Model $model The model to get the information from
     * @return string The general relation information of the provided model
     */
    private function getRelationInfo(Model $model) {
        $table = $model->getMeta()->getModelTable();

        $info = '';

        $relations = array();
        $fields = $table->getFields();
        foreach ($fields as $field) {
            if ($field instanceof PropertyField) {
                continue;
            }

            $relationModelName = $field->getRelationModelName();

            $relationModelValue = $relationModelName;
            if ($this->action) {
                $anchor = new Anchor($relationModelName, $this->action . $relationModelName);
                $relationModelValue = $anchor->getHtml();
            }

            $relations[$relationModelName] = $relationModelValue;
        }
        $numRelations = count($relations);

        if ($numRelations == 1) {
            $relation = array_pop($relations);
            $info .= $this->translator->translate('orm.label.relation.with', array('model' => $relation)) . '<br />';
        } elseif ($numRelations) {
            $last = array_pop($relations);
            $first = implode(', ', $relations);
            $info .= $this->translator->translate('orm.label.relations.with', array('first' => $first, 'last' => $last)) . '<br />';
        }

        return $info;
    }

    /**
     * Gets the information about the unlinked models of the provided table
     * @param string $tableName The name of the table
     * @return string The information about the unlinked models of the provided table
     */
    private function getUnlinkedModelsInfo($tableName) {
        $info = '';

        $model = ModelManager::getInstance()->getModel($tableName);
        $unlinkedModels = $model->getMeta()->getUnlinkedModels();
        $numUnlinkedModels = count($unlinkedModels);

        if ($this->action) {
            foreach ($unlinkedModels as $index => $modelName) {
                $anchor = new Anchor($modelName, $this->action . $modelName);
                $unlinkedModels[$index] = $anchor->getHtml();
            }
        }

        if ($numUnlinkedModels == 1) {
            $model = array_pop($unlinkedModels);
            $info .= $this->translator->translate('orm.label.unlinked.model', array('model' => $model)) . '<br />';
        } elseif ($numUnlinkedModels) {
            $last = array_pop($unlinkedModels);
            $first = implode(', ', $unlinkedModels);
            $info .= $this->translator->translate('orm.label.unlinked.models', array('first' => $first, 'last' => $last)) . '<br />';
        }

        return $info;
    }

}