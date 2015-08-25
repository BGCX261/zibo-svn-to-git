<?php

namespace joppa\model;

use zibo\library\orm\model\ExtendedModel;
use zibo\library\DateTime;
use zibo\library\String;

/**
 * Model for the content types with a slug
 */
abstract class SlugModel extends ExtendedModel {

    /**
     * Validates the data and generates the slug for the data
     * @param mixed $data The data object of the model
     * @return null
     * @throws zibo\library\validation\exception\ValidationException when the data is not valid
     */
    public function validate($data) {
    	$slugString = $this->getSlugString($data);
    	if ($slugString) {
	    	$slug = $baseSlug = String::safeString($slugString);
	    	$index = 1;

	    	do {
	            $query = $this->createQuery();
	            $query->addCondition('{slug} = %1%', $slug);
	            if ($data->id) {
	            	$query->addCondition('{id} <> %1%', $data->id);
	            }

	            if ($query->count()) {
	            	$slug = $baseSlug . '-' . $index;
	            	$index++;
	            } else {
	            	break;
	            }
	    	} while (true);

	    	$data->slug = $slug;
    	}

    	parent::validate($data);
    }

    /**
     * Gets the string to base the slug upon
     * @param mixed $data The data object of the model
     * @return string
     */
    abstract protected function getSlugString($data);

}