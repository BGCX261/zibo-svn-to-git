<?php

namespace joppa\advertising\table\decorator;

use joppa\advertising\model\data\AdvertisementData;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\html\Image;
use zibo\library\i18n\I18n;
use zibo\library\String;

use \Exception;

/**
 * Table decorator for an advertisement
 */
class AdvertisementDecorator implements Decorator {

	/**
	 * Translation key for the display label
	 * @var string
	 */
	const TRANSLATION_DISPLAY = 'joppa.advertising.label.display';

	/**
	 * Translation key for the clicks label
	 * @var string
	 */
	const TRANSLATION_CLICKS = 'joppa.advertising.label.clicks';

	/**
	 * URL to the detail action of the advertisement
	 * @var string
	 */
    private $action;

    /**
     * Current locale
     * @var zibo\library\i18n\locale\Locale
     */
    private $locale;

    /**
     * Translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new advertisement decorator
     * @param string $action URL to the detail of the advertisement
     * @return null
     */
    public function __construct($action) {
        $this->action = $action;

        $i18n = I18n::getInstance();
        $this->locale = $i18n->getLocale();
        $this->translator = $i18n->getTranslator();
    }

    /**
     * Decorates a cell which contains an Advertisement object
     * @param zibo\library\html\table\Cell $cell
     * @param zibo\library\html\table\Row $row
     * @param int $rowNumber
     * @param array $remainingValues
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $advertisement = $cell->getValue();

        if (!($advertisement instanceof AdvertisementData)) {
        	return;
        }

        $cell->appendToClass('advertisement');

        try {
            $image = new Image($advertisement->image);
            $image->appendToClass('data');
            $image->setThumbnailer('crop', 50, 50);
            $value = $image->getHtml();
        } catch (Exception $e) {
            $value = 'Could not load image: ' . $e->getMessage();
        }

        $anchor = new Anchor($advertisement->name, $this->action . $advertisement->id);
        $value .= $anchor->getHtml();

        if (!$advertisement->clicks) {
        	$advertisement->clicks = '0';
        }

        $translateParams = array(
            'from' => $this->locale->formatDate($advertisement->dateStart),
            'till' => $this->locale->formatDate($advertisement->dateStop),
            'clicks' => $advertisement->clicks,
        );

        $value .= '<div class="info">';
        $value .= $advertisement->website . '<br />';
        $value .= $this->translator->translate(self::TRANSLATION_DISPLAY, $translateParams) . '<br />';
        $value .= $this->translator->translate(self::TRANSLATION_CLICKS, $translateParams);
        $value .= '</div>';

        $cell->setValue($value);
    }

}