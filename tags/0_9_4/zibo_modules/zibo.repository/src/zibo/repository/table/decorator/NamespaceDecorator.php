<?php

namespace zibo\repository\table\decorator;

use zibo\library\i18n\I18n;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Anchor;
use zibo\library\html\Image;

use zibo\repository\model\ModuleNamespace;

/**
 * Decorator for a namespace of the repository
 */
class NamespaceDecorator implements Decorator {

    /**
     * Translation key for the number of modules label
     * @var string
     */
    const TRANSLATION_MODULES = 'repository.label.modules.count';

    /**
     * URL for the link behind the namespace name
     * @var string
     */
    private $action;

    /**
     * HTML of the generic icon of a namespace
     * @var string
     */
    private $image;

    /**
     * Constructs a new namespace decorator
     * @param string $action URL where the link behind the namespace name will point to. The name of the namespace will be concatted to this URL
     * @return null
     */
    public function __construct($action) {
        $this->action = $action;
        $this->translator = I18n::getInstance()->getTranslator();

        $image = new Image(ModuleNamespace::ICON);
        $this->image = $image->getHtml();
    }

    /**
     * Decorates the namespace value of the cell
     * @param zibo\library\html\table\Cell $cell Cell to decorate
     * @param zibo\library\html\table\Row $row Row which will contain the cell
     * @param int $rowNumber Number of the row in the table
     * @return null|boolean When used as group decorator, return true to display the group row, false or null otherwise
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $values) {
        $value = $cell->getValue();

        if ($value instanceof ModuleNamespace) {
            $value = $this->getNamespaceHtml($value);
        }

        $cell->setValue($value);
    }

    /**
     * Gets the HTML to represent a namespace
     * @param zibo\repository\model\ModuleNamespace $namespace
     * @return string
     */
    private function getNamespaceHtml(ModuleNamespace $namespace) {
        $value = $namespace->getName();

        if ($this->action) {
            $action = $this->action . $value;
            $anchor = new Anchor($value, $action);
            $value = $anchor->getHtml();
        }

        $value = $this->image . $value;
        $value .= '<div class="info">';
        $value .= $this->translator->translatePlural($namespace->countModules(), self::TRANSLATION_MODULES);
        $value .= '</div>';

        return $value;
    }

}