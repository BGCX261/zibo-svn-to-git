<?php

namespace zibo\library\excel\format;

/**
 * Factory for some default formats
 */
class FormatFactory {

    /**
     * Create the default cell format
     * @return Format default cell format
     */
    public function createDefaultFormat() {
        return new Format();
    }

    /**
     * Create the bold cell format
     * @return Format bold cell format
     */
    public function createBoldFormat() {
        $format = $this->createDefaultFormat();
        $format->setTextWeight(Format::WEIGHT_BOLD);

        return $format;
    }

    /**
     * Create the title cell format
     * @return Format title cell format
     */
    public function createTitleFormat() {
        $format = $this->createDefaultFormat();
        $format->setAlign(Format::ALIGN_LEFT);
        $format->setTextSize(12);
        $format->setTextWeight(Format::WEIGHT_BOLD);

        return $format;
    }

    /**
     * Create the subtitle cell format
     * @return Format subtitle cell format
     */
    public function createSubtitleFormat() {
        $format = $this->createTitleFormat();
        $format->setTextSize(10);

        return $format;
    }

}