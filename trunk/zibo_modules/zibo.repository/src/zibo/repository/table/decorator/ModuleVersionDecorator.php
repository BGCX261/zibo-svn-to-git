<?php

namespace zibo\repository\table\decorator;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;

/**
 * Decorator for a module version with it dependencies
 */
class ModuleVersionDecorator implements Decorator {

    const TRANSLATION_DEPENDS = 'repository.label.depends';

    /**
     * URL to the action of a version
     * @var string
     */
    private $action;

    /**
     * Instance of the translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new version decorator
     * @param string $action URL to the action of a version
     */
    public function __construct($action = null) {
        $this->action = $action;
        $this->translator = I18n::getInstance()->getTranslator();
    }

    /**
     * Decorates the module value of the cell with it's version
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @param array $values Array with the remaining rows
     * @return null|boolean When used as group decorator, return true to display the group row, false or null otherwise
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $values) {
        $module = $cell->getValue();
        $version = $module->getVersion();

        if ($this->action) {
            $anchor = new Anchor($version, $this->action . $version);
            $value = $anchor->getHtml();
        } else {
            $value = $version;
        }

        $value .= $this->getDependenciesHtml('dependencies-' . $version, $module->getDependencies());

        $cell->setValue($value);
    }

    /**
     * Gets the HTML of the dependencies of the module
     * @param string $id CSS id for the dependencies container
     * @param array $dependencies Array with Module instances with version info
     * @return HTML of the dependencies
     */
    protected function getDependenciesHtml($id, $dependencies) {
        $dependenciesCount = count($dependencies);

        if (!$dependenciesCount) {
            return '';
        }

        $label = $this->translator->translatePlural($dependenciesCount, self::TRANSLATION_DEPENDS);

        $anchor = new Anchor($label, '#');
        $jQueryEscapedId = str_replace('.', '\\\\.', $id);
        $anchor->setAttribute('onclick', '$("#' . $jQueryEscapedId . '").slideToggle("normal"); return false;');

        $html = '<div class="info">' . PHP_EOL;
        $html .= $anchor->getHtml() . PHP_EOL;
        $html .= '<div id="' . $id . '" style="display:none;"><ul>' . PHP_EOL;
        foreach ($dependencies as $dependency) {
            $html .= '<li>' . $dependency->getNamespace() . '.' . $dependency->getName() . ' ' . $dependency->getVersion() . '</li>' . PHP_EOL;
        }
        $html .= '</ul></div></div>';

        return $html;
    }

}