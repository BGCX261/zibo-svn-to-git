<?php

namespace zibo\repository\table\decorator;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\html\Image;

use zibo\repository\model\Module;

/**
 * Decorator for a module of the repository
 */
class ModuleDecorator implements Decorator {

    /**
     * Translation key for the label of the latest module version
     * @var string
     */
    const TRANSLATION_VERSION = 'repository.label.version.newest';

    /**
     * URL for the link behind the module name
     * @var string
     */
    private $action;

    /**
     * HTML of the generic icon of a module
     * @var string
     */
    private $image;

    /**
     * Constructs a new module decorator
     * @param string $action URL where the link behind the module name will point to. The name of the module will be concatted to this URL
     * @return null
     */
    public function __construct($action) {
        $this->action = $action;
        $this->translator = I18n::getInstance()->getTranslator();

        $image = new Image(Module::ICON);
        $this->image = $image->getHtml();
    }
    /**
     * Decorates the module value of the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @return null|boolean When used as group decorator, return true to display the group row, false or null otherwise
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $values) {
        $value = $cell->getValue();

        if ($value instanceof Module) {
            $value = $this->getModuleHtml($value);
        }

        $cell->setValue($value);
    }

    /**
     * Gets the HTML to represent a module
     * @param zibo\repository\model\Module $module
     * @return string
     */
    private function getModuleHtml(Module $module) {
        $value = $module->getName();

        if ($this->action) {
            $action = $this->action . $value;
            $anchor = new Anchor($value, $action);
            $value = $anchor->getHtml();
        }

        $value = $this->image . $value;
        $value .= '<div class="info">';
        $value .= $this->translator->translate(self::TRANSLATION_VERSION, array('version' => $module->getVersion()));
        $value .= '</div>';

        return $value;
    }

}