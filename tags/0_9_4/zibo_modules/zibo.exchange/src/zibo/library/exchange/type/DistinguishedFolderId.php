<?php

namespace zibo\library\exchange\type;

use zibo\library\exchange\Client;

use \DOMDocument;
use \InvalidArgumentException;

/**
 * The DistinguishedFolderId element identifies Microsoft Exchange Server 2007 folders that can be referenced by name.
 * If you do not use this element, you must use the FolderId  element to identify a folder.
 */
class DistinguishedFolderId extends BaseFolderId {

    /**
     * Name for the XML element of this type
     * @var string
     */
    const NAME = 'DistinguishedFolderId';

    /**
     * Name of the calendar folder
     * @var string
     */
    const CALENDAR = 'calendar';

    /**
     * Name of the contacts folder
     * @var string
     */
    const CONTACTS = 'contacts';

    /**
     * Name of the deleted items folder
     * @var string
     */
    const DELETEDITEMS = 'deleteditems';

    /**
     * Name of the drafts folder
     * @var string
     */
    const DRAFTS = 'drafts';

    /**
     * Name of the inbox folder
     * @var string
     */
    const INBOX = 'inbox';

    /**
     * Name of the journal folder
     * @var string
     */
    const JOURNAL = 'journal';

    /**
     * Name of the notes folder
     * @var string
     */
    const NOTES = 'notes';

    /**
     * Name of the outbox folder
     * @var string
     */
    const OUTBOX = 'outbox';

    /**
     * Name of the sent items folder
     * @var string
     */
    const SENTITEMS = 'sentitems';

    /**
     * Name of the tasks folder
     * @var string
     */
    const TASKS = 'tasks';

    /**
     * Name of the message folder root
     * @var string
     */
    const MSGFOLDERROOT = 'msgfolderroot';

    /**
     * Name of the public folder root
     * @var string
     */
    const PUBLICFOLDERSROOT = 'publicfoldersroot';

    /**
     * Name of the root of the mailbox
     * @var string
     */
    const ROOT = 'root';

    /**
     * Name of the junk folder
     * @var string
     */
    const JUNKEMAIL = 'junkemail';

    /**
     * Name of the search folder
     * @var string
     */
    const SEARCHFOLDERS = 'searchfolders';

    /**
     * Name of the voicemail folder
     * @var string
     */
    const VOICEMAIL = 'voicemail';

    /**
     * Constructs a new DistinguishedFolderId element
     * @param string $id Contains a string that identifies a default folder. This attribute is required.
     * @param string $changeKey Contains a string that identifies a version of a folder that is identified by the Id attribute. This attribute is optional. Use this attribute to make sure that the correct version of a folder is used.
     * @return null
     * @throws InvalidArgumentException when the provided id id not a distinguished folder id
     */
    public function __construct($id, $changeKey = null) {
        parent::__construct(self::NAME, $id, $changeKey);
    }

    /**
     * Sets the id of this FolderId
     * @param string $id
     * @return null
     * @throws InvalidArgumentException when the provided id id not a distinguished folder id
     */
    public function setId($id) {
        $ids = array(
            self::CALENDAR,
            self::CONTACTS,
            self::DELETEDITEMS,
            self::DRAFTS,
            self::INBOX,
            self::JOURNAL,
            self::NOTES,
            self::OUTBOX,
            self::SENTITEMS,
            self::TASKS,
            self::MSGFOLDERROOT,
            self::PUBLICFOLDERSROOT,
            self::ROOT,
            self::JUNKEMAIL,
            self::SEARCHFOLDERS,
            self::VOICEMAIL,
        );

        if (!in_array($id, $ids)) {
            throw new InvalidArgumentException('Provided id is not a distinguished folder id');
        }

        $this->Id = $id;
    }

}