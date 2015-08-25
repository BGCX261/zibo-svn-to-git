<?php

namespace zibo\library\orm\erd;

use zibo\library\diagram\Diagram;
use zibo\library\diagram\PlainDiagramConnection;
use zibo\library\image\Color;
use zibo\library\image\Dimension;
use zibo\library\image\Image;
use zibo\library\image\Point;
use zibo\library\orm\definition\field\RelationField;
use zibo\library\orm\model\meta\ModelMeta;

/**
 * Diagram connection between 2 model diagram objects
 */
class ModelDiagramConnection extends PlainDiagramConnection {

    /**
     * Field name in the begin model
     * @var string
     */
    private $beginFieldName;

    /**
     * Field name in the end model
     * @var string
     */
    private $endFieldName;

    /**
     * The point of the begin field
     * @var zibo\library\image\Point
     */
    private $beginPoint;

    /**
     * The point of the end field
     * @var zibo\library\image\Point
     */
    private $endPoint;

    /**
     * Sets the name of the field in the begin model
     * @param string $fieldName Field name in the begin model
     * @return null
     */
    public function setBeginFieldName($fieldName) {
        $this->beginFieldName = $fieldName;
        $this->id = md5($this->id . $fieldName);
    }

    /**
     * Sets the name of the field in the end model
     * @param string $fieldName Field name in the end model
     * @return null
     */
    public function setEndFieldName($fieldName) {
        $this->endFieldName = $fieldName;
        $this->id = md5($this->id . $fieldName);
    }

    /**
     * Gets the actual location of the field in the image and updates the begin and end point of the connection
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function preDraw(Diagram $diagram) {
        $grid = $diagram->getGrid();

        $beginModel = $grid->getDiagramObject($this->begin);
        $endModel = $grid->getDiagramObject($this->end);

        // Get the field points left or right of the model boxes
        $beginFieldPoint = $beginModel->getFieldPoint($this->beginFieldName);
        $endFieldPoint = $endModel->getFieldPoint($this->endFieldName);

        $beginWidth = $beginModel->getDimension()->getWidth();
        $endWidth = $endModel->getDimension()->getWidth();

        $beginX = $beginFieldPoint->getX();
        $endX = $endFieldPoint->getX();

        $beginIsLeft = true;
        $endIsLeft = true;

        if ($beginX < $endX) {
            if ($beginX + $beginWidth < $endX) {
                $beginX += $beginWidth;
                $beginIsLeft = false;
            }
        } else {
            if ($beginX > $endX + $endWidth) {
                $endX += $endWidth;
                $endIsLeft = false;
            }
        }

        $this->beginPoint = new Point($beginX, $beginFieldPoint->getY());
        $this->endPoint = new Point($endX, $endFieldPoint->getY());

        // Now get a point in the grid 2 cells away from the model box
        $margin = $diagram->getMargin();

        $beginGridPoint = $grid->getGridPoint(new Point($this->beginPoint->getX() - $margin, $this->beginPoint->getY() - $margin));
        $endGridPoint = $grid->getGridPoint(new Point($this->endPoint->getX() - $margin, $this->endPoint->getY() - $margin));

        $beginGridX = $beginGridPoint->getX();
        $beginGridY = $beginGridPoint->getY();
        $endGridX = $endGridPoint->getX();
        $endGridY = $endGridPoint->getY();

        $needsUp = $grid->needsUp($beginGridY, $endGridY);
        if ($needsUp === true) {
             $beginGridY--;
             if ($beginGridY != $endGridY) {
                 $endGridY++;
             }
        } elseif ($needsUp === false) {
             $beginGridY++;
             if ($beginGridY != $endGridY) {
                 $endGridY--;
             }
        }

        $offset = 2;

        if ($beginIsLeft) {
            $beginGridX -= $offset;
        } else {
            $beginGridX += $offset;
        }

        if ($endIsLeft) {
            $endGridX -= $offset;
        } else {
            $endGridX += $offset;
        }

        $beginGridX = max($beginGridX, -1);
        $beginGridY = max($beginGridY, -1);
        $endGridX = max($endGridX, -1);
        $endGridY = max($endGridY, -1);

        $this->begin = new Point($beginGridX, $beginGridY);
        $this->end = new Point($endGridX, $endGridY);
    }

    /**
     * Sets the points to connect in order to draw this connection
     * @param array $points Array of Point objects
     * @return null
     */
    public function setPoints(array $points) {
        if (count($points) > 4) {
            array_shift($points);
            array_pop($points);
        }

        array_unshift($points, $this->beginPoint);
        $points[] = $this->endPoint;

        parent::setPoints($points);
    }

    /**
     * Draw the connection on the provided image
     * @param zibo\library\image\Image $image
     * @return null
     */
    public function draw(Image $image) {
        parent::draw($image);

        $bulletDimension = new Dimension(3, 3);

        $image->fillEllipse($this->beginPoint, $bulletDimension, $this->color);
        $image->fillEllipse($this->endPoint, $bulletDimension, $this->color);
    }

}