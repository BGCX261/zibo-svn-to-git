<?php

namespace zibo\library\i18n\locale;

use zibo\core\Zibo;

use zibo\library\ObjectFactory;

use zibo\ZiboException;

use \InvalidArgumentException;

/**
 * Manager of the locales
 */
class Manager {

    /**
     * Configuration key for the locale input/output implementation
     * @var string
     */
    const CONFIG_IO = 'i18n.locale.io';

    /**
     * Configuration key for the locale order
     * @var string
     */
    const CONFIG_LOCALE_ORDER = 'i18n.locale.order';

    /**
     * Configuration key for the negotiator class
     * @var string
     */
    const CONFIG_NEGOTIATOR = 'i18n.locale.negotiator';

    /**
     * Class name of the default locale input/output implementation
     * @var unknown_type
     */
    const CLASS_IO = 'zibo\\library\\i18n\\locale\\io\\ConfigLocaleIO';

    /**
     * Class name of the default negotiator implementation
     * @var string
     */
    const CLASS_NEGOTIATOR = 'zibo\\library\\i18n\\locale\negotiator\\HttpNegotiator';

    /**
     * Class name of the Locale input/output interface
     * @var unknown_type
     */
    const INTERFACE_IO = 'zibo\\library\\i18n\\locale\\io\\LocaleIO';

    /**
     * Class name of the negotiator interface
     * @var string
     */
    const INTERFACE_NEGOTIATOR = 'zibo\\library\\i18n\\locale\negotiator\\Negotiator';

    /**
     * The loaded locales
     * @var array
     */
    protected $locales;

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
     * The current locale
     * @var zibo\library\i18n\locale\Locale
     */
    protected $currentLocale;

    /**
     * Constructs a new Locale manager
     * @uses zibo\library\ObjectFactory::createFromConfig()
     */
    public function __construct() {
        $objectFactory = new ObjectFactory();
        $this->io = $objectFactory->createFromConfig(self::CONFIG_IO, self::CLASS_IO, self::INTERFACE_IO);
    }

    /**
     * Gets a locale
     * @param string $code if not provided, the current locale will be returned
     * @return zibo\library\i18n\locale\Locale
     *
     * @uses zibo\library\i18n\locale\io\LocaleIO::getLocale()
     */
    public function getLocale($code = null) {
        if ($code === null) {
            if (!isset($this->currentLocale)) {
                $this->initCurrentLocale();
            }
            return $this->currentLocale;
        } else if (is_string($code)) {
            return $this->io->getLocale($code);
        } else {
            throw new InvalidArgumentException('Expected argument $code of type string, got type: ' . gettype($code));
        }
    }

    /**
     * Gets all locales
     * @param null|array $order
     * @return array a sorted list of locales
     *
     * @uses zibo\library\i18n\locale\io\LocaleIO::getAllLocales()
     */
    public function getAllLocales(array $order = null) {
        $locales = $this->io->getAllLocales();

        if (is_null($order)) {
            $order = $this->getOrder();
        }

        if (empty($order)) {
            return $locales;
        }

        uasort($locales, function(Locale $a, Locale $b) use ($order) {
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

        return $locales;
    }

    /**
     * Gets the default locale.
     *
     * The default locale is the first preferred locale that exists.
     *
     * @return zibo\library\i18n\locale\Locale the default locale
     * @uses Manager::getOrder()
     * @uses zibo\library\i18n\locale\IO::getLocale()
     *
     * @throws zibo\ZiboException when no locales could be found
     */
    public function getDefaultLocale() {
        $order = $this->getOrder();

        foreach ($order as $code) {
            $locale = $this->io->getLocale($code);
            if ($locale) {
                return $locale;
            }
        }

        throw new ZiboException('No locales defined in this Zibo installation');
    }

    /**
     * Gets the preferred order of the locales
     * @return array Array with locale codes
     */
    public function getOrder() {
        $orderConfig = Zibo::getInstance()->getConfigValue(self::CONFIG_LOCALE_ORDER);

        if (empty($orderConfig)) {
            $order = array();

            $locales = $this->io->getAllLocales();
            foreach ($locales as $locale) {
                $order[] = $locale->getCode();
            }
        } else {
            $order = explode(',', $orderConfig);
        }

        return $order;
    }

    /**
     * Sets the current locale
     * @param zibo\library\i18n\locale\Locale $locale
     * @return null
     */
    public function setCurrentLocale(Locale $locale) {
        setlocale(LC_ALL, $locale->getCode());
        $this->currentLocale = $locale;
    }

    /**
     * Initiates the current locale.
     *
     * First the configured locale negotiator is used. If locale negotiation fails then the default locale
     * is used.
     *
     * @see zibo\library\i18n\I18n::getNegotiator()
     * @see zibo\library\i18n\locale\Manager::getDefaultLocale()
     */
    private function initCurrentLocale() {
        $negotiator = $this->getNegotiator();
        $locale = $negotiator->getLocale($this->io);

        if (!$locale) {
            $locale = $this->getDefaultLocale();
        }

        $this->setCurrentLocale($locale);
    }

    /**
     * Gets the negotiator implementation to use for locale negotiation.
     *
     * You can configure which class is being used with the configuration key "i18n.negotiator".
     *
     * @return zibo\library\i18n\locale\negotiator\Negotiator
     */
    private function getNegotiator() {
        if (!$this->negotiator) {
            $objectFactory = new ObjectFactory();
            $this->negotiator = $objectFactory->createFromConfig(self::CONFIG_NEGOTIATOR, self::CLASS_NEGOTIATOR, self::INTERFACE_NEGOTIATOR);
        }

        return $this->negotiator;
    }

}