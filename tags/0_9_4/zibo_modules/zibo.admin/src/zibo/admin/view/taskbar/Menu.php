<?php

namespace zibo\admin\view\taskbar;

use zibo\core\Zibo;

use zibo\library\html\AbstractElement;
use zibo\library\security\SecurityManager;

/**
 * Data container for a menu
 */
class Menu extends AbstractElement {

    /**
     * Value for a separator
     * @var string
     */
    const SEPARATOR = '-';

    /**
     * Label of the menu
     * @var string
     */
    private $label;

    /**
     * Array with the sub menu items
     * @var array
     */
    private $items;

    /**
     * The base url, needed for the getHtml method
     * @var string
     */
    private $baseUrl;

    /**
     * Construct this menu
     * @param string $label
     * @return null
     */
    public function __construct($label) {
        $this->items = array();
        $this->setLabel($label);
    }

    /**
     * Set the base url, needed for the getHtml method
     * @param string $baseUrl
     * @return null
     */
    public function setBaseUrl($baseUrl) {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get the html of this menu
     * @param string $baseUrl
     * @return string the html of this menu
     */
    public function getHtml() {
        if (!$this->hasItems()) {
            return '';
        }

        $html = '<ul' . $this->getIdHtml() . $this->getClassHtml() . $this->getAttributesHtml() . '>';
        foreach ($this->items as $item) {
            if ($item === self::SEPARATOR) {
                $html .= '<li class="separator"></li>';
                continue;
            }

            $item->setBaseUrl($this->baseUrl);

            if ($item instanceof self) {
                if ($item->hasItems()) {
                    $html .= '<li class="menu"><a href="#" class="menu">' . $item->getLabel() . '</a>' . $item->getHtml() . '</li>';
                }
            } else {
                $html .= $item->getHtml();
            }
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Set the label of this menu
     * @param string $label
     * @return null
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Get the label of this menu
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Add a sub menu item to this menu
     * @param MenuItem $menuItem
     * @return null
     */
    public function addMenuItem(MenuItem $menuItem) {
        if (!SecurityManager::getInstance()->isRouteAllowed($menuItem->getRoute())) {
            return;
        }

        $this->items[] = $menuItem;
    }

    /**
     * Add a sub menu to this menu
     * @param Menu $menu
     * @return null
     */
    public function addMenu(Menu $menu) {
        $this->items[] = $menu;
    }

    /**
     * Add a separator to this menu
     * @return null
     */
    public function addSeparator() {
        $this->items[] = self::SEPARATOR;
    }

    /**
     * Check whether this menu contains sub items
     * @return boolean true if there are items in this menu, false otherwise
     */
    public function hasItems() {
        foreach ($this->items as $item) {
            if ($item === self::SEPARATOR) {
                continue;
            }
            if ($item instanceof MenuItem) {
                return true;
            }
            if ($item->hasItems()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the sub item for a label
     * @param string $label
     * @return Menu|MenuItem|null the item with the provided label when found, null otherwise
     */
    public function getItem($label) {
        foreach ($this->items as $item) {
            if ($item === self::SEPARATOR) {
                continue;
            }
            if ($item->getLabel() == $label) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Get all the sub items of this menu
     * @return array Array with all the sub items of this menu (Menu, MenuItem or a '-')
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * Order the items in this menu alphabetically. Separator will be removed by calling this method.
     * @param boolean $recursive true to order the items recursivly, false to only order 1 level
     * @return null
     */
    public function orderItems($recursive = true) {
        foreach ($this->items as $index => $item) {
            if ($item === self::SEPARATOR) {
                unset($this->items[$index]);
                continue;
            }
            if ($recursive && $item instanceof self) {
                $item->orderItems(true);
            }
        }

        usort($this->items, array($this, 'compareItems'));
    }

    /**
     * Compare 2 items of a menu
     * @param Menu|MenuItem $a
     * @param Menu|MenuItem $b
     * @return 0 when $a and $b are the same, 1 when $a is bigger then $b, -1 otherwise
     */
    public static function compareItems($a, $b) {
        $al = strtolower($a->getLabel());
        $bl = strtolower($b->getLabel());

        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

}