<?php

namespace joppa\form\backend;

use joppa\model\Theme;

use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;

/**
 * Form to select a region of a theme
 */
class RegionSelectForm extends Form {

    /**
     * Name of the form
     * @var string
     */
	const NAME = 'formRegionSelect';

	/**
	 * Name of the region field
	 * @var string
	 */
	const FIELD_REGION = 'region';

	/**
     * Construct this form
     * @param string $action url where this form will point to
     * @param joppa\model\Theme $theme the theme for which a region has to be selected
     * @param string $region optional name of a region to preselect
     * @return null
	 */
	public function __construct($action, Theme $theme, $region = null) {
		parent::__construct($action, self::NAME);

		$regions = array();

		$themeRegions = $theme->getRegions();
		foreach ($themeRegions as $name => $themeRegion) {
		    $regions[$name] = $themeRegion->getName();
		}

		$factory = FieldFactory::getInstance();

		$field = $factory->createField(FieldFactory::TYPE_LIST, self::FIELD_REGION, $region);
        $field->setAttribute('onchange', 'this.form.submit()');
        $field->setOptions($regions);
        $field->addEmpty('---', '');

		$this->addField($field);
	}

	/**
	 * Get the current selected region
	 * @return string name of the region
	 */
    public function getRegion() {
        $region = $this->getValue(self::FIELD_REGION);

        if ($region) {
            return $region;
        }

        return null;
    }

}