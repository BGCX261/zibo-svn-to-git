<?php

namespace zibo\library\smarty\resource;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Resource handler for Smarty according to the Zibo standards with theming extension
 */
class ThemedResourceHandler extends ResourceHandler {

    /**
     * Name of the default themes directory
     * @var string
     */
    const DEFAULT_THEMES_DIRECTORY = 'themes';

    /**
     * Name of the current theme
     * @var string
     */
    private $theme;

    /**
     * Directory of the themes
     * @var string
     */
    private $themesDirectory;

    /**
     * Id of the template
     * @var string
     */
    private $templateId;

    /**
     * Construct the themed resource handler
     * @param string $theme name of the theme
     * @return null
     */
    public function __construct($theme = null) {
        $this->setThemesDirectory();
        $this->setTheme($theme);
    }

    /**
     * Set the current theme
     * @param string $theme name of the theme
     * @return null
     */
    public function setTheme($theme = null) {
        if ($theme === null) {
            $this->theme = null;
            return;
        }

        if (String::isEmpty($theme)) {
            throw new ZiboException('Theme is empty');
        }

        $this->theme = $theme;
    }

    /**
     * Set the directory of the themes
     * @param string $directory name of the themes directory inside the Zibo view directory, null for the configuration's default
     * @return null
     */
    public function setThemesDirectory($directory = null) {
        if ($directory === null) {
            $this->themesDirectory = self::DEFAULT_THEMES_DIRECTORY;
            return;
        }

        if (String::isEmpty($directory)) {
            throw new ZiboException('Directory is empty');
        }

        $this->themesDirectory = $directory;
    }

    /**
     * Sets the id of the template. Don't forget to set compile_id on the Smarty engine itself.
     * @param string $templateId Id of the template
     * @return null
     */
    public function setTemplateId($templateId) {
        $this->templateId = $templateId;
    }

    /**
     * Get the source file of a template, check theme directories first
     * @param string $name relative path of the template to the view folder without the extension
     * @return zibo\library\filesystem\File instance of a File if the source is found, null otherwise
     */
    protected function getFile($name, $smarty) {
        $zibo = Zibo::getInstance();
        $viewPath = Zibo::DIRECTORY_VIEW . File::DIRECTORY_SEPARATOR;
        $themeBasePath = $viewPath . $this->themesDirectory . DIRECTORY_SEPARATOR;
        $template = $name . self::EXTENSION;

        if ($this->theme !== null) {
            $themePath = $themeBasePath . $this->theme . File::DIRECTORY_SEPARATOR;

            if ($this->templateId !== null) {
                $templateFile = $zibo->getFile($themePath . $name . '.' . $this->templateId . self::EXTENSION);
                if ($templateFile) {
                    return $templateFile;
                }
            }

            $templateFile = $zibo->getFile($themePath . $template);
            if ($templateFile) {
                return $templateFile;
            }
        }

        $templateFile = $zibo->getFile($viewPath . $template);
        if ($templateFile) {
            return $templateFile;
        }

        return null;
    }

    /**
     * Get the available themes based on the existance of the theme directory
     * @param string $themesDirectory name of the theme directory in the view directory (optional)
     * @return array Array with the names of the available themes
     */
    public static function getThemes($themesDirectory = null) {
        if ($themesDirectory === null) {
            $themesDirectory = self::DEFAULT_THEMES_DIRECTORY;
        }

        $themes = array();
        $includePaths = Zibo::getInstance()->getIncludePaths();
        $viewPath = new File(Zibo::DIRECTORY_VIEW, $themesDirectory);

        foreach ($includePaths as $includePath) {
            $themesPath = new File($includePath, $viewPath);
            if ($themesPath->exists() && $themesPath->isDirectory() && $themesPath->isReadable()) {
                $themesFiles = $themesPath->read();
                foreach ($themesFiles as $themesFile) {
                    if ($themesFile->isDirectory() && $themesFile->isReadable()) {
                        $name = $themesFile->getName();
                        $themes[$name] = $name;
                    }
                }
            }
        }

        return $themes;
    }

}