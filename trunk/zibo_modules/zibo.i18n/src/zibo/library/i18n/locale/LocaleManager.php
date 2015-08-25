<?php

namespace zibo\library\i18n\locale;

use zibo\library\i18n\exception\LocaleNotFoundException;
use zibo\library\i18n\locale\negotiator\Negotiator;
use zibo\library\i18n\locale\io\LocaleIO;
use zibo\library\String;

use zibo\ZiboException;

use \InvalidArgumentException;

/**
 * Manager of the locales
 */
class LocaleManager {

    /**
     * The loaded locales
     * @var array
     */
    protected $locales;

    /**
     * The current locale
     * @var string
     */
    protected $currentLocale;

    /**
     * The locale input/ouput implementation
     * @var zibo\library\i18n\locale\io\LocaleIO
     */
    protected $io;

    /**
     * The negotiator which is being used
     * @var zibo\library\i18n\locale\negotiator\Negotiator
     */
    protected $negotiator;

    /**
     * Constructs a new Locale manager
     * @param zibo\library\i18n\locale\io\LocaleIO $io
     * @param zibo\library\i18n\locale\negotiator\Negotiator $negotiator
     * @return null
     */
    public function __construct(LocaleIO $io, Negotiator $negotiator = null) {
        $this->io = $io;
        $this->negotiator = $negotiator;
    }

    /**
     * Checks if the provided locale is available
     * @param string $code The code of the locale
     * @return boolean
     */
    public function hasLocale($code) {
        if (!String::isString($code, String::NOT_EMPTY)) {
            throw new ZiboException('Provided code is empty or invalid');
        }

        $this->initLocales();

        return isset($this->locales[$code]);
    }

    /**
     * Gets a locale
     * @param string $code if not provided, the current locale will be returned
     * @return zibo\library\i18n\locale\Locale
     *
     * @uses zibo\library\i18n\locale\io\LocaleIO::getLocale()
     */
    public function getLocale($code = null) {
        $this->initLocales();

        if ($code === null) {
            if (!isset($this->currentLocale)) {
                $this->initCurrentLocale();
            }

            $code = $this->currentLocale;
        } elseif (!String::isString($code, String::NOT_EMPTY)) {
            throw new ZiboException('Provided code is invalid');
        }

        if (!isset($this->locales[$code])) {
            throw new LocaleNotFoundException($code);
        }

        return $this->locales[$code];
    }

    /**
     * Gets all locales
     * @return array Array with the locale code as key and the Locale as value
     */
    public function getLocales() {
        $this->initLocales();

        return $this->locales;
    }

    /**
     * Gets the default locale.
     *
     * The default locale is the first locale. The order of the locales can be
     * defined with setOrder().
     *
     * @return zibo\library\i18n\locale\Locale the default locale
     *
     * @throws zibo\ZiboException when no locales could be found
     * @see setOrder()
     */
    public function getDefaultLocale() {
        $locales = $this->getLocales();
        if (!$locales) {
            throw new ZiboException('No locales defined in this Zibo installation');
        }

        return array_shift($locales);
    }

    /**
     * Sets the order of the locales
     * @param string|array $order A comma separated string or an array of
     * locale codes
     * @return null
     */
    public function setOrder($order = null) {
        if (is_string($order)) {
            $order = explode(',', $order);
        } elseif (!is_array($order) && $order !== null) {
            throw new ZiboException('Invalid order provided');
        }

        $this->getLocales();

        uasort($this->locales, function(Locale $a, Locale $b) use ($order) {
            $aIndex = array_search($a->getCode(), $order);
            $bIndex = array_search($b->getCode(), $order);

            if ($aIndex === $bIndex) {
                return 0;
            } else if ($aIndex === false) {
                return 1;
            } else if ($bIndex === false) {
                return -1;
            } else {
                return ($aIndex < $bIndex) ? -1 : 1;
            }
        });
    }

    /**
     * Gets the preferred order of the locales
     * @return array Array with locale codes
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Sets the current locale
     * @param string|zibo\library\i18n\locale\Locale $locale
     * @return null
     */
    public function setCurrentLocale($locale) {
        if (!$locale instanceof Locale) {
            $locale = $this->getLocale($locale);
        }

        $code = $locale->getCode();

        setlocale(LC_ALL, $code);
        $this->currentLocale = $code;
    }

    /**
     * Initiates the current locale.
     *
     * First the configured locale negotiator is used. If locale negotiation fails then the default locale
     * is used.
     * @return null
     */
    private function initCurrentLocale() {
        $locale = null;

        if ($this->negotiator) {
            $locale = $this->negotiator->getLocale($this);
        }

        if (!$locale) {
            $locale = $this->getDefaultLocale();
        }

        $this->setCurrentLocale($locale);
    }

    /**
     * Initializes the available locales
     * @return null
     */
    private function initLocales() {
        if (!isset($this->locales)) {
            $this->locales = $this->io->getLocales();
        }
    }

}