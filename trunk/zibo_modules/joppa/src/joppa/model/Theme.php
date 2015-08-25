<?php

namespace joppa\model;

use zibo\core\Zibo;

use zibo\library\String;

use zibo\ZiboException;

/**
 * Theme data container
 */
class Theme {

    /**
     * Regions configuration key
     * @var string
     */
    const CONFIG_REGIONS = 'regions';

    /**
     * Name configuration key
     * @var string
     */
    const CONFIG_NAME = 'name';

    /**
     * Style configuration key
     * @var string
     */
    const CONFIG_STYLE = 'style';

    /**
     * Template configuration key
     * @var string
     */
    const CONFIG_TEMPLATE = 'template';

    /**
     * Suffix of a theme configuration key
     * @var string
     */
    const CONFIG_THEMES = 'themes';

    /**
     * Name of the default region for when there are no regions defined in the theme
     * @var string
     */
    const DEFAULT_REGION = 'content';

    /**
     * Default template file to be used when no template file is specified in the theme properties
     * @var string
     */
    const DEFAULT_TEMPLATE = 'joppa/index';

    /**
     * Name of this theme
     * @var string
     */
    private $name;

    /**
     * Path to the index template of this theme
     * @var string
     */
    private $template;

    /**
     * Array with the region's name as key and a Region object as value
     * @var array
     */
    private $regions;

    /**
     * Path to the css stylesheet of this theme
     * @var string
     */
    private $style;

    /**
     * Constructs a new theme object, reads the theme properties in the Zibo configuration
     *
     * The configuration keys of the theme's properties are generated with the provided name.
     * <ul>
     * <li>themes.[name].regions.[region] = regions in the index template (optional)</li>
     * <li>themes.[name].style = path to the css stylesheet of this theme (optional)</li>
     * <li>themes.[name].template = path to the index template of this theme (optional)</li>
     * </ul>
     * @param string $name name of the theme
     * @return null
     * @throws zibo\ZiboException when the provided name is invalid
     */
    public function __construct($name) {
        $this->setName($name);

        $configPrefix = self::CONFIG_THEMES . '.' . $this->name . '.';

        $zibo = Zibo::getInstance();
        $this->setRegions($zibo->getConfigValue($configPrefix . self::CONFIG_REGIONS, array()));
        $this->setStyle($zibo->getConfigValue($configPrefix . self::CONFIG_STYLE));
        $this->setTemplate($zibo->getConfigValue($configPrefix . self::CONFIG_TEMPLATE));
    }

    /**
     * Set the name of this theme
     * @param string $name
     * @return null
     * @throws zibo\ZiboException when the provided name is invalid
     */
    private function setName($name) {
        if (String::isEmpty($name)) {
            throw new ZiboException('Name of the theme is empty');
        }

        $this->name = $name;
    }

    /**
     * Get the name of this theme
     * @return string
     */
    public function getName() {
    	return $this->name;
    }

    /**
     * Set the style of this theme
     * @param string $style path to the css stylesheet
     * @return null
     * @throws zibo\ZiboException when the provided style is invalid
     */
    private function setStyle($style = null) {
        if ($style === null) {
            $this->style = null;
            return;
        }

        if (String::isEmpty($style)) {
            throw new ZiboException('Provided style is empty');
        }

        $this->style = $style;
    }

    /**
     * Get the style of this theme
     * @return string path to the css stylesheet
     */
    public function getStyle() {
        return $this->style;
    }

    /**
     * Set the template file of this theme
     * @param string $template path to the index template file
     * @return null
     * @throws zibo\ZiboException when the provided template is invalid
     */
    private function setTemplate($template = null) {
        if ($template === null) {
            $this->template = self::DEFAULT_TEMPLATE;
            return;
        }

        if (String::isEmpty($template)) {
            throw new ZiboException('Provided template is empty');
        }

        $this->template = $template;
    }

    /**
     * Get the template of this theme
     * @return string path to the index template file
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * Set the regions to this theme
     * @param array $regions
     * @return null;
     */
    private function setRegions(array $regions) {
        $this->regions = array();

        foreach ($regions as $key => $name) {
            $this->regions[$key] = new Region($name);
        }

        if (empty($this->regions)) {
            $this->regions[self::DEFAULT_REGION] = new Region(self::DEFAULT_REGION);
        }
    }

    /**
     * Get the regions of this theme
     * @return array Array with the name as key and a Region object as value
     */
    public function getRegions() {
        return $this->regions;
    }

    /**
     * Gets whether this theme has the provided region
     * @param string $region Name of the region
     * @return boolean True when this theme has the provided region, false otherwise
     */
    public function hasRegion($region) {
        return array_key_exists($region, $this->regions);
    }

    /**
     * Gets a list of the available themes
     * @return array Array with the name of the theme as key and as value
     */
    public static function getThemes() {
    	$themes = Zibo::getInstance()->getConfigValue(self::CONFIG_THEMES, array());

    	$list = array();
    	foreach ($themes as $name => $parameters) {
    		if (array_key_exists(self::CONFIG_NAME, $parameters)) {
                $list[$name] = $parameters[self::CONFIG_NAME];
    		} else {
                $list[$name] = $name;
    		}
    	}

    	return $list;
    }

}