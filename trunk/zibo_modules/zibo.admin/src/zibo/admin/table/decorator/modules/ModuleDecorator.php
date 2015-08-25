<?php

namespace zibo\admin\table\decorator\modules;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;

/**
 * Decorator of a module for the module table
 */
class ModuleDecorator implements Decorator {

    /**
     * The translator
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Constructs a new module decorator
     * @return null
     */
    public function __construct() {
        $this->translator = I18n::getInstance()->getTranslator();
    }

    /**
     * Decorates the cell with formatted information about the module in it
     * @param zibo\library\html\table\Cell $cell The cell to decorate
     * @param zibo\library\html\table\Row $row The row containing the cell
     * @param integer $rowNumber The number of the current row
     * @param array $remainingValues Array with the values of the remaining rows
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $html = '';

        $module = $cell->getValue();

        $id = $module['name'] . ucFirst($module['namespace']);
        $html .= $this->getModulesHtml($module['dependencies'], $id, 'dependencies', 'modules.label.depends.module', 'modules.label.depends.modules');
        $html .= $this->getModulesHtml($module['usage'], $id, 'usage', 'modules.label.used.module', 'modules.label.used.modules');

        if ($html != '') {
            $html = '<div class="info">' . $html . '</div>';
        }

        $html = '<span class="module">' . $this->getNameHtml($module) . '</span>' . $html;

        $cell->setValue($html);
    }

    /**
     * Gets the HTML of the name of the provided module
     * @param array $module Array with the module data
     * @return string
     */
    private function getNameHtml($module) {
        return ucfirst($module['name']) . ' ' . $module['version'] . ' (' . $module['namespace'] . ')';
    }

    /**
     * Gets the HTML of the provided modules
     * @param array $modules Array with the data of the modules
     * @param string $id Style id for the containing ul
     * @param string $class Style class for the containing div
     * @param string $labelModule The translation key for 1 module
     * @param string $labelModules The translation key for multiple modules
     * @return string The HTML of the provided modules
     */
    private function getModulesHtml($modules, $id, $class, $labelModule, $labelModules) {
        $numModules = count($modules);
        if ($numModules == 0) {
            return '';
        }

        if ($numModules == 1) {
            $label = $this->translator->translate($labelModule);
        } else {
            $label = $this->translator->translate($labelModules, array('number' => $numModules));
        }

        $id .= ucfirst($class);
        $anchor = new Anchor($label, '#');
        $anchor->setAttribute('id', $id);

        $html = '<div class="' . $class . '">';
        $html .= $anchor->getHtml() . '<ul id="' . $id . 'List">';
        foreach ($modules as $module) {
            $html .= '<li>' . $this->getNameHtml($module) . '</li>';
        }
        $html .= '</ul></div>';

        return $html;
    }

}