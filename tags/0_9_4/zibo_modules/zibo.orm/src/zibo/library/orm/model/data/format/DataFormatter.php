<?php

namespace zibo\library\orm\model\data\format;

/**
 * Formatter for model data
 */
class DataFormatter {

    /**
     * Name of the title format
     * @var string
     */
    const FORMAT_TITLE = 'title';

    /**
     * Name of the teaser format
     * @var string
     */
    const FORMAT_TEASER = 'teaser';

    /**
     * Name of the image format
     * @var string
     */
    const FORMAT_IMAGE = 'image';

    /**
     * Name of the image format
     * @var string
     */
    const FORMAT_DATE = 'date';

    /**
     * Used data formats
     * @var array
     */
    private $formats;

    /**
     * Construct a new data formatter
     * @return null;
     */
    public function __construct() {
        $this->formats = array();
    }

    /**
     * Format the data with the provided format
     * @param mixed $data Model data object
     * @param string $format The format string
     * @return mixed A human readable string or a certain value of the data
     */
    public function formatData($data, $format) {
        if (!array_key_exists($format, $this->formats)) {
            $this->formats[$format] = new DataFormat($format);
        }

        return $this->formats[$format]->formatData($data);
    }

}