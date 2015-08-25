<?php

namespace zibo\queue\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\i18n\I18n;

/**
 * Table decorator to show the detail of a job
 */
class DetailDecorator implements Decorator {

    /**
     * Translation key for the info label
     * @var string
     */
    const TRANSLATION_INFO = 'queue.label.added.detail';

    /**
     * Translation key for the info label with schedule date
     * @var string
     */
    const TRANSLATION_INFO_SCHEDULED = 'queue.label.added.detail.scheduled';

    /**
     * The URL for the action behind this decorator
     * @var string
     */
    private $action;

    /**
     * The current locale to format the dates
     * @var zibo\library\i18n\locale\Locale
     */
    private $locale;

    /**
     * The translator for the labels
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new queue detail decorator
     * @param string $action URL for the action behind this decorator
     * @return null
     */
    public function __construct($action) {
        $this->action = $action;

        $i18n = I18n::getInstance();

        $this->locale = $i18n->getLocale();
        $this->translator = $i18n->getTranslator();
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

        $title = '#' . $data->id . ': ' . $data->getJobClassName();

        $anchor = new Anchor($title, $this->action . $data->id);

        $translationVars = array(
            'queue' => $data->queue,
            'date' => $this->locale->formatDate($data->dateAdded, 'j F Y H:i:s'),
        );

        if ($data->dateScheduled) {
            $translationKey = self::TRANSLATION_INFO_SCHEDULED;
            $translationVars['scheduled'] = $this->locale->formatDate($data->dateScheduled, 'j F Y H:i:s');
        } else {
            $translationKey = self::TRANSLATION_INFO;
        }

        $value = $anchor->getHtml();
        $value .= '<div class="info">' . $this->translator->translate($translationKey, $translationVars) . '</div>';

        $cell->setValue($value);
    }

}