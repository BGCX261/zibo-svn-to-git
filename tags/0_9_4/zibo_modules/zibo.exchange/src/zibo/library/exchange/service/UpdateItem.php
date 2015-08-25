<?php

namespace zibo\library\exchange\service;

use zibo\library\exchange\type\ItemChanges;
use zibo\library\exchange\type\SavedItemFolderId;

use zibo\ZiboException;

/**
 * The UpdateItem element defines a request to update an item in a mailbox.
 */
class UpdateItem {

    /**
     * If there is a conflict, the update operation fails and an error is returned.
     * @var string
     */
    const CONFLICT_NEVER_OVERWRITE = 'NeverOverwrite';

    /**
     * The update operation automatically resolves any conflict.
     * @var string
     */
    const CONFLICT_AUTO_RESOLVE = 'AutoResolve';

    /**
     * If there is a conflict, the update operation will overwrite information.
     * @var string
     */
    const CONFLICT_ALWAYS_OVERWRITE = 'AlwaysOverwrite';

    /**
     * The item is updated and saved back to its current folder.
     * @var string
     */
    const MESSAGE_SAVE_ONLY = 'SaveOnly';

    /**
     * The item is updated and sent but no copy is saved.
     * @var string
     */
    const MESSAGE_SEND_ONLY = 'SendOnly';

    /**
     * The item is updated and a copy is saved in the folder identified by the SavedItemFolderId element.
     * @var string
     */
    const MESSAGE_SEND_AND_SAVE_COPY = 'SendAndSaveCopy';

    /**
     * The calendar item is updated but updates are not sent to attendees.
     * @var string
     */
    const SEND_TO_NONE = 'SendToNone';

    /**
     * The calendar item is updated and the meeting update is sent to all attendees but is not saved in the Sent Items folder.
     * @var string
     */
    const SEND_ONLY_TO_ALL = 'SendOnlyToAll';

    /**
     * The calendar item is updated and the meeting update is sent only to attendees that are affected by the change in the meeting.
     * @var string
     */
    const SEND_ONLY_TO_CHANGED = 'SendOnlyToChanged';

    /**
     * The calendar item is updated, the meeting update is sent to all attendees, and a copy is saved in the Sent Items folder.
     * @var string
     */
    const SEND_TO_ALL_AND_SAVE_COPY = 'SendToAllAndSaveCopy';

    /**
     * The calendar item is updated, the meeting update is sent to all attendees that are affected by the change in the meeting, and a copy is saved in the Sent Items folder.
     * @var string
     */
    const SEND_TO_CHANGED_AND_SAVE_COPY = 'SendToChangedAndSaveCopy';

    /**
     * Identifies the target folder for operations that update, send, and create items in the Exchange store.
     * @var zibo\library\exchange\type\SavedItemFolderId
     */
    public $SavedItemFolderId;

    /**
     * Contains an array of ItemChange  elements that identify items and the updates to apply to the items.
     * @var zibo\library\exchange\type\ItemChanges
     */
    public $ItemChanges;

    /**
     * Identifies the type of conflict resolution to try during an update. The default value is AutoResolve.
     * @var string
     */
    public $ConflictResolution;

    /**
     * Describes how the item will be handled after it is updated. The MessageDisposition  attribute is required for message items, including meeting messages such as meeting cancellations, meeting requests, and meeting responses.
     * @var string
     */
    public $MessageDisposition;

    /**
     * Describes how meeting updates are communicated after a calendar item is updated. This attribute is required for calendar items and calendar item occurrences.
     * @var string
     */
    public $SendMeetingInvitationsOrCancellations;

    /**
     * Constructs a new UpdateItem element
     * @param zibo\library\exchange\type\ItemChanges $itemChanges Contains an array of ItemChange  elements that identify items and the updates to apply to the items.
     * @param zibo\library\exchange\type\SavedItemFolderId $savedItemFolderId Identifies the target folder for operations that update, send, and create items in the Exchange store.
     * @param string $conflictResolution Identifies the type of conflict resolution to try during an update. The default value is AutoResolve.
     * @param string $messageDisposition Describes how the item will be handled after it is updated. The MessageDisposition  attribute is required for message items, including meeting messages such as meeting cancellations, meeting requests, and meeting responses.
     * @param string $sendMeetingInvitationsOrCancellations Describes how meeting updates are communicated after a calendar item is updated. This attribute is required for calendar items and calendar item occurrences.
     * @return null
     */
    public function __construct(ItemChanges $itemChanges, SavedItemFolderId $savedItemFolderId = null, $conflictResolution = null, $messageDisposition = null, $sendMeetingInvitationsOrCancellations = null) {
        $this->setConflictResolution($conflictResolution);
        $this->setMessageDisposition($messageDisposition);
        $this->setSendMeetingInvitationsOrCancellations($sendMeetingInvitationsOrCancellations);

        $this->ItemChanges = $itemChanges;
        $this->SavedItemFolderId = $savedItemFolderId;
    }

    public function setConflictResolution($conflictResolution) {
        if ($conflictResolution === null) {
            $conflictResolution = self::CONFLICT_AUTO_RESOLVE;
        } else if ($conflictResolution != self::CONFLICT_NEVER_OVERWRITE && $conflictResolution != self::CONFLICT_AUTO_RESOLVE && $conflictResolution != self::CONFLICT_ALWAYS_OVERWRITE) {
            throw new ZiboException('Provided conflict resolution is not valid');
        }

        $this->ConflictResolution = $conflictResolution;
    }

    public function setMessageDisposition($messageDisposition) {
        if ($messageDisposition !== null) {
            if ($messageDisposition != self::MESSAGE_SAVE_ONLY && $messageDisposition != self::MESSAGE_SEND_ONLY && $messageDisposition != self::MESSAGE_SEND_AND_SAVE_COPY) {
                throw new ZiboException('Provided message resolution is not valid');
            }
        }

        $this->MessageDisposition = $messageDisposition;
    }

    public function setSendMeetingInvitationsOrCancellations($send) {
        if ($send !== null) {
            if ($send != self::SEND_TO_NONE && $send != self::SEND_ONLY_TO_ALL && $send != self::SEND_ONLY_TO_CHANGED && $send != self::SEND_TO_ALL_AND_SAVE_COPY && $send != self::SEND_TO_CHANGED_AND_SAVE_COPY) {
                throw new ZiboException('Provided send meeting invitations or cancellations flag is not valid');
            }
        }

        $this->SendMeetingInvitationsOrCancellations = $send;
    }

}