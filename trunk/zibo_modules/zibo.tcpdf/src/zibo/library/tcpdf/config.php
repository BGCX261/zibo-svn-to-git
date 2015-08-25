<?php

use zibo\core\Zibo;

use zibo\library\filesystem\File;

/**
 * Configuration file for the TCPDF library
 */

/**
 * Instance of Zibo
 * @var zibo\core\Zibo
 */
$zibo = Zibo::getInstance();

/**
 * The path to the files of the TCPDF library
 * @var string
 */
$vendorDirectory = __DIR__ . '/../../../../vendor/tcpdf/';

/**
 * The base path of the Zibo installation
 * @var zibo\library\filesystem\File
 */
$baseDirectory = $zibo->getRootPath();

/**
 * The path for the TCPDF cache
 * @var zibo\library\filesystem\File
 */
$cachePath = new File(Zibo::DIRECTORY_APPLICATION . '/' . Zibo::DIRECTORY_PUBLIC . '/tcpdf');

/**
 * The full directory of the TCPDF cache
 * @var zibo\library\filesystem\File
 */
$cacheDirectory = new File($baseDirectory, $cachePath);
$cacheDirectory->create();

/**
 * The base URL of the Zibo installation
 * @var string
 */
$baseUrl = $zibo->getRequest()->getBaseUrl();

/*
 * Define the configuration constants of TCPDF
 */

define('K_TCPDF_EXTERNAL_CONFIG', true);

define('K_PATH_MAIN', $vendorDirectory);

define ('K_PATH_URL', $baseUrl);

define ('K_PATH_FONTS', K_PATH_MAIN .'fonts/');

define ('K_PATH_CACHE', $cacheDirectory->getPath() . '/');

define ('K_PATH_URL_CACHE', $baseUrl . '/' . $cachePath . '/');

define ('K_PATH_IMAGES', K_PATH_MAIN . 'images/');

define ('K_BLANK_IMAGE', K_PATH_IMAGES . '_blank.png');

define ('PDF_PAGE_FORMAT', 'A4');

define ('PDF_PAGE_ORIENTATION', 'P');

define ('PDF_CREATOR', 'TCPDF');

define ('PDF_AUTHOR', 'TCPDF');

define ('PDF_HEADER_TITLE', 'TCPDF Example');

define ('PDF_HEADER_STRING', "by Nicola Asuni - Tecnick.com\nwww.tcpdf.org");

define ('PDF_HEADER_LOGO', 'tcpdf_logo.jpg');

define ('PDF_HEADER_LOGO_WIDTH', 30);

define ('PDF_UNIT', 'mm');

define ('PDF_MARGIN_HEADER', 5);

define ('PDF_MARGIN_FOOTER', 10);

define ('PDF_MARGIN_TOP', 27);

define ('PDF_MARGIN_BOTTOM', 25);

define ('PDF_MARGIN_LEFT', 15);

define ('PDF_MARGIN_RIGHT', 15);

define ('PDF_FONT_NAME_MAIN', 'helvetica');

define ('PDF_FONT_SIZE_MAIN', 10);

define ('PDF_FONT_NAME_DATA', 'helvetica');

define ('PDF_FONT_SIZE_DATA', 8);

define ('PDF_FONT_MONOSPACED', 'courier');

define ('PDF_IMAGE_SCALE_RATIO', 1.25);

define('HEAD_MAGNIFICATION', 1.1);

define('K_CELL_HEIGHT_RATIO', 1.25);

define('K_TITLE_MAGNIFICATION', 1.3);

define('K_SMALL_RATIO', 2/3);

define('K_THAI_TOPCHARS', true);

define('K_TCPDF_CALLS_IN_HTML', true);

require_once K_PATH_MAIN . 'tcpdf.php';
