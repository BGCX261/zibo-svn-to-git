<?php

namespace zibo\queue\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\i18n\translation\Translator;

/**
 * Table decorator to show the status of a job
 */
class StatusDecorator implements Decorator {

    /**
     * Translation key for the waiting status
     * @var string
     */
    const TRANSLATION_WAITING = 'queue.label.status.waiting';

    /**
     * Translation key for the progress status
     * @var string
     */
    const TRANSLATION_PROGRESS = 'queue.label.status.progress';

    /**
     * Translation key for the error status
     * @var string
     */
    const TRANSLATION_ERROR = 'queue.label.status.error';

    /**
     * The translator to use for the status messages
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new status decorator
     * @param zibo\library\i18n\translation\Translator $translator
     * @return null
     */
    public function __construct(Translator $translator) {
        $this->translator = $translator;
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

        $class = $data->getStatusClass();

        if ($data->isError) {
            $status = $this->translator->translate(self::TRANSLATION_ERROR);
        } elseif ($data->isInProgress) {
            $status = $this->translator->translate(self::TRANSLATION_PROGRESS);
        } else {
            $status = $this->translator->translate(self::TRANSLATION_WAITING);
        }

        $value = '<div class="' . $class . '"></div> ' . $status;

        $cell->setValue($value);
        $cell->appendToClass('status');
    }

}