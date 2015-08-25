<?php

namespace zibo\orm\scaffold\form\field\decorator;

use zibo\library\html\form\field\decorator\Decorator;
use zibo\library\orm\model\data\format\DataFormatter;
use zibo\library\orm\model\meta\ModelMeta;

class DataDecorator implements Decorator {

    /**
     * Model meta
     * @var zibo\library\orm\model\meta\ModelMeta
     */
    private $meta;

    /**
     * Constructs a new data decorator
     * @param zibo\library\orm\model\meta\ModelMeta $meta Model meta
     */
    public function __construct(ModelMeta $meta) {
        $this->meta = $meta;
    }

    /**
     * Decorates the data
     * @param mixed $data
     * @return string
     */
    public function decorate($data) {
        if (!$this->meta->isValidData($data)) {
            return '';
        }

        return $this->meta->formatData($data, DataFormatter::FORMAT_TITLE);
    }

}