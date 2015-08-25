<?php

namespace zibo\library\exchange\type;

use zibo\library\String;

use zibo\ZiboException;

/**
 * The ContactsView element defines a search for contact items based on alphabetical display names.
 */
class ContactsView extends BasePaging {

    /**
     * Defines the first name in the contacts list to return in the response. If the specified initial name is not in the contacts list,
     * the next alphabetical name as defined by the cultural context will be returned, except if the next name comes after FinalName.
     * If the InitialName attribute is omitted, the response will contain a list of contacts that starts with the first name in the contact list.
     * @var string
     */
    public $InitialName;

    /**
     * Defines the last name in the contacts list to return in the response. If the FinalName attribute is omitted, the response will contain all
     * subsequent contacts in the specified sort order. If the specified final name is not in the contacts list, the next alphabetical name as defined
     * by the cultural context will be excluded.
     */
    public $FinalName;

    /**
     * Constructs a new ContactsView element
     * @param integer $maxEntriesReturned Describes the maximum number of results to return in the response
     * @param string $initialName Defines the first name in the contacts list to return in the response
     * @param string $finalName Defines the last name in the contacts list to return in the response
     * @return null
     */
    public function __construct($maxEntriesReturned = null, $initialName = null, $finalName = null) {
        parent::__construct($maxEntriesReturned);

        if ($initialName !== null && String::isEmpty($initialName)) {
            throw new ZiboException('Provided initial name is empty');
        }

        if ($finalName !== null && String::isEmpty($finalName)) {
            throw new ZiboException('Provided final name is empty');
        }

        $this->InitialName = $initialName;
        $this->FinalName = $finalName;
    }

}