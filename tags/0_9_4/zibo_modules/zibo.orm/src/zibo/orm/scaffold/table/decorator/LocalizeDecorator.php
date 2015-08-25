<?php

namespace zibo\orm\scaffold\table\decorator;

use zibo\admin\controller\LocalizeController;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\ActionDecorator;
use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\orm\model\Model;

/**
 * Decorator to view the localized state of data
 */
class LocalizeDecorator implements Decorator {

    /**
     * Style class for a unlocalized data row
     * @var string
     */
    const STYLE_UNLOCALIZED = 'unlocalized';

    /**
     * URL where the locale code should point to
     * @var string
     */
    private $action;

    /**
     * The localized model of the data
     * @var zibo\library\orm\model\LocalizedModel
     */
    private $localizedModel;

    /**
     * Array with the locale codes
     * @var array
     */
    private $locales;

    /**
     * The code of the current localize locale
     * @var string
     */
    private $currentLocale;

    /**
     * Constructs a new localize decorator
     * @param zibo\library\orm\model\Model $model Model of the data
     * @param string $action URL where the locale code should point to
     * @return null
     */
    public function __construct(Model $model, $action = null) {
        $this->action = $action;

        $this->meta = $model->getMeta();
        $this->localizedModel = $this->meta->getLocalizedModel();

        $this->currentLocale = LocalizeController::getLocale();
        $this->locales = I18n::getInstance()->getLocaleCodeList();
        unset($this->locales[$this->currentLocale]);
    }

    /**
     * Decorates the data into a locale overview
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row containing the cell
     * @param int $rowNumber Number of the current row
     * @param array $remainingValues Array with all the values of the remaining rows of the table
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $data = $cell->getValue();
        $value = '';

        if (!$this->meta->isValidData($data)) {
            $cell->setValue($value);
            return;
        }

        if (isset($data->dataLocale) && $data->dataLocale != $this->currentLocale) {
            $row->appendToClass(self::STYLE_UNLOCALIZED);
        }

        $ids = $this->localizedModel->getLocalizedIds($data->id);

        foreach ($this->locales as $locale) {
            if (array_key_exists($locale, $ids)) {
                $localeString = '<strong>' . $locale . '</strong>';
            } else {
                $localeString = $locale;
            }

            if ($this->action !== null) {
                $anchor = new Anchor($localeString, $this->action . '/' . $data->id. '/' . $locale);
                $localeString = $anchor->getHtml();
            }

            $value .= ($value == '' ? '' : ' ') . $localeString;
        }

        $cell->setValue($value);
    }

}