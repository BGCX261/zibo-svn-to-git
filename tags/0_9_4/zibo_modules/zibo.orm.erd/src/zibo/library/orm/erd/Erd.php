<?php

namespace zibo\library\orm\erd;

use zibo\library\diagram\layer\ConnectionLayer;
use zibo\library\diagram\layer\GridLayer;
use zibo\library\diagram\layer\ObjectLayer;
use zibo\library\diagram\path\AstarPathFinder;
use zibo\library\diagram\Diagram;
use zibo\library\diagram\Grid;
use zibo\library\filesystem\File;
use zibo\library\image\Color;
use zibo\library\orm\definition\field\BelongsToField;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\erd\layout\GridLayout;
use zibo\library\orm\erd\layout\Layout;
use zibo\library\orm\model\LocalizedModel;

/**
 * Class to create a Entity Relationshop Diagram of orm models
 */
class Erd {

    /**
     * The default file name
     * @var string
     */
    const DEFAULT_FILE_NAME = 'application/data/erd.png';

    /**
     * The layout for the objects of the diagram
     * @var zibo\library\orm\erd\layout\Layout
     */
    protected $layout;

    /**
     * Flag to see if models of the same group should get the same color
     * @var boolean
     */
    protected $willColorGroups;

    /**
     * Color of the groups
     * @var array
     */
    private $groupColors;

    /**
     * Predefined colors
     * @var array
     */
    private $colors;

    /**
     * Constructs a new Erd object
     * @return null
     */
    public function __construct() {
        $this->layout = new GridLayout();

        $this->willColorGroups = true;
        $this->groupColors = array();
        $this->colors = array(
            new Color(250, 250, 223), // YELLOW
            new Color(221, 250, 223), // GREEN
            new Color(214, 235, 250), // BLUE
            new Color(250, 214, 214), // RED
            new Color(220, 220, 220), // GREY
            new Color(250, 228, 223), // ORANGE
            new Color(248, 230, 214), // BROWN
            new Color(223, 250, 240), // EXOTIC BLUE
            new Color(250, 223, 250), // PURPLE
        );
    }

    /**
     * Sets the layout for the diagram
     * @param zibo\library\orm\erd\layout\Layout $layout
     * @return null
     */
    public function setLayout(Layout $layout) {
        $this->layout = $layout;
    }

    /**
     * Gets the layout for the diagram
     * @return zibo\library\orm\erd\layout\Layout
     */
    public function getLayout() {
        return $this->layout;
    }

    /**
     * Sets whether the models of the same group will be colored with the same color
     * @param boolean $flag True to color the models, false otherwise
     * @return null
     */
    public function setWillColorGroups($flag) {
        $this->willColorGroups = $flag;
    }

    /**
     * Gets whether the models of the same group will be colored with the same color
     * @return boolean True to color the models, false otherwise
     */
    public function willColorGroups() {
        return $this->willColorGroups;
    }

    /**
     * Sets the predefined colors for the groups
     * @param array $colors Array with Color objects
     * @return null
     */
    public function setColors(array $colors) {
        $this->colors = $color;
    }

    /**
     * Gets the predefined colors for the groups
     * @return array Array with Color objects
     */
    public function getColors() {
        return $colors;
    }

    /**
     * Gets a file with a erd diagram of the provided models
     * @param array $models Models to draw on the diagram
     * @param zibo\library\filesystem\File $file File for the image
     * @return zibo\library\filesystem\File $file File to the image
     */
    public function getFile(array $models, File $file = null) {
        if (!$file) {
            $file = new File(self::DEFAULT_FILE_NAME);
        }

        $image = $this->getImage($models);
        $image->write($file);

        return $file;
    }

    /**
     * Creates a image of the diagram
     * @param array $models Models to draw on the diagram
     * @return zibo\library\image\Image Image of the diagram
     */
    public function getImage(array $models) {
        $objects = $this->getDiagramObjects($models);
        $connections = $this->getDiagramConnections($objects);

        $layout = $this->getLayout();
        $diagram = $this->createDiagram();
        $grid = $diagram->getGrid();

        $layout->performLayout($grid, $objects);

        foreach ($connections as $connection) {
            $grid->addDiagramConnection($connection);
        }

        return $diagram->getImage();
    }

    /**
     * Creates a diagram object and adds the necessairy layers
     * @return zibo\library\diagram\Diagram
     */
    protected function createDiagram() {
        $connectionLayer = new ConnectionLayer();
        $connectionLayer->addPathFinder(new AstarPathFinder());

        $diagram = new Diagram();
        $diagram->addLayer(new GridLayer());
        $diagram->addLayer(new ObjectLayer());
        $diagram->addLayer($connectionLayer);

        return $diagram;
    }

    /**
     * Gets the diagram connections for the provided model diagram objects
     * @param array $modelDiagramObjects Array with ModelDiagramObjects
     * @return array Array with the connections of the provided models
     */
    protected function getDiagramConnections($modelDiagramObjects) {
        $connections = array();
        $connectedModelDiagramObjects = array();

        foreach ($modelDiagramObjects as $modelDiagramObject) {
            $meta = $modelDiagramObject->getMeta();
            $modelName = $meta->getName();

            if ($meta->isLocalized()) {
                $localizedModelName = $meta->getLocalizedModelName();
                if (array_key_exists($localizedModelName, $modelDiagramObjects) && !array_key_exists($localizedModelName, $connectedModelDiagramObjects)) {
                    $connection = new ModelDiagramConnection($modelName, $localizedModelName);
                    $connection->setColor($this->getRandomColor());
                    $connection->setBeginFieldName(ModelTable::PRIMARY_KEY);
                    $connection->setEndFieldName(LocalizedModel::FIELD_DATA);

                    $connections[] = $connection;
                }
            }

            $relationFields = $modelDiagramObject->getRelationFields();
            if (!$relationFields) {
                continue;
            }

            foreach ($relationFields as $fieldName => $field) {
                $relationModelName = $field->getRelationModelName();
                $linkModelName = $field->getLinkModelName();

                if ($linkModelName) {
                    $relationModelName = $linkModelName;
                    $foreignKey = $meta->getRelationForeignKeyToSelf($fieldName);
                } else {
                    if ($field instanceof BelongsToField) {
                        $foreignKey = ModelTable::PRIMARY_KEY;
                    } else {
                        $foreignKey = $meta->getRelationForeignKey($fieldName);
                    }
                }

                if (!$foreignKey || !array_key_exists($relationModelName, $modelDiagramObjects)) {
                    continue;
                }

                $relationMeta = $modelDiagramObjects[$relationModelName]->getMeta();
                $relationUnlinkedModels = $relationMeta->getUnlinkedModels();

                if (array_key_exists($relationModelName, $connectedModelDiagramObjects) && !in_array($modelName, $relationUnlinkedModels)) {
                    continue;
                }

                if (!is_string($foreignKey)) {
                    $foreignKey = $foreignKey->getName();
                }

                $connection = new ModelDiagramConnection($modelName, $relationModelName);
                $connection->setColor($this->getRandomColor());
                $connection->setBeginFieldName($fieldName);
                $connection->setEndFieldName($foreignKey);

                $connections[] = $connection;
            }

            $connectedModelDiagramObjects[$modelName] = true;
        }

        return $connections;
    }

    /**
     * Gets the diagram objects out of the provided model array
     * @param array $models Array with models
     * @return array Array with ModelDiagramObjects of the provided models
     */
    protected function getDiagramObjects(array $models) {
        $objects = array();

        foreach ($models as $model) {
            $meta = $model->getMeta();
            $object = new ModelDiagramObject($meta);

            if ($this->willColorGroups()) {
                $group = $meta->getModelTable()->getGroup();
                $groupColor = $this->getGroupColor($group);

                $object->setBackgroundColor($groupColor);
            }

            $objects[$model->getName()] = $object;
        }

        return $objects;
    }

    /**
     * Gets the color for a group
     * @return zibo\library\image\Color
     */
    protected function getGroupColor($group) {
        if (!$group) {
            return new Color(255, 255, 255);
        }

        if (!array_key_exists($group, $this->groupColors)) {
            if ($this->colors) {
                $this->groupColors[$group] = array_shift($this->colors);
            } else {
                $this->groupColors[$group] = $this->getRandomColor();
            }
        }

        return $this->groupColors[$group];
    }

    /**
     * Generates a random color
     * @return zibo\library\image\Color
     */
    protected function getRandomColor() {
        $red = rand(11, 150);
        $green = rand(11, 150);
        $blue = rand(11, 150);

        return new Color($red, $green, $blue);
    }

}