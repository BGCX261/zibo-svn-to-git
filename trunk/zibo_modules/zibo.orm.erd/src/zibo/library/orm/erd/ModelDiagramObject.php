<?php

namespace zibo\library\orm\erd;

use zibo\library\diagram\AbstractDiagramObject;
use zibo\library\image\Color;
use zibo\library\image\Dimension;
use zibo\library\image\Image;
use zibo\library\image\Point;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\orm\model\meta\ModelMeta;

/**
 * Diagram object for a model
 */
class ModelDiagramObject extends AbstractDiagramObject {

    /**
     * Generic character width
     * @var integer
     */
    const CHARACTER_WIDTH = 6;

    /**
     * Generic character height
     * @var integer
     */
    const CHARACTER_HEIGHT = 8;

    /**
     * The meta of the model
     * @var zibo\library\orm\model\meta\ModelMeta
     */
    private $meta;

    /**
     * Array with the image points of the fields
     * @var array
     */
    private $fieldPoints;

    /**
     * The color of the background
     * @var zibo\library\image\Color
     */
    private $backgroundColor;

    /**
     * The color of the text and borders
     * @var zibo\library\image\Color
     */
    private $frontColor;

    /**
     * The top padding of the box
     * @var integer
     */
    private $paddingTop;

    /**
     * The right padding of the box
     * @var integer
     */
    private $paddingRight;

    /**
     * The bottom padding of the box
     * @var integer
     */
    private $paddingBottom;

    /**
     * The left padding of the box
     * @var integer
     */
    private $paddingLeft;

    /**
     * Constructs a new model diagram object
     * @param zibo\library\orm\model\meta\ModelMeta $meta The meta of the model
     * @return null
     */
    public function __construct(ModelMeta $meta) {
        $this->id = $meta->getName();
        $this->meta = $meta;
        $this->fieldPoints = array();

        $this->setPadding(5, 5, 5, 5);
        $this->setBackgroundColor(new Color(255, 255, 255));
        $this->setFrontColor(new Color(15, 15, 15));
    }

    /**
     * Gets the meta of the model
     * @return zibo\library\orm\model\meta\ModelMeta
     */
    public function getMeta() {
        return $this->meta;
    }

    /**
     * Gets the relation fields of this model
     * @return array
     */
    public function getRelationFields() {
        $relationFields = array();

        $fields = $this->meta->getFields();
        foreach ($fields as $fieldName => $field) {
            if (!($field instanceof RelationField)) {
                continue;
            }

            $relationFields[$fieldName] = $field;
        }

        return $relationFields;
    }

    /**
     * Gets the point on the image of the left border of the provided field.
     * @param string $fieldName Name of the field
     * @return zibo\library\image\Point|null
     */
    public function getFieldPoint($fieldName) {
        if (!array_key_exists($fieldName, $this->fieldPoints)) {
            return null;
        }

        return $this->fieldPoints[$fieldName];
    }

    /**
     * Sets the color of the text and borders
     * @param zibo\library\image\Color $color
     * @return null
     */
    public function setFrontColor(Color $color) {
        $this->frontColor = $color;
    }

    /**
     * Sets the color of the background
     * @param zibo\library\image\Color $color
     * @return null
     */
    public function setBackgroundColor(Color $color) {
        $this->backgroundColor = $color;
    }

    /**
     * Sets the padding of the box
     * @param integer $top The top padding
     * @param integer $right The right padding
     * @param integer $bottom The bottom padding
     * @param integer $left The left padding
     * @return null
     */
    public function setPadding($top = null, $right = null, $bottom = null, $left = null) {
        if ($top !== null) {
            $this->paddingTop = $top;
        }

        if ($right !== null) {
            $this->paddingRight = $right;
        }

        if ($bottom !== null) {
            $this->paddingBottom = $bottom;
        }

        if ($left !== null) {
            $this->paddingLeft = $left;
        }

        $this->calculateDimension();
    }

    /**
     * Draws the object on the provided image
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\image\Point $point The top left corner to start drawing
     * @return null
     */
    public function draw(Image $image, Point $point) {
        $x = $point->getX();
        $y = $point->getY();
        $width = $this->dimension->getWidth();
        $height = $this->dimension->getHeight();

        $textX = $x + $this->paddingLeft;

        // draw the box
        $image->fillRoundedRectangle($point, $this->dimension, 3, $this->backgroundColor);
        $image->drawRoundedRectangle($point, $this->dimension, 3, $this->frontColor);

        // draw the title bar
        $title = $this->meta->getName(); // . ($this->isLinkModel ? ' (L)' : '');
        $image->drawText(new Point($textX, $point->getY() + $this->paddingTop), $this->frontColor, $title);

        $lineY = $y + (2 * $this->paddingTop) + self::CHARACTER_HEIGHT + $this->paddingBottom;
        $image->drawLine(new Point($x, $lineY), new Point($x + $width, $lineY), $this->frontColor);

        // draw the fields
        $fieldY = $lineY + $this->paddingTop;

        $fields = $this->meta->getFields();
        foreach ($fields as $fieldName => $field) {
            $label = '+ ' . $field->getName() . ': ' . $this->getFieldType($field);

            $this->fieldPoints[$fieldName] = new Point($x, ceil($fieldY + $this->paddingTop + 1));

            $image->drawText(new Point($textX, $fieldY), $this->frontColor, $label);
            $fieldY += $this->paddingTop + self::CHARACTER_HEIGHT;
        }
    }

    /**
     * Calculates the dimension of the diagram object
     * @return null
     */
    private function calculateDimension() {
        $maxLength = strlen($this->meta->getName());

        $fields = $this->meta->getFields();
        foreach ($fields as $fieldName => $field) {
            $fieldNameLength = strlen($fieldName . $this->getFieldType($field)) + 4;
            if ($fieldNameLength > $maxLength) {
                $maxLength = $fieldNameLength;
            }
        }

        $width = ($maxLength * self::CHARACTER_WIDTH) + $this->paddingLeft + $this->paddingRight;
        $height = ((count($fields) + 2) * ($this->paddingTop + self::CHARACTER_HEIGHT)) + $this->paddingTop + $this->paddingBottom;

        $this->dimension = new Dimension($width, $height);
    }

    /**
     * Gets the type of a field for display
     * @param zibo\library\orm\definition\field\ModelField $field
     * @return string
     */
    private function getFieldType($field) {
        if ($field instanceof RelationField) {
            $type = $field->getRelationModelName();
        } else {
            $type = $field->getType();
        }

        return $type;
    }

}