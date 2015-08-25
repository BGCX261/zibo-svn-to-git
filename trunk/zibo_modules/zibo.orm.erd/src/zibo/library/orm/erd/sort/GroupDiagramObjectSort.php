<?php

namespace zibo\library\orm\erd\sort;

/**
 * Implementation to sort diagram objects by their group
 */
class GroupDiagramObjectSort implements DiagramObjectSort {

    /**
     * The default group
     * @var string
     */
    const DEFAULT_GROUP = 'erd';

    /**
     * Sorts the diagram objects by their group
     * @param array $diagramObjects Array with the model diagram objects to sort
     * @return array Sorted diagram objects
     */
    public function sortDiagramObjects(array $diagramObjects) {
        $groups = array(
            self::DEFAULT_GROUP => array()
        );

        foreach ($diagramObjects as $modelDiagramObject) {
            $modelTable = $modelDiagramObject->getMeta()->getModelTable();
            $group = $modelTable->getGroup();

            if (!$group) {
                $groups[self::DEFAULT_GROUP][] = $modelDiagramObject;
                continue;
            }

            if (!array_key_exists($group, $groups)) {
                $groups[$group] = array();
            }

            $groups[$group][] = $modelDiagramObject;
        }

        $sortedDiagramObjects = array();
        foreach ($groups as $groupName => $groupDiagramObjects) {
            foreach ($groupDiagramObjects as $modelDiagramObject) {
                $modelName = $modelDiagramObject->getMeta()->getName();
                $sortedDiagramObjects[$modelName] = $modelDiagramObject;
            }
        }

        return $sortedDiagramObjects;
    }

}