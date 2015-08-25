<?php

namespace zibo\core\config\io\ini;

use zibo\core\config\exception\ConfigException;
use zibo\core\config\io\ConfigIO;
use zibo\core\config\Config;
use zibo\core\environment\Environment;
use zibo\core\filesystem\FileBrowser;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\String;
use zibo\library\Structure;

use zibo\ZiboException;

/**
 * Reads configuration in the ini format from the config directories found by a Browser
 */
class IniConfigIO implements ConfigIO {

    /**
     * Extension for the files which are handled by this I/O implementation
     * @var string
     */
    const FILE_EXTENSION = '.ini';

    /**
     * Parser to get the read configuration in the right array structure
     * @var zibo\library\config\ini\IniIOParser
     */
    private $parser;

    /**
     * The environment in which we are operating
     * @var zibo\core\environment\Environment
     */
    private $environment;

    /**
     * The path of the environment configuration values
     * @var zibo\library\filesystem\File
     */
    private $environmentPath;

    /**
     * The path to write new configuration values to
     * @var zibo\library\filesystem\File
     */
    private $writePath;

    /**
     * The file browser used to find configuration files
     * @var zibo\library\filesystem\browser\Browser
     */
    private $browser;

    /**
     * Construct this configuration ini I/O implementation
     * @param zibo\core\environment\Environment the environment
     * @param zibo\library\filesystem\browser\Browser the browser needed for finding configuration files
     * @return null
     */
    public function __construct(Environment $environment, FileBrowser $fileBrowser) {
        $this->environment = $environment;
        $this->fileBrowser = $fileBrowser;

        $this->parser = new IniParser();
    }

    /**
     * Write a configuration value
     * @param string $key
     * @param mixed $value
     * @return null
     * @throws zibo\library\config\exception\ConfigException when the provided key is invalid or empty
     */
    public function write($key, $value) {
        if (!String::isString($key, String::NOT_EMPTY)) {
            throw new ConfigException('Provided key is empty');
        }

        $tokens = explode(Config::TOKEN_SEPARATOR, $key);
        if (count($tokens) < 2) {
            throw new ConfigException($key . ' should have at least 2 tokens (eg system.memory). Use ' . Config::TOKEN_SEPARATOR . ' as a token separator.');
        }

        $fileName = array_shift($tokens) . self::FILE_EXTENSION;
        $filePath = $this->getWritePath();
        $filePath->create();
        $file = new File($filePath, $fileName);

        $values = array();
        if ($file->exists()) {
            $values = $this->readFile($file, $values);
        }

        $keyInFile = implode(Config::TOKEN_SEPARATOR, $tokens);
        $values = $this->parser->addKey($values, $keyInFile, $value);
        $output = $this->parser->getWriteOutput($values);

        if ($output) {
            $file->write($output);
        } elseif ($file->exists()) {
            $file->delete();
        }
    }

    /**
     * Read the complete configuration
     * @return array Hierarchic array with each configuration token as a key
     */
    public function readAll() {
        $all = array();

        $sections = $this->getAllSections();
        foreach ($sections as $section) {
            $all[$section] = $this->read($section);
        }

        return $all;
    }

    /**
     * Read the configuration values for a section
     * @param string $section name of the section to read
     * @return array Hierarchic array with each configuration token as a key
     * @throws zibo\library\config\exception\ConfigException when the section name is invalid or empty
     */
    public function read($section) {
        if (!String::isString($section, String::NOT_EMPTY)) {
            throw new ConfigException('Provided section name is empty');
        }

        $values = array();
        $fileName = $section . self::FILE_EXTENSION;

        $values = $this->readIncludePaths($fileName, $values);
        $values = $this->readEnvironmentPath($fileName, $values);

        return $values;
    }

    /**
     * Read the configuration values for all the files with the provided file name in the Zibo file system structure
     * @param string $fileName name of the section file eg system.ini
     * @param array $values Array with the values which are already read
     * @return array Values array with the read configuration values added
     */
    private function readIncludePaths($fileName, array $values) {
        $fileName = Zibo::DIRECTORY_CONFIG . File::DIRECTORY_SEPARATOR . $fileName;
        $files = array_reverse($this->fileBrowser->getFiles($fileName));

        foreach ($files as $file) {
            $path = str_replace(File::DIRECTORY_SEPARATOR . $fileName, '', $file->getPath());

            $this->parser->setVariables(array('path' => $path));

            $values = $this->readFile($file, $values);

            $this->parser->setVariables(null);
        }

        return $values;
    }

    /**
     * Read the configuration values from the environment path and add them to the provided values array
     * @param string $fileName name of the section file eg system.ini
     * @param array $values Array with the values which are already read
     * @return array Values array with the read configuration values added
     */
    private function readEnvironmentPath($fileName, array $values) {
        $path = $this->getEnvironmentPath();

        $file = new File($path, $fileName);
        if ($file->exists()) {
            $this->parser->setVariables(array('path' => $path));

            $values = $this->readFile($file, $values);

            $this->parser->setVariables(null);
        }

        return $values;
    }

    /**
     * Read the configuration values for the provided file and add them to the provided values array
     * @param zibo\library\filesystem\File $file file to read and parse
     * @param array $values Array with the values which are already read
     * @return array Values array with the read configuration values added
     * @throws zibo\ZiboException when the provided file could not be read
     */
    private function readFile(File $file, $values) {
        $fileContent = $file->read();

        $ini = @parse_ini_string($fileContent, true);

        if ($ini === false) {
            $fileContent = $this->parser->parseReservedWords($fileContent);
            $ini = @parse_ini_string($fileContent, true, INI_SCANNER_RAW);
            if ($ini === false) {
                $error = error_get_last();
                throw new ZiboException('Could not read ' . $file->getPath() . ': ' . $error['message']);
            }
            $ini = $this->parser->unparseIniWithReservedWords($ini);
        }

        return $this->parser->getValuesFromIni($ini, $values);
    }

    /**
     * Get the names of all the sections in the configuration
     * @return array Array with the names of all the ini files in the configuration directory, withouth the extension
     */
    public function getAllSections() {
        $sections = array();

        $includePaths = $this->fileBrowser->getIncludePaths();

        foreach ($includePaths as $includePath) {
            $path = new File($includePath, Zibo::DIRECTORY_CONFIG);
            $sections = $this->getDirectorySections($path) + $sections;
        }

        $path = new File($this->getEnvironmentPath());
        $sections = $this->getDirectorySections($path) + $sections;

        return $sections;
    }

    /**
     * Get the names of the sections in the provided directory
     * @param zibo\library\filesystem\File $directory
     * @return array Array with the file names of all the ini files, withouth the extension
     */
    private function getDirectorySections(File $directory) {
        $sections = array();

        if (!$directory->exists()) {
            return $sections;
        }

        $files = $directory->read();
        foreach ($files as $file) {
            if ($file->isDirectory() || $file->getExtension() != 'ini') {
                continue;
            }
            $sectionName = substr($file->getName(), 0, -4);
            $sections[$sectionName] = $sectionName;
        }

        return $sections;
    }

    /**
     * Get the path of the environment configuration
     * @return string
     */
    private function getEnvironmentPath() {
        if (!$this->environmentPath) {
            $environmentName = $this->environment->getName();

            $writePath = $this->getWritePath();

            $this->environmentPath = new File($writePath, $environmentName);
        }

        return $this->environmentPath;
    }

    /**
     * Get the path to write new configuration to
     * @return zibo\library\filesystem\File
     */
    private function getWritePath() {
        if (!$this->writePath) {
            $this->writePath = new File(Zibo::DIRECTORY_APPLICATION, Zibo::DIRECTORY_CONFIG);
        }

        return $this->writePath;
    }

}