<?php

namespace zibo\library\diagram\layer;

use zibo\core\Zibo;

use zibo\library\diagram\exception\ConnectionDiagramException;
use zibo\library\diagram\exception\DiagramException;
use zibo\library\diagram\path\PathFinder;
use zibo\library\diagram\Diagram;
use zibo\library\diagram\DiagramConnection;
use zibo\library\diagram\Grid;
use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * Layer implementation to draw the diagram connections
 */
class ConnectionLayer implements Layer {

    /**
     * Array with the path finders for the connections
     * @var array
     */
    private $pathFinders;

    /**
     * Constructs the connection layer
     * @return false;
     */
    public function __construct() {
        $this->pathFinders = array();
    }

    /**
     * Adds a path finder for this layer.
     * @param zibo\library\diagram\path\PathFinder $pathFinder The path finder to add
     * @return null
     */
    public function addPathFinder(PathFinder $pathFinder) {
        $this->pathFinders[] = $pathFinder;
    }

    /**
     * Draws the layers content on the image
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function draw(Image $image, Diagram $diagram) {
        if (!$this->pathFinders) {
            throw new ConnectionDiagramException('Could not draw the connections: no path finders set');
        }

        $grid = $diagram->getGrid();
        $connections = $grid->getDiagramConnections();

        foreach ($connections as $connection) {
            $this->drawConnection($image, $diagram, $connection);
        }
    }

    /**
     * Finds a path for the connection and draws it
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @param zibo\library\diagram\DiagramConnection $connection The connection to draw
     * @return boolean True if the connection has been drawn, false otherwise
     */
    private function drawConnection(Image $image, Diagram $diagram, DiagramConnection $connection) {
        try {
            $connection->preDraw($diagram);

            $begin = $connection->getBegin();
            $end = $connection->getEnd();

            $beginPoint = $this->getDiagramObjectPoint($begin, $diagram);
            $endPoint = $this->getDiagramObjectPoint($end, $diagram);

            $path = false;
            foreach ($this->pathFinders as $pathFinder) {
                $pathFinder->setDiagram($diagram);

                $path = $pathFinder->findPath($beginPoint, $endPoint);

                if ($path) {
                    break;
                }
            }

            if (!$path) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Could not find a path for the connection ' . $connection->getId(), $begin . ' -> ' . $end, 1);
                return false;
            }

            $points = $this->getImagePoints($path, $diagram);

            $connection->setPoints($points);
            $connection->draw($image);
        } catch (DiagramException $e) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Exception: ' . $e->getMessage(), $e->getTraceAsString(), 1);
            return false;
        }

        return true;
    }

    /**
     * Gets the points on the image for the provided grid points
     * @param array $gridPoints Grid points to calculate the image points of
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return array Array with the image points of the provided grid points
     */
    private function getImagePoints(array $gridPoints, Diagram $diagram) {
        $grid = $diagram->getGrid();
        $margin = $diagram->getMargin();

        $points = array();
        foreach ($gridPoints as $gridPoint) {
            $points[] = $grid->getImagePoint($gridPoint, $margin);
        }

        return $points;
    }

    /**
     * Gets the point for the provided id of a diagram object
     * @param string|zibo\library\image\Point $id Id of the diagram object or a point
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return zibo\library\image\Point The provided point or the point of the provided diagram object
     */
    private function getDiagramObjectPoint($id, Diagram $diagram) {
        if ($id instanceof Point) {
            return $id;
        }

        $grid = $diagram->getGrid();

        $object = $grid->getDiagramObject($point);

        return $object->getGridPoint();
    }

}