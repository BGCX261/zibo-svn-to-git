<?php

namespace zibo\library\orm\erd\layout;

use zibo\library\diagram\Grid;
use zibo\library\image\Point;
use zibo\library\orm\erd\sort\GroupDiagramObjectSort;

/**
 * Layout to put diagram objects in a grid shape
 */
class GridLayout implements Layout {

    private $x;

    private $y;

    private $startX = 1;

    private $startY = 1;

    private $numObject;

    private $numObjectsPerRow;

    private $rowHeight;

    /**
     * Diagram objects sort
     * @var zibo\library\orm\erd\layout\sort\DiagramObjectSort
     */
    private $dos;

    /**
     * Constructs a new grid layout
     * @param zibo\library\orm\erd\layout\sort\DiagramObjectSort $dos Sorting algorithm for the diagram objects
     * @return null
     */
    public function __construct(DiagramObjectSort $dos = null) {
        if (!$dos) {
            $dos = new GroupDiagramObjectSort();
        }

        $this->dos = $dos;
    }

    /**
     * Performs the layouting of the diagram objects
     * @param zibo\library\diagram\Grid $grid The grid to place the objects on
     * @param array $diagramObjects An array with the model diagram objects to layout
     * @return null
     */
    public function performLayout(Grid $grid, array $diagramObjects) {
        $diagramObjects = $this->dos->sortDiagramObjects($diagramObjects);

        $numObjects = count($diagramObjects);

        if ($numObjects < 5) {
            $divider = 2;
        } elseif ($numObjects < 9) {
            $divider = 3;
        } elseif ($numObjects < 16) {
            $divider = 4;
        } elseif ($numObjects < 24) {
            $divider = 5;
        } else {
            $divider = 6;
        }

        $this->numRowObject = 0;
        $this->numObjectsPerRow = ceil($numObjects / $divider);

        $this->x = $this->startX;
        $this->y = $this->startY;

        $this->rowHeight = 0;

        while ($diagramObject = array_pop($diagramObjects)) {
            $this->addDiagramObject($grid, $diagramObject);

            $meta = $diagramObject->getMeta();

            if ($meta->isLocalized()) {
                $localizedModelName = $meta->getLocalizedModelName();

                if (array_key_exists($localizedModelName, $diagramObjects)) {
                    $localizedDiagramObject = $diagramObjects[$localizedModelName];

                    $this->addDiagramObject($grid, $localizedDiagramObject);

                    unset($diagramObjects[$localizedModelName]);
                }
            }

            $relationFields = $diagramObject->getRelationFields();
            foreach ($relationFields as $relationField) {
                $relationModelName = $relationField->getRelationModelName();
                if (!array_key_exists($relationModelName, $diagramObjects)) {
                    continue;
                }

                $linkModelName = $relationField->getLinkModelName();
                if (array_key_exists($linkModelName, $diagramObjects)) {
                    $linkDiagramObject = $diagramObjects[$linkModelName];

                    $this->addDiagramObject($grid, $linkDiagramObject);

                    unset($diagramObjects[$linkModelName]);
                }

                $relationDiagramObject = $diagramObjects[$relationModelName];

                $this->addDiagramObject($grid, $relationDiagramObject);

                unset($diagramObjects[$relationModelName]);
            }
        }
    }

    private function addDiagramObject($grid, $diagramObject) {
        $point = new Point($this->x, $this->y);

        $grid->setDiagramObjectAt($diagramObject, $point);

        $gridDimension = $diagramObject->getGridDimension();

        \zibo\core\Zibo::getInstance()->runEvent('log', 'Adding ' . $diagramObject->getId() . ' to ' . $point, $gridDimension);


        $cellHeight = $gridDimension->getHeight();
        if ($cellHeight > $this->rowHeight) {
            $this->rowHeight = $cellHeight;
        }

        $objectMargin = $grid->getObjectMargin();

        $this->numRowObject++;
        if ($this->numRowObject == $this->numObjectsPerRow) {
            $this->x = $this->startX;
            $this->y += $this->rowHeight + $objectMargin;
            $this->numRowObject = 0;
            $this->rowHeight = 0;

            $point = new Point($this->x, $this->y);
            \zibo\core\Zibo::getInstance()->runEvent('log', 'New row ' . $point);
            return;
        }

        $this->x += $gridDimension->getWidth() + $objectMargin;

        $point = new Point($this->x, $this->y);
        \zibo\core\Zibo::getInstance()->runEvent('log', 'Next point ' . $point);
    }

}