<?php

namespace zibo\library\exchange\type;

/**
 * The ContactsFolder element represents a contacts folder that is contained in a mailbox.
 */
class ContactsFolder extends BaseFolder {

    /**
     * Name for the element of this type
     * @var string
     */
    const NAME = 'ContactsFolder';

    /**
     * Constructs a new ContactsFolder element
     * @param string $displayName
     * @return null
     */
    public function __construct($displayName) {
        parent::__construct(self::NAME, $displayName);
    }

}
