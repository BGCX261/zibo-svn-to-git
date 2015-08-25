<?php

/**
 *
 * Script to automate the download and unpacking of the latest version of Zibo.
 *
 */

/**
 * The URL of the Zibo package
 * @var string
 */
const PACKAGE = 'http://repository.zibo.be/public/zibo-current.zip';

//
// # Uncomment and set the BASE_URL constant if the system is not able to determine the base URL.
//
///**
// * The base URL where this script resides
// * @var string
// */
//const BASE_URL = 'http://localhost/zibo';

//
// # Uncomment and set the INSTALL_DIR constant if you don't want to use the path where the script is located.
//
///**
// * The path to the installation directory
// * @var string
// */
//const INSTALL_DIR = '/var/www/zibo';

/*
 * Script implementation from now on
 */

/**
 * Runs this installation script
 * @return null
 */
function run() {
    $action = null;
    if (array_key_exists('action', $_GET)) {
        $action = $_GET['action'];
    }

    try {
        $baseUrl = getBaseUrl();
        $url = getBaseUrl() . str_replace(__DIR__, '', __FILE__);

        $content = '<p>This script will help you download and launch the latest Zibo installation.</p>';

        $requirements = checkRequirements();
        if ($requirements) {
            $content .= '<div class="error">';
            $content .= '<p>Not all requirements are met, please solve the following issues:</p>';
            $content .= '<ul>';
            foreach ($requirements as $requirement) {
                $content .= '<li>' . $requirement . '</li>';
            }
            $content .= '</ul>';
            $content .= '</div>';
            $content .= '<form action="' . $url . '"><input type="submit" value="Check again" /></form>';
        } else {
            if (!$action) {
                $content .= '<p class="success">All the requirements are met.</p>';
                $content .= '<p>The script is ready to download Zibo.</p>';
                $content .= '<p>Zibo download:<br /><code><a href="' . PACKAGE . '">' . PACKAGE . '</a></code></p>';
                $content .= '<p>Installation directory:<br /><code>' . getInstallationDirectory() . '</code></p>';
                $content .= '<form action="' . $url . '?action=download" method="post"><input type="submit" value="Proceed" /></form>';
            } elseif ($action == 'download') {
                downloadAndUnpack();
                header('Location: ' . $baseUrl);
            }
        }
    } catch (Exception $exception) {
        $content = '<div class="error"><pre>';
        $content .= $exception->getMessage() . "\n" . $exception->getTraceAsString();
        $content .= '</pre></div>';
    }

    echo renderOutput($content);
}

/**
 * Checks for the installation requirements
 * @return array Array with a description of not met requirements
 */
function checkRequirements() {
    $requirements = array();

    list($major, $minor, $rest) = explode('.', PHP_VERSION);
    if ($major < 5 || ($major = 5 && $minor < 3)) {
        $requirements[] = 'You need at least PHP 5.3.0.';
    }

    if (!class_exists('ZipArchive')) {
        $requirements[] = 'Your PHP has no support for the ZipArchive class.';
    }

    if (!in_array('mod_rewrite', apache_get_modules())) {
        $requirements[] = 'Friendly URLs are disabled. Please check that <code>mod_rewrite</code> is installed and enabled on the webserver.';
    }

    $installDirectory = getInstallationDirectory();

    if (!is_writable($installDirectory)) {
        $requirements[] = 'The installation directory <code>' . $installDirectory . '</code> is not writable for the webserver.';
    }

    return $requirements;
}

/**
 * Downloads the latest Zibo version and unpacks it in the installation directory
 * @return null
 */
function downloadAndUnpack() {
    $installDirectory = getInstallationDirectory();

    $lastSeparator = strrpos(PACKAGE, DIRECTORY_SEPARATOR);
    $package = $installDirectory . DIRECTORY_SEPARATOR . substr(PACKAGE, $lastSeparator + 1);

    file_put_contents($package, file_get_contents(PACKAGE));

    $zip = new ZipArchive;
    $zip->open($package);
    $zip->extractTo($installDirectory);
    $zip->close();
}

/**
 * Renders the provided output in a basic HTML template
 * @param string $content HTML content
 * @return string
 */
function renderOutput($content) {
    $title = 'Zibo Installation';

    $style = <<<EOF
/** Font normalization **/
body { font:81.3%/1.231 Arial, Helvetica, sans-serif; /* (13px) */ *font-size:small; }
select, input, textarea, button { font:99% sans-serif; }
pre, code, kbd, samp { font-family: monospace, sans-serif; }

/** Minimal base styles **/
body, select, input, textarea { color: #333; }
h1,h2,h3,h4,h5,h6 { font-weight: bold; }
html { overflow-y: scroll; }

/** Here we go **/
body { background-color: #EEE; }

#body-container {
    background-color: #FFF;
    width: 800px;
    margin: 25px auto;
    padding: 15px;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
    -moz-box-shadow: 0px 0px 5px #999;
    -webkit-box-shadow: 0px 0px 5px #999;
    box-shadow: 0px 0px 5px #999;
}

h1,h2,h3,h4,h5,h6 { font-weight: bold; text-rendering: optimizeLegibility; }
h1 { font-size:1.385em; margin: 0 0 1em; } /* font-size with a base of 13px = 20px */
h2 { font-size:1.077em; margin: 0 0 1em; } /* font-size with a base of 13px = 18px */
h3 { font-size:1.077em; margin: 0 0 0.5em; } /* font-size with a base of 13px = 16px */
h4 { font-size:1em; margin: 0 0 0.25em; }
h5 { font-size:1em; margin: 0 0 0.25em; }
h6 { font-size:1em; }

p { margin-bottom: 1em; }

.error { color: red; }
.success { color: green; }

EOF;

    $output = '<html>';
    $output .= '<head>';
    $output .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    $output .= '<title>' . $title . '</title>';
    $output .= '<style type="text/css">' . $style . '</style>';
    $output .= '</head>';
    $output .= '<body>';
    $output .= '<div id="body-container">';
    $output .= '<h1>' . $title . '</h1>';
    $output .= '<div class="content">' . $content . '</div>';
    $output .= '</div>';
    $output .= '</body>';
    $output .= '</html>';

    return $output;
}

/**
 * Determines the installation directory, the directory in which this script resides.
 * @return string Path to the installation directory
 * @throws Exception When the installation directory could not be determined
 */
function getInstallationDirectory() {
    if (defined('INSTALL_DIR')) {
        return INSTALL_DIR;
    }

    if (!array_key_exists('SCRIPT_FILENAME', $_SERVER)) {
        throw new Exception('Could not determine the installation directory. Please set the INSTALL_DIR constant at the top of this script.');
    }

    return dirname($_SERVER['SCRIPT_FILENAME']);
}

/**
 * Determines the base URL where this script resides.
 * @return string The base URL
 * @throws Exception when the URL could not be determined
 */
function getBaseUrl() {
    if (defined('BASE_URL')) {
        return BASE_URL;
    }

    if (!array_key_exists('SERVER_NAME', $_SERVER)) {
        throw new Exception('Could not determine the URL for this installation. Please set the BASE_URL constant at the top of this script.');
    }

    if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on') {
        $url = 'https://';
    } else {
        $url = 'http://';
    }

    $url .= $_SERVER['SERVER_NAME'];

    $port = $_SERVER['SERVER_PORT'];
    if (!empty($port) && $port != 80) {
        $url .=  ':' . $port;
    }

    $script = $_SERVER['SCRIPT_NAME'];
    if (strpos($script, '/') === false) {
        $script = '/' . $script;
    }
    $url .= $script;

    return dirname($url);
}

run();