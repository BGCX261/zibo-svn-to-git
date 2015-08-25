<?php

namespace zibo\library\orm\erd\sort;

/**
 * Interface to sort diagram objects according to a certain rule or algorithm
 */
interface DiagramObjectSort {

    /**
     * Sorts the diagram objects according to a certain rule or algorithm
     * @param array $diagramObjects Array with the diagram objects to sort
     * @return array Sorted diagram objects
     */
    public function sortDiagramObjects(array $diagramObjects);

}