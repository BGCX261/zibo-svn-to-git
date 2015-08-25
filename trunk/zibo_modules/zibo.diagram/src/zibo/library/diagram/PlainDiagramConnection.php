<?php

namespace zibo\library\diagram;

use zibo\library\image\Color;
use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * A plain connection between 2 objects on the grid
 */
class PlainDiagramConnection implements DiagramConnection {

    /**
     * The id of this connection
     * @var string
     */
    protected $id;

    /**
     * The color for this connection
     * @var zibo\library\image\Color
     */
    protected $color;

    /**
     * The begin of this connection
     * @var string|zibo\library\image\Point
     */
    protected $begin;

    /**
     * The end of this connection
     * @var string|zibo\library\image\Point
     */
    protected $end;

    /**
     * The points of this connection
     * @var array
     */
    protected $points;

    /**
     * Constructs a new plain connection
     * @param string|zibo\library\image\Point $begin Id of the begin diagram object or a point in the grid
     * @param string|zibo\library\image\Point $end Id of the end diagram object or a point in the grid
     * @param string $id Id of this connection
     */
    public function __construct($begin, $end, $id = null) {
        if (!$id) {
            $id = md5($begin . $end . microtime());
        }

        $this->id = $id;
        $this->color = new Color(0, 0, 0);
        $this->begin = $begin;
        $this->end = $end;
    }

    /**
     * Gets the color of this connection
     * @return zibo\library\image\Color
     */
    public function getColor() {
        return $this->color;
    }

    /**
     * Sets the color of this connection
     * @param zibo\library\image\Color $color
     * @return null
     */
    public function setColor(Color $color) {
        $this->color = $color;
    }

    /**
     * Gets a unique id for the connection
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the begin of the connection
     * @return string|zibo\library\image\Point Id of the begin diagram object or a point in the grid
     */
    public function getBegin() {
        return $this->begin;
    }

    /**
     * Gets the end of the connection
     * @return string|zibo\library\image\Point Id of the end diagram object or a point in the grid
     */
    public function getEnd() {
        return $this->end;
    }

    /**
     * Sets the points to connect in order to draw this connection
     * @param array $points Array of Point objects
     * @return null
     */
    public function setPoints(array $points) {
        $this->points = $points;
    }

    /**
     * Gets the points to connect in order to draw this connection
     * @return array Array of Point objects
     */
    public function getPoints() {
        $this->points = $points;
    }

    /**
     * Hook to process the connection before the connection layer is drawn
     * @param zibo\library\diagram\Diagram $diagram
     * @return null
     */
    public function preDraw(Diagram $diagram) {

    }

    /**
     * Draw the connection on the provided image
     * @param zibo\library\image\Image $image
     * @return null
     */
    public function draw(Image $image) {
        $previousPoint = null;

        foreach ($this->points as $point) {
            if (!$previousPoint) {
                $previousPoint = $point;
                continue;
            }

            $image->drawLine($previousPoint, $point, $this->color);

            $previousPoint = $point;
        }
    }

}