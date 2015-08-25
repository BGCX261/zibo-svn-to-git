<?php

namespace joppa\comment\model;

use zibo\library\orm\model\ExtendedModel;

/**
 * The model of the comments
 */
class CommentModel extends ExtendedModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'Comment';

    const PERMISSION_ADMIN = 'joppa.comment.admin';

    /**
     * Gets the latest comments
     * @param integer $number Number of comments to fetch
     * @return array Array with CommentData objects
     */
    public function getLatestComments($number = 7) {
        $query = $this->createQuery();
        $query->addOrderBy('{dateAdded} DESC');
        $query->setLimit($number);

        return $query->query();
    }

    /**
     * Gets the comments for the provided object
     * @param string $objectType Type of the object
     * @param string $objectId Id of the object
     * @param integer $parent Id of the parent comment id
     * @return array Array with CommentData objects
     */
    public function getComments($objectType, $objectId, $parent = null) {
    	$query = $this->createQuery();
    	$query->addCondition('{objectType} = %1% AND {objectId} = %2%', $objectType, $objectId);
    	$query->addOrderBy('{dateAdded} ASC');

    	if ($parent) {
    	   $query->addCondition('{parent} = %1%', $parent);
    	} else {
    	   $query->addCondition('{parent} IS NULL');
    	}

    	$comments = $query->query();
    	foreach ($comments as $comment) {
    		$comment->replies = $this->getComments($objectType, $objectId, $comment->id);
    	}

    	return $comments;
    }

}