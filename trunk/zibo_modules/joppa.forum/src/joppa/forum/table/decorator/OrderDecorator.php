<?php

namespace joppa\forum\table\decorator;

use zibo\library\html\table\decorator\Decorator;
use zibo\library\html\table\Cell;
use zibo\library\html\table\Row;
use zibo\library\html\Image;

/**
 * Decorator for the order of the forum objects
 */
class OrderDecorator implements Decorator {

	/**
	 * Path to the image for the handle
	 * @var string
	 */
    const IMAGE_HANDLE = 'web/images/order.png';

    /**
     * Decorates the cell with a order handle for the category
     * @param zibo\library\html\table\Cell $cell
     * @param zibo\library\html\table\Row $row
     * @param integer $rowNumber
     * @param array $remainingValues
     * @return null
     */
    public function decorate(Cell $cell, Row $row, $rowNumber, array $remainingValues) {
        $data = $cell->getValue();

        $row->setId('data_' . $data->id);
        $cell->appendToClass('action');

        $image = new Image(self::IMAGE_HANDLE);
        $image->appendToClass('handle');

        $cell->setValue($image->getHtml());
    }

}