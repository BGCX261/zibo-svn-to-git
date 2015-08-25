<?php

namespace zibo\orm\builder\table\decorator;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\field\HasOneField;
use zibo\library\orm\definition\field\HasManyField;
use zibo\library\orm\definition\field\ModelField;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\String;

/**
 * Decorator for a model field
 */
class ModelFieldDecorator implements Decorator {

    /**
     * URL to the action of a field name
     * @var string
     */
    private $fieldAction;

    /**
     * URL to the action of a model
     * @var string
     */
    private $modelAction;

    /**
     * Instance of the translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new field decorator
     * @param string $fieldAction URL to the action for a field
     * @param string $modelAction URL to the action for a model
     * @return null
     */
    public function __construct($fieldAction = null, $modelAction = null) {
        $this->fieldAction = $fieldAction;
        $this->modelAction = $modelAction;
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
        $field = $cell->getValue();
        if (!($field instanceof ModelField)) {
            return;
        }

        $fieldName = $field->getName();

        if ($this->fieldAction) {
            $anchor = new Anchor($fieldName, $this->fieldAction . $fieldName);
            $value = $anchor->getHtml();
        } else {
            $value = $fieldName;
        }

        $value .= '<div class="info">';
        if ($field instanceof RelationField) {
            if ($field instanceof BelongsToField) {
                $relationType = 'belongsTo';
            } elseif ($field instanceof HasOneField) {
                $relationType = 'hasOne';
            } else if ($field instanceof HasManyField) {
                $relationType = 'hasMany';
            }

            $relationModelName = $field->getRelationModelName();
            $linkModelName = $field->getLinkModelName();
            $foreignKeyName = $field->getForeignKeyName();

            if ($this->modelAction) {
                $anchor = new Anchor($relationModelName, $this->modelAction . $relationModelName);
                $relationModelName = $anchor->getHtml();

                if ($linkModelName) {
                    $anchor = new Anchor($linkModelName, $this->modelAction . $linkModelName);
                    $linkModelName = $anchor->getHtml();
                }
            }

            $parameters = array(
                'type' => $relationType,
                'model' => $relationModelName,
                'link' => $linkModelName,
                'foreignKey' => $foreignKeyName,
            );

            if ($linkModelName) {
                if ($foreignKeyName) {
                    $value .= $this->translator->translate('orm.label.relation.type.link.fk', $parameters);
                } else {
                    $value .= $this->translator->translate('orm.label.relation.type.link', $parameters);
                }
            } else {
                if ($foreignKeyName) {
                    $value .= $this->translator->translate('orm.label.relation.type.fk', $parameters);
                } else {
                    $value .= $this->translator->translate('orm.label.relation.type', $parameters);
                }
            }
        } else {
            $value .= $this->translator->translate('orm.label.field.type', array('type' => $field->getType())) . '<br />';

            $defaultValue = $field->getDefaultValue();
            if (!String::isEmpty($defaultValue)) {
                $value .= $this->translator->translate('orm.label.value.default') . ': ' . $defaultValue;
            }
        }
        $value .= '</div>';

        $cell->setValue($value);
    }

}