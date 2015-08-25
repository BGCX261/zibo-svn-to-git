<?php

namespace joppa\advertising\model;

use zibo\library\orm\model\ExtendedModel;
use zibo\library\DateTime;

/**
 * Model of the advertisements
 */
class AdvertisementModel extends ExtendedModel {

	/**
	 * Name of the model
	 * @var string
	 */
	const NAME = 'Advertisement';

	/**
	 * Path for the images
	 * @var string
	 */
    const PATH_IMAGE = 'application/web/images/advertisement';

    /**
     * Gets a list of advertisements
     * @return array Array with the id as key and name as value
     */
    public function getDataList($locale = null) {
        $query = $this->createQuery(0, $locale);
        $query->setFields('{id}, {name}');
        $query->addOrderBy('{name} ASC');

        $advertisements = $query->query();

        $list = array();
        foreach ($advertisements as $advertisement) {
            $list[$advertisement->id] = $advertisement->name;
        }

        return $list;
    }

    /**
     * Gets a running advertisement
     * @param int $block Id of the advertisement block (optional)
     * @return null|Advertisement
     */
    public function getRunningAdvertisement($block = null) {
        $time = DateTime::roundTimeToDay();

        $query = $this->createQuery(0);
        $query->addCondition('{dateStart} <= %1% AND %1% < {dateStop}', $time);

        if ($block) {
            $query->addCondition('{blocks.id} = %1%', $block);
        }

        $advertisements = $query->query();

        if (!$advertisements) {
        	return null;
        }

        $advertisementKey = array_rand($advertisements);

        return $advertisements[$advertisementKey];
    }

    /**
     * Registers a click for an advertisement
     * @param int $id Id of the advertisement
     * @return string URL to redirect to
     */
    public function click($id) {
        $advertisement = $this->findById($id, 0);
        if (!$advertisement) {
        	throw new ZiboException('Could not find the advertisement with id ' . $id);
        }

        $advertisement->clicks++;

        $this->save($advertisement);

        return $advertisement->website;
    }

}