<?php

namespace zibo\library\diagram;

use zibo\library\diagram\exception\DiagramException;
use zibo\library\image\Dimension;
use zibo\library\image\Point;

/**
 * The grid is our piece of paper to draw upon with objects
 */
class Grid {

    /**
     * The content of the grid
     * @var array
     */
    protected $grid;

    /**
     * The dimension of the grid
     * @var zibo\library\image\Dimension
     */
    protected $gridDimension;

    /**
     * The dimension of a cell
     * @var zibo\library\image\Dimension
     */
    protected $cellDimension;

    /**
     * The number of cells around an objects where no other objects can be placed
     * @var integer
     */
    protected $objectMargin;

    /**
     * Array with the diagram objects placed on this grid
     * @var array
     */
    protected $objects;

    /**
     * Array with the diagram connections between objects placed on this grid
     * @var array
     */
    protected $connections;

    /**
     * Constructs a new grid
     * @return null
     */
    public function __construct() {
        $this->grid = array();

        $this->gridDimension = null;
        $this->cellDimension = new Dimension(20, 20);
        $this->objectMargin = 7;

        $this->objects = array();
        $this->connections = array();
    }

    /**
     * Gets a string representation of the grid
     * @return string
     */
    public function getGridAsString() {
        $grid = '';

        $objectIds = array_keys($this->objects);
        $index = array();

        foreach ($objectIds as $objectIndex => $objectId) {
            $grid .= $objectIndex . ': ' . $objectId . "<br />\n";

            $index[$objectId] = $objectIndex;
        }

        if (!$this->grid) {
            return '[]';
        }

        $xKeys = array_keys($this->grid);
        $beginX = array_shift($xKeys);
        $endX = array_pop($xKeys);
        $beginY = null;
        $endY = null;
        $maxLength = 3;

        for ($i = $beginX; $i <= $endX; $i++) {
            if (!array_key_exists($i, $this->grid)) {
                continue;
            }

            $yKeys = array_keys($this->grid[$i]);
            $tmpBeginY = array_shift($yKeys);
            $tmpEndY = array_pop($yKeys);

            if ($beginY === null) {
                $beginY = $tmpBeginY;
                $endY = $tmpEndY;
                continue;
            }

            if ($tmpBeginY < $beginY) {
                $beginY = $tmpBeginY;
            }
            if ($tmpEndY > $endY) {
                $endY = $tmpEndY;
            }
        }

        for ($i = $beginX; $i <= $endX; $i++) {
            $grid .= '[';
            for ($j = $beginY; $j <= $endY; $j++) {
                $grid .= "($i, $j): ";
                if (array_key_exists($i, $this->grid) && array_key_exists($j, $this->grid[$i])) {
                    $grid .= str_pad($index[$this->grid[$i][$j]], $maxLength, ' ');
                } else {
                    $grid .= str_pad('', $maxLength, ' ');
                }

                if ($j != $endY) {
                    $grid .= ' | ';
                }
            }
            $grid .= ']<br />' . "\n";
        }

        return $grid;
    }

    /**
     * Gets the dimension of the grid
     * @return zibo\library\image\Dimension The number of cells used
     */
    public function getGridDimension() {
        if ($this->gridDimension) {
            return $this->gridDimension;
        }

        $minX = $minY = $maxX = $maxY = 0;

        foreach ($this->grid as $x => $columns) {
            if ($x < $minX) {
                $minX = $x;
            }

            if ($x > $maxX) {
                $maxX = $x;
            }

            foreach ($this->grid[$x] as $y => $value) {
                if ($y < $minY) {
                    $minY = $y;
                }

                if ($y > $maxY) {
                    $maxY = $y;
                }
            }
        }

        $width = abs($maxX - $minX);
        $height = abs($maxY - $minY);

        $this->gridDimension = new Dimension($width, $height);

        return $this->gridDimension;
    }

    /**
     * Gets the grid point for a point in the image
     * @param zibo\library\image\Point $point
     * @return zibo\library\image\Point $point
     */
    public function getGridPoint(Point $point, $needsLeft = null, $needsUp = null) {
        $divX = $point->getX() / $this->cellDimension->getWidth();
        $divY = $point->getY() / $this->cellDimension->getHeight();

        $x = floor($divX);
        $y = floor($divY);

        if ($needsLeft !== true && ($needsLeft === false || ($needsLeft === null && $divX - $x > 0.5))) {
            $x++;
        }

        if ($needsUp !== true && ($needsUp === false || ($needsUp === null && $divY - $y > 0.5))) {
            $y++;
        }

        return new Point($x, $y);
    }

    /**
     * Gets the image point of the provided grid point. The image point is the center point of the grid cell
     * @param zibo\library\image\Point $point Point identifying a grid cell
     * @param integer $margin The margin of the image
     * @return zibo\library\image\Point Center point of the grid cell in the image
     */
    public function getImagePoint(Point $point, $margin) {
        $x = $point->getX();
        $y = $point->getY();

        $cellWidth = $this->cellDimension->getWidth();
        $cellHeight = $this->cellDimension->getHeight();

        $x = floor($margin + ($x * $cellWidth) + ($cellWidth / 2));
        $y = floor($margin + ($y * $cellHeight) + ($cellHeight / 2));

        return new Point($x, $y);
    }

    /**
     * Checks if the provided begin point needs to go left to reach the end point
     * @param integer beginX X coordinate of the begin point
     * @param integer endX X coordinate of the end point
     * @return boolean|null True if the begin needs to go left, False if the begin needs to right, null to keep value
     */
    public function needsLeft($beginX, $endX) {
        if ($beginX < $endX) {
            return false;
        } elseif ($beginX > $endX) {
            return true;
        }

        return null;
    }

    /**
     * Checks if the provided begin point needs to go up to reach the end point
     * @param integer beginY Y coordinate of the begin point
     * @param integer endY Y coordinate of the end point
     * @return boolean|null True if the begin needs to go up, False if the begin needs to go down, null to keep value
     */
    public function needsUp($beginY, $endY) {
        if ($beginY < $endY) {
            return false;
        } elseif ($beginY > $endY) {
            return true;
        }

        return null;
    }

    /**
     * Checks if the grid point is occupied
     * @param zibo\library\image\Point $gridPoint
     * @param integer $threshold Number of surrounding cells to check
     * @return boolean
     */
    public function isFree(Point $gridPoint, $threshold = 0) {
        $x = $gridPoint->getX();
        $y = $gridPoint->getY();

        $minX = $x - $threshold;
        $maxX = $x + $threshold;
        $minY = $y - $threshold;
        $maxY = $y + $threshold;

        for ($x = $minX; $x <= $maxX; $x++) {
            for ($y = $minY; $y <= $maxY; $y++) {
                if ($this->getCell(new Point($x, $y))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Gets the dimension of a cell in pixels
     * @return zibo\library\image\Dimension Dimension of a cell in pixels
     */
    public function getCellDimension() {
        return $this->cellDimension;
    }

    /**
     * Sets the dimension of a cell in pixels
     * @param zibo\library\image\Dimension $cellDimension Dimension of a grid cell in pixels
     * @return null
     */
    public function setCellDimension(Dimension $cellDimension) {
        $this->cellDimension = $cellDimension;
    }

    /**
     * Gets the contents of a cell
     * @param zibo\library\image\Point $point
     * @return null|string Null if nothing set, The diagram object id if set
     */
    public function getCell(Point $point) {
        $x = $point->getX();

        if (!array_key_exists('' . $x, $this->grid)) {
            return null;
        }

        $y = $point->getY();

        if (!array_key_exists('' . $y, $this->grid[$x])) {
            return null;
        }

        return $this->grid[$x][$y];
    }

    /**
     * Gets the margin for the diagram objects
     * @return integer The number of cells around an objects where no other objects can be placed
     */
    public function getObjectMargin() {
        return $this->objectMargin;
    }

    /**
     * Sets the margin for the diagram objects
     * @param integer $cells The number of cells around an objects where no other objects can be placed
     * @return null
     */
    public function setObjectMargin($cells) {
        $this->objectMargin = $cells;
    }

    /**
     * Checks whether the grid contains a certain diagram object
     * @param string $id Id of the diagram object
     * @return boolean True if the grid contains the provided diagram object, false otherwise
     */
    public function hasDiagramObject($id) {
        return array_key_exists($id, $this->objects);
    }

    /**
     * Gets a diagram object from the grid
     * @param string $id Id of the diagram object
     * @return DiagramObject
     * @throws zibo\library\diagram\exception\DiagramException if the object is not set
     */
    public function getDiagramObject($id) {
        if (!$this->hasDiagramObject($id)) {
            throw new DiagramException('Could not find diagram object ' . $id . ' in this grid');
        }

        return $this->objects[$id];
    }

    /**
     * Gets all the diagram objects placed on the grid
     * @return array Array with the id of the object as key and the instance of DiagramObjects as value
     */
    public function getDiagramObjects() {
        return $this->objects;
    }

    /**
     * Sets the provided object on the provided x and y coordinates
     * @param DiagramObject $object Object to check
     * @param zibo\library\image\Point The top left point for the object
     * @return null
     * @throws zibo\library\diagram\exception\DiagramException when the place is not free
     */
    public function setDiagramObjectAt(DiagramObject $object, Point $point) {
        $id = $object->getId();
        $x = $point->getX();
        $y = $point->getY();

        if (array_key_exists($id, $this->objects)) {
            throw new DiagramException('Could not set diagram object ' . $id . ': id ' . $id . ' is already set');
        }

        if (!$this->isFreeForDiagramObject($object, $point)) {
            $occupant = 'a margin';
            if (isset($this->grid[$x][$y])) {
                $occupant = $this->grid[$x][$y];
            }

            throw new DiagramException('Could not set diagram object ' . $id . ': point ' . $point->__toString() . ' is occupied by ' . $occupant);
        }

        $dimension = $object->getDimension();

        $cellsX = ceil($dimension->getWidth() / $this->cellDimension->getWidth());
        $cellsY = ceil($dimension->getHeight() / $this->cellDimension->getHeight());

        for ($i = 0; $i < $cellsX; $i++) {
            $loopX = '' . ($i + $x);

            if (!array_key_exists($loopX, $this->grid)) {
                $this->grid[$loopX] = array();
            }

            for ($j = 0; $j < $cellsY; $j++) {
                $loopY = '' . ($j + $y);
                $this->grid[$loopX][$loopY] = $id;
            }

            ksort($this->grid[$loopX], SORT_NUMERIC);
        }

        ksort($this->grid, SORT_NUMERIC);

        $object->setGridPoint($point);
        $object->setGridDimension(new Dimension($cellsX, $cellsY));

        $this->objects[$id] = $object;
        $this->gridDimension = null;
    }

    /**
     * Checks of the provided object can be placed on the provided x and y coordinates
     * @param DiagramObject $object Object to check
     * @param zibo\library\image\Point The top left point in the grid for the object
     * @return boolean True if the object can be placed on the provided point, false otherwise
     */
    public function isFreeForDiagramObject(DiagramObject $object, Point $point) {
        $dimension = $object->getDimension();
        $x = $point->getX();
        $y = $point->getY();

        $margin = 2 * $this->objectMargin;

        $cellsX = ceil($dimension->getWidth() / $this->cellDimension->getWidth()) + $margin;
        $cellsY = ceil($dimension->getHeight() / $this->cellDimension->getHeight()) + $margin;

        for ($i = 0; $i < $cellsX; $i++) {
            $loopX = $i + $x - $this->objectMargin;

            if (!array_key_exists('' . $loopX, $this->grid)) {
                continue;
            }

            for ($j = 0; $j < $cellsY; $j++) {
                $loopY = $j + $y - $this->objectMargin;

                if (!array_key_exists('' . $loopY, $this->grid[$loopX])) {
                    continue;
                }

                if ($this->grid[$loopX][$loopY]) {
                    throw new DiagramException('Could not set diagram object ' . $object->getId() . ' to ' . $point . ': (' . $loopX . ', ' . $loopY . ') is occupied by ' . $this->grid[$loopX][$loopY]);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Removes a diagram object from the grid
     * @param string $id Id of the diagram object
     * @return null
     */
    public function removeDiagramObject($id) {
        $object = $this->getDiagramObject($id);

        $dimension = $object->getDimension();

        $cellsX = ceil($dimension->getWidth() / $this->cellDimension->getWidth());
        $cellsY = ceil($dimension->getHeight() / $this->cellDimension->getHeight());

        for ($i = 0; $i < $cellsX; $i++) {
            $loopX = $i + $x;

            for ($j = 0; $j < $cellsY; $j++) {
                unset($this->grid[$loopX][$loopY]);
            }
        }

        $object->setGridPoint(null);
        $object->setGridDimension(null);

        unset($this->objects[$id]);
    }

    /**
     * Adds a connection between 2 diagram objects
     * @param DiagramConnection $connection
     * @return null
     */
    public function addDiagramConnection(DiagramConnection $connection) {
        $this->connections[$connection->getId()] = $connection;
    }

    /**
     * Gets the connections between the diagram objects
     * @return array
     */
    public function getDiagramConnections() {
        return $this->connections;
    }

}