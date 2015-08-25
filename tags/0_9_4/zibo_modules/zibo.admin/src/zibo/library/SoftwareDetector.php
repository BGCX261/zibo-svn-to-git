<?php

namespace zibo\library;

use zibo\core\Request;
use zibo\core\Zibo;

/**
 * Detector for the software of the client, based on the user agent string
 */
class SoftwareDetector {

    /**
     * Amaya browser
     * @var string
     */
    const BROWSER_AMAYA = 'Amaya';

    /**
     * Chrome browser
     * @var string
     */
    const BROWSER_CHROME = 'Chrome';

    /**
     * Firebird browser
     * @var string
     */
    const BROWSER_FIREBIRD = 'Firebird';

    /**
     * Firefox browser
     * @var string
     */
    const BROWSER_FIREFOX = 'Firefox';

    /**
     * Galeon browser
     * @var string
     */
    const BROWSER_GALEON = 'Galeon';

    /**
     * iCab browser
     * @var string
     */
    const BROWSER_ICAB = 'iCab';

    /**
     * Internet Explorer browser
     * @var string
     */
    const BROWSER_INTERNET_EXPLORER = 'Internet Explorer';

    /**
     * Internet Explorer (Pocket) browser
     * @var string
     */
    const BROWSER_INTERNET_EXPLORER_POCKET = 'Internet Explorer (Pocket)';

    /**
     * Konqueror browser
     * @var string
     */
    const BROWSER_KONQUEROR = 'Konqueror';

    /**
     * Lynx browser
     * @var string
     */
    const BROWSER_LYNX = 'Lynx';

    /**
     * Mozilla browser
     * @var string
     */
    const BROWSER_MOZILLA = 'Mozilla';

    /**
     * Netscape browser
     * @var string
     */
    const BROWSER_NETSCAPE = 'Netscape';

    /**
     * NetPositive browser
     * @var string
     */
    const BROWSER_NETPOSITIVE = 'NetPositive';

    /**
     * OmniWeb browser
     * @var string
     */
    const BROWSER_OMNIWEB = 'Omniweb';

    /**
     * Opera browser
     * @var string
     */
    const BROWSER_OPERA = 'Opera';

    /**
     * Phoenix browser
     * @var string
     */
    const BROWSER_PHOENIX = 'Phoenix';

    /**
     * Safari browser
     * @var string
     */
    const BROWSER_SAFARI = 'Safari';

    /**
     * Macintoch operating system
     * @var string
     */
    const OS_MAC = 'Macintosh';

    /**
     * Linux operating system
     * @var string
     */
    const OS_LINUX = 'Linux';

    /**
     * Windows operating system
     * @var string
     */
    const OS_WINDOWS = 'Windows';

    /**
     * Search engine
     * @var string
     */
    const OS_SEARCH_ENGINE = 'Search engine';

    /**
     * Unknown software
     * @var string
     */
    const UNKNOWN = 'Unknown';

    /**
     * The name of the browser
     * @var string
     */
    private $browserName;

    /**
     * The version of the browser
     * @var string
     */
    private $browserVersion;

    /**
     * The name of the operating system
     * @var string
     */
    private $osName;

    /**
     * The version of the operating system
     * @var string
     */
    private $osVersion;

    /**
     * The version of PHP
     * @var string
     */
    private $phpVersion;

    /**
     * The loaded PHP extensions
     * @var array
     */
    private $phpExtensions;

    /**
     * The user agent string used for detection
     * @var string
     */
    private $userAgent;

    /**
     * Constructs a new software detector
     * @param string $userAgent Custom user agent string. If not provided the user agent from the request will be used
     * @return null
     */
    public function __construct($userAgent = null) {
        if (empty($userAgent)) {
            $request = Zibo::getInstance()->getRequest();
            $userAgent = $request->getHeader(Request::HEADER_USER_AGENT);

            if (!$userAgent) {
                $userAgent = self::UNKNOWN;
                $this->osName = self::UNKNOWN;
                $this->browserName = self::UNKNOWN;
            }
        }

        $this->userAgent = $userAgent;
    }

    /**
     * Gets the version of PHP
     * @return string
     */
    public function getPhpVersion() {
        $this->detectPhp();
        return $this->phpVersion;
    }

    /**
     * Gets the loaded extension of PHP
     * @return array
     */
    public function getPhpExtensions() {
        $this->detectPhp();
        return $this->phpExtensions;
    }

    /**
     * Gets the name of the browser
     * @return string
     */
    public function getBrowserName() {
        $this->detectSoftware();
        return $this->browserName;
    }

    /**
     * Gets the version of the browser
     * @return string
     */
    public function getBrowserVersion() {
        $this->detectSoftware();
        return $this->browserVersion;
    }

    /**
     * Gets the name of the operating system
     * @return string
     */
    public function getOperatingSystemName() {
        $this->detectSoftware();
        return $this->osName;
    }

    /**
     * Gets the version of the operating system
     * @return string
     */
    public function getOperatingSystemVersion() {
        $this->detectSoftware();
        return $this->osVersion;
    }

    /**
     * Gets the user agent string used by this detector
     * @return string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * Checks if the user is using Windows
     * @return boolean
     */
    public function isWindows() {
        return $this->checkOperatingSystem(self::OS_WINDOWS);
    }

    /**
     * Checks if the user is using Linux
     * @return boolean
     */
    public function isLinux() {
        return $this->checkOperatingSystem(self::OS_LINUX);
    }

    /**
     * Checks if the user is using Macintoch
     * @return boolean
     */
    public function isMac() {
        return $this->checkOperatingSystem(self::OS_MAC);
    }

    /**
     * Checks if the user is a search engine
     * @return boolean
     */
    public function isSearchEngine() {
        return $this->checkOperatingSystem(self::OS_SEARCH_ENGINE);
    }

    /**
     * Checks if the user is using Chrome
     * @return boolean
     */
    public function isChrome() {
        return $this->checkBrowser(self::BROWSER_CHROME);
    }

    /**
     * Checks if the user is using Internet Explorer
     * @return boolean
     */
    public function isInternetExplorer() {
        return $this->checkBrowser(self::BROWSER_INTERNET_EXPLORER);
    }

    /**
     * Checks if the user is using Firefox
     * @return boolean
     */
    public function isFirefox() {
        return $this->checkBrowser(self::BROWSER_FIREFOX);
    }

    /**
     * Checks if the user is using Opera
     * @return boolean
     */
    public function isOpera() {
        return $this->checkBrowser(self::BROWSER_OPERA);
    }

    /**
     * Checks if the user is using Safari
     * @return boolean
     */
    public function isSafari() {
        return $this->checkBrowser(self::BROWSER_SAFARI);
    }

    /**
     * Checks if the user is using the provided operating system
     * @param string $name Name of the operating system (use the class constants)
     * @return boolean
     */
    private function checkOperatingSystem($name) {
        $this->detectSoftware();

        if ($this->osName != $name) {
            return false;
        }

        if ($this->osVersion) {
            return $this->osVersion;
        }

        return true;
    }

    /**
     * Checks if a specified browser is used
     * @param string $name Name of the browser (use the class constants)
     * @return boolean|string The version of the browser, if detected. Otherwise a boolean to see if the provided browser is used
     */
    private function checkBrowser($name) {
        $this->detectSoftware();

        if ($this->browserName != $name) {
            return false;
        }

        if ($this->browserVersion) {
            return $this->browserVersion;
        }

        return true;
    }

    /**
     * Detects the PHP version and extensions
     * @return null
     */
    private function detectPhp() {
        if ($this->phpVersion) {
            return;
        }

        $this->phpVersion = phpversion();
        $this->phpExtensions = array();

        $extensions = get_loaded_extensions();
        foreach ($extensions as $extension) {
            $extension = strtolower($extension);
            $settings = ini_get_all($extension, false);
            if ($extension != 'core' && count($settings) > 50) {
                $settings = array();
            }

            $info = array(
                'version' => phpversion($extension),
                'settings' => $settings,
            );
            $this->phpExtensions[$extension] = $info;
        }

        ksort($this->phpExtensions);
    }

    /**
     * Detects the software through the user agent string
     * @return null
     */
    private function detectSoftware() {
        if ($this->osName) {
            return;
        }

        $this->detectOperatingSystem();
        $this->detectBrowser();
    }

    /**
     * Detects the operating system through the user agent string
     * @return null
     */
    private function detectOperatingSystem() {
        $list = array (
            'Win16' => array(
                'name' => self::OS_WINDOWS,
                'version' => '3.11',
            ),
            '(Windows 95)|(Win95)|(Windows_95)' => array(
                'name' => self::OS_WINDOWS,
                'version' => '95',
            ),
            '(Windows 98)|(Win98)' => array(
                'name' => self::OS_WINDOWS,
                'version' => '98',
            ),
            '(Windows NT 5.0)|(Windows 2000)' => array(
                'name' => self::OS_WINDOWS,
                'version' => '2000',
            ),
            '(Windows NT 5.1)|(Windows XP)' => array(
                'name' => self::OS_WINDOWS,
                'version' => 'XP',
            ),
            '(Windows NT 5.2)' => array(
                'name' => self::OS_WINDOWS,
                'version' => 'Server 2003',
            ),
            '(Windows NT 6.0)' => array(
                'name' => self::OS_WINDOWS,
                'version' => 'Vista',
            ),
            '(Windows NT 7.0)' => array(
                'name' => self::OS_WINDOWS,
                'version' => '7',
            ),
            '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)' => array(
                'name' => self::OS_WINDOWS,
                'version' => 'NT 4.0',
            ),
            '(pocket)|(mspie)' => array(
                'name' => self::OS_WINDOWS,
                'version' => 'CE',
            ),
            'Windows ME' => array(
                'name' => self::OS_WINDOWS,
                'version' => 'ME',
            ),
            '(Linux)|(X11)' => array(
                'name' => self::OS_LINUX,
                'version' => '',
            ),
            'Macintosh' => array(
                'name' => self::OS_MAC,
                'version' => '',
            ),
            'Mac_PowerPC' => array(
                'name' => self::OS_MAC,
                'version' => '',
            ),
            '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(' . preg_quote('Ask Jeeves/Teoma', '/') . ')|(ia_archiver)' => array(
                'name' => self::OS_SEARCH_ENGINE,
                'version' => '',
            ),
        );

        foreach ($list as $regex => $os) {
            if (preg_match("/$regex/i", $this->userAgent)) {
                $this->osName = $os['name'];
                $this->osVersion = $os['version'];
                break;
            }
        }

        if (!$this->osName) {
            $this->osName = self::UNKNOWN;
        }
    }

    /**
     * Detects the browser through the user agent string
     * @return null
     */
    private function detectBrowser() {
        if (preg_match('/opera/i', $this->userAgent)) {
            // Opera
            $this->browserName = self::BROWSER_OPERA;
            $val = stristr($this->userAgent, 'opera');
            if (strpos($val, '/') !== false) {
                $val = explode('/', $val);
                $val = explode(' ', $val[1]);
                $this->browserVersion = $val[0];
            } else {
                $val = explode(' ',stristr($val, 'opera'));
                $this->browserVersion = $val[1];
            }
        } elseif (preg_match('/Chrome/i', $this->userAgent)) {
            $val = stristr($this->userAgent, 'Chrome');
            $val = explode('/', $val);
            $val = explode(' ', $val[1]);
            $this->browserName = self::BROWSER_CHROME;
            $this->browserVersion = $val[0];
        } elseif (preg_match('/Firefox/i', $this->userAgent)) {
            $val = stristr($this->userAgent, 'Firefox');
            $val = explode('/', $val);
            $this->browserName = self::BROWSER_FIREFOX;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/Konqueror/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, '/Konqueror/i'));
            $val = explode('/', $val[0]);
            $this->browserName = self::BROWSER_KONQUEROR;
            $this->browserVersion = str_replace(')', '', $val[1]);
        } elseif (preg_match('/microsoft internet explorer/i', $this->userAgent)) {
            $this->browserName = self::BROWSER_INTERNET_EXPLORER;
            $this->browserVersion = '1.0';
            $var = stristr($this->userAgent, '/');
            if (ereg('308|425|426|474|0b1', $var)) {
                $this->browserVersion = '1.5';
            }
        } elseif (preg_match('/msie/i', $this->userAgent)) { // && !preg_match('/opera/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, 'msie'));
            $this->browserName = self::BROWSER_INTERNET_EXPLORER;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/galeon/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, 'galeon'));
            $val = explode('/', $val[0]);
            $this->browserName = self::BROWSER_GALEON;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/NetPositive/i', $this->userAgent)) {
            $val = explode('/', stristr($this->userAgent, 'NetPositive'));
            $this->browserName = self::BROWSER_NETPOSITIVE;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/mspie/i', $this->userAgent) || preg_match('/pocket/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, 'mspie'));
            $this->browserName = self::BROWSER_INTERNET_EXPLORER_POCKET;
            if (preg_match('/mspie/i', $this->userAgent)) {
                $this->browserVersion = $val[1];
            } else {
                $val = explode('/', $this->userAgent);
                $this->browserVersion = $val[1];
            }
        } elseif (preg_match('/icab/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, 'icab'));
            $this->browserName = self::BROWSER_ICAB;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/omniweb/i', $this->userAgent)) {
            $val = explode('/', stristr($this->userAgent, 'omniweb'));
            $this->browserName = self::BROWSER_OMNIWEB;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/phoenix/i', $this->userAgent)) {
            $val = explode('/', stristr($this->userAgent,'Phoenix/'));
            $this->browserName = self::BROWSER_PHOENIX;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/firebird/i', $this->userAgent)) {
            $val = stristr($this->userAgent, 'Firebird');
            $val = explode('/', $val);
            $this->browserName = self::BROWSER_FIREBIRD;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/mozilla/i', $this->userAgent) && preg_match('/rv:[0-9][.][0-9][a-b]/i', $this->userAgent) && !preg_match('/netscape/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, 'rv:'));
            preg_match('rv:[0-9][.][0-9][a-b]',$this->userAgent,$val);
            $this->browserName = self::BROWSER_MOZILLA;
            $this->browserVersion = str_replace('rv:', '', $val[0]);
        } elseif(preg_match('/mozilla/i', $this->userAgent) && preg_match('/rv:[0-9][.][0-9]/i', $this->userAgent) && !preg_match('/netscape/i',$this->userAgent)) {
            $this->browserName = self::BROWSER_MOZILLA;
            $val = explode(' ', stristr($this->userAgent, 'rv:'));
            preg_match('/rv:[0-9][.][0-9][.][0-9]/i',$this->userAgent,$val);
            $this->browserVersion = str_replace('rv:','',$val[0]);
        } elseif (preg_match('/libwww/i', $this->userAgent)) {
            if (preg_match('/amaya/i', $this->userAgent)) {
                $val = explode('/', stristr($this->userAgent, 'amaya'));
                $val = explode(' ', $val[1]);
                $this->browserName = self::BROWSER_AMAYA;
                $this->browserVersion = $val[0];
            } else {
                $val = explode('/', $this->userAgent);
                $this->browserName = self::BROWSER_LYNX;
                $this->browserVersion = $val[1];
            }
        } elseif (preg_match('/safari/i', $this->userAgent)) {
            $this->browserName = self::BROWSER_SAFARI;
            $this->browserVersion = '';
        } elseif (preg_match('/netscape/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, 'netscape'));
            $val = explode('/', $val[0]);
            $this->browserName = self::BROWSER_NETSCAPE;
            $this->browserVersion = $val[1];
        } elseif (preg_match('/mozilla/i', $this->userAgent) && !preg_match('/rv:[0-9][.][0-9][.][0-9]/i', $this->userAgent)) {
            $val = explode(' ', stristr($this->userAgent, 'mozilla'));
            $val = explode('/', $val[0]);
            $this->browserName = self::BROWSER_NETSCAPE;
            $this->browserVersion = $val[1];
        }

        if (!$this->browserName) {
            $this->browserName = self::UNKNOWN;
        }
        $this->browserVersion = str_replace(';', '', $this->browserVersion);
    }

}