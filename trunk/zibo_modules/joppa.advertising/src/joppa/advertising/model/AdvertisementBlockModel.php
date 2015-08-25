<?php

namespace joppa\advertising\model;

use zibo\library\orm\model\ExtendedModel;

/**
 * Model of the advertisement blocks
 */
class AdvertisementBlockModel extends ExtendedModel {

	/**
	 * Name of the model
	 * @var string
	 */
	const NAME = 'AdvertisementBlock';

	/**
	 * Gets a list of advertisement blocks
	 * @return array Array with the id as key and name as value
	 */
    public function getDataList($locale = null) {
        $query = $this->createQuery(0, $locale);
        $query->setFields('{id}, {name}');
        $query->addOrderBy('{name} ASC');

        $blocks = $query->query();

        $list = array();
        foreach ($blocks as $block) {
            $list[$block->id] = $block->name;
        }

        return $list;
    }

}