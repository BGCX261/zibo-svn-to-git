<?php

namespace zibo\orm\scaffold\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\html\Image;
use zibo\library\orm\definition\ModelTable;
use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\orm\model\meta\ModelMeta;

/**
 * Decorator for a orm data object based on the data formats
 */
class DataDecorator implements Decorator {

    /**
     * Path to the default data image
     * @var string
     */
    const DEFAULT_IMAGE = 'web/images/orm/default.png';

    /**
     * Style class for the image of the data
     * @var string
     */
    const STYLE_IMAGE = 'data';

    /**
     * Meta of the data model
     * @var zibo\library\orm\model\meta\ModelMeta
     */
    private $meta;

    /**
     * URL where the title of the data will point to
     * @var string
     */
    private $action;

    /**
     * Constructs a new data decorator
     * @param zibo\library\orm\model\meta\ModelMeta$meta
     * @param string $action URL where the title of the data will point to
     * @return null
     */
    public function __construct(ModelMeta $meta, $action = null, $defaultImage = null) {
        if (!$defaultImage) {
            $defaultImage = self::DEFAULT_IMAGE;
        }

        $this->meta = $meta;
        $this->action = $action;
        $this->defaultImage = $defaultImage;

        $modelTable = $this->meta->getModelTable();
        $this->hasTeaserFormat = $modelTable->hasDataFormat(DataFormatter::FORMAT_TEASER);
    }

    /**
     * Decorates the data in the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array with the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $data = $cell->getValue();

        if (!$this->meta->isValidData($data)) {
            $cell->setValue('');
        }

        $title = $this->meta->formatData($data, DataFormatter::FORMAT_TITLE);

        $teaser = '';
        if ($this->hasTeaserFormat) {
            $teaser = $this->meta->formatData($data, DataFormatter::FORMAT_TEASER);
        }

        $value = $this->getImageHtml($data);

        if ($this->action) {
            if (!$title) {
                $title = $this->meta->getName() . ' ' . $data->id;
            }
            $anchor = new Anchor($title, $this->action . $data->id);
            $value .= $anchor->getHtml();
        } else {
            $value .= $title;
        }

        if ($teaser) {
            $value .= '<div class="info">' . $teaser . '</div>';
        }

        $cell->setValue($value);
    }

    /**
     * Gets the HTML for the image of the data
     * @param mixed $data
     * @return string
     */
    private function getImageHtml($data) {
        $modelTable = $this->meta->getModelTable();

        if (!$modelTable->hasDataFormat(DataFormatter::FORMAT_IMAGE)) {
            return '';
        }

        $image = $this->meta->formatData($data, DataFormatter::FORMAT_IMAGE);
        if ($image) {
            $image = new Image($image);
        } else {
            $image = new Image($this->defaultImage);
        }
        $image->setThumbnailer('crop', 50, 50);
        $image->appendToClass(self::STYLE_IMAGE);

        return $image->getHtml();
    }

}