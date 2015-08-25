<?php

namespace joppa\forum\model;

use zibo\library\orm\model\ExtendedModel;

use \Exception;

/**
 * Model of the forum categories
 */
class ForumCategoryModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'ForumCategory';

    /**
     * Gets the categories with their board for the frontend
     * @return array Array with ForumCategoryData objects
     */
    public function getCategories() {
        $query = $this->createQuery(1);
        $query->addOrderBy('{orderIndex} ASC');

        $categories = $query->query();

        $profileModel = $this->getModel(ForumProfileModel::NAME);
        $topicModel = $this->getModel(ForumTopicModel::NAME);

        foreach ($categories as $category) {
            foreach ($category->boards as $board) {
            	$board->moderators = $profileModel->getModeratorsForBoard($board->id);
                $board->lastTopic = $topicModel->getLastTopicForBoard($board->id);
            }
        }

        return $categories;
    }

	/**
	 * Gets a list of all the forum categories.
	 * @param string $locale Not used
	 * @return array Array with the id as key and the name as value
	 */
    public function getDataList($locale = null) {
        $query = $this->createQuery(0);
        $query->setFields('{id}, {name}, {orderIndex}');
        $query->addOrderBy('{orderIndex} ASC');

        $categories = $query->query();

        $list = array();
        foreach ($categories as $category) {
            $list[$category->id] = $category->name;
        }
        return $list;
    }

    /**
     * Update the order index of the provided categories
     * @param array $order Array with the id's of the categories in the order they need to be
     * @return null
     */
    public function orderCategories(array $order) {
    	$query = $this->createQuery(0);
    	$query->setFields('{id}, {orderIndex}');
    	$query->addOrderBy('{orderIndex} ASC');

    	$categories = $query->query();

    	$index = 1;

    	$transactionStarted = $this->startTransaction();
    	try {
    		foreach ($order as $categoryId) {
    			if (!array_key_exists($categoryId, $categories)) {
    				throw new Exception('Provided category does not exist (id: ' . $categoryId . ')');
    			}

    			$categories[$categoryId]->orderIndex = $index;

    			$this->save($categories[$categoryId]);

    			unset($categories[$categoryId]);

    			$index++;
    		}

    		foreach ($categories as $category) {
    			$category->orderIndex = $index;

    			$this->save($category);

    			$index++;
    		}

    		$this->commitTransaction($transactionStarted);
    	} catch (Exception $exception) {
    		$this->rollbackTransaction($transactionStarted);

    		throw $exception;
    	}
    }

}