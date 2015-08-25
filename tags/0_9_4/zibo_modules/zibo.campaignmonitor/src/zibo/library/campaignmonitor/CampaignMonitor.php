<?php

namespace zibo\library\campaignmonitor;

use zibo\library\campaignmonitor\exception\CampaignMonitorException;

use \Exception;

/**
 * Implementation of the CampaignMonitor API
 * @see http://www.campaignmonitor.com/api
 */
class CampaignMonitor extends CampaignMonitorBase {

    /**
     * @param mixed $date If a string, should be in the date() format of 'Y-m-d H:i:s', otherwise, a Unix timestamp.
     * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
     * @return mixed A parsed response from the server, or null if something failed.
     * @see http://www.campaignmonitor.com/api/Subscribers.GetActive.aspx
     */
    public function subscriberGetActive($date = 0, $listId = null) {
        return $this->subscriberGenericDate('Subscribers.GetActive', $date, $listId);
    }

    /**
     * @param mixed $date If a string, should be in the date() format of 'Y-m-d H:i:s', otherwise, a Unix timestamp.
     * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
     * @see http://www.campaignmonitor.com/api/Subscribers.GetUnsubscribed.aspx
     */
    public function subscriberGetUnsubscribed($date  = 0, $listId = null) {
        return $this->subscriberGenericDate('Subscribers.GetUnsubscribed', $date, $listId);
    }

    /**
     * @param mixed $date If a string, should be in the date() format of 'Y-m-d H:i:s', otherwise, a Unix timestamp.
     * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
     * @see http://www.campaignmonitor.com/api/Subscribers.GetBounced.aspx
     */
    public function subscriberGetBounced($date  = 0, $listId = null) {
        return $this->subscriberGenericDate('Subscribers.GetBounced', $date, $listId);
    }

    /**
     * Wrapper for Subscribers.GetActive. This method triples as Subscribers.GetUnsubscribed
     * and Subscribers.GetBounced when the very last parameter is overridden.
     *
     * @param string $method Set the actual API method to call.
     * @param mixed $date If a string, should be in the date() format of 'Y-m-d H:i:s', otherwise, a Unix timestamp.
     * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
     * @return mixed A parsed response from the server, or null if something failed.
     * @see http://www.campaignmonitor.com/api/Subscribers.GetActive.aspx
     */
    private function subscriberGenericDate($method, $date = 0, $listId = null) {
        $listId = $this->getListId($listId);

        if (is_numeric($date)) {
            $date = date('Y-m-d H:i:s', $date);
        }

        $result = $this->invoke(
            $method,
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId,
                    'Date' => $date,
                )
            )
        );

        if (!$result) {
            return array();
        }

        $this->checkResultForErrors($result, 'anyType');

        if (isset($result['anyType']['Code']) && $result['anyType']['Code'] == 5) {
            throw new CampaignMonitorException($result['anyType']['Message'], 5);
        }

        if (isset($result['anyType']['Subscriber']['0'])) {
            return $result['anyType']['Subscriber'];
        }

        return array($result['anyType']['Subscriber']);
    }

    /*
     * @param string $method Set the actual API method to call.
     * @param string $email Email address.
     * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
     * @return mixed A parsed response from the server, or null if something failed.
     * @see http://www.campaignmonitor.com/api/Subscribers.GetActive.aspx
     */
    private function subscriberGenericEmail($method, $email, $listId = null) {
        $listId = $this->getListId($listId);

        return $this->invoke(
            $method,
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId,
                    'Email' => $email,
                )
            )
        );
    }

    /**
     * @param string $email Email address.
     * @param string $name User's name.
     * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
     * @param boolean $resubscribe If true, does an equivalent 'AndResubscribe' API method.
     * @see http://www.campaignmonitor.com/api/Subscriber.Add.aspx
     */
    public function subscriberAdd($email, $name, $listId = null, $resubscribe = false) {
        $listId = $this->getListId($listId);

        $method = 'Subscriber.Add';
        if ($resubscribe) {
            $method = 'Subscriber.AddAndResubscribe';
        }

        $result = $this->invoke(
            $method,
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId,
                    'Email' => $email,
                    'Name' => $name,
                )
            )
        );

        if (isset($result['Result']['Code']) && $result['Result']['Code'] == 1) {
            throw new CampaignMonitorException($result['Result']['Message'], 1);
        }

        return $result;
    }

    /**
    * This encapsulates the check of whether this particular user unsubscribed once.
    * @param string $email Email address.
    * @param string $name User's name.
    * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
    */
    public function subscriberAddRedundant($email, $name, $listId = null) {
        $added = $this->subscriberAdd($email, $name, $listId);

        if ($added && $added['Result']['Code'] == '204') {
            $subscribed = $this->subscribersGetIsSubscribed($email, $listId);

            // Must have unsubscribed, so resubscribe
            if ($subscribed['anyType'] == 'False') {
                // since we're internal, we'll just call the method with full parameters rather
                // than go through a secondary wrapper function.
                $added = $this->subscriberAdd($email, $name, $listId, true);
            }
        }

        return $added;
    }

    /**
    * @param string $email Email address.
    * @param string $name User's name.
    * @param mixed $fields Should be a $key => $value mapping. If there are more than one items for $key, let
    *        $value be a list of scalar values. Example: array('Interests' => array('xbox', 'wii'))
    * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
    * @param boolean $resubscribe If true, does an equivalent 'AndResubscribe' API method.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Subscriber.AddWithCustomFields.aspx
    */
    public function subscriberAddWithCustomFields($email, $name, $fields, $listId = null, $resubscribe = false) {
        $invokeMethod = $this->getMethod();
        $this->setMethod(self::METHOD_SOAP);

        $listId = $this->getListId($listId);

        $method = 'Subscriber.AddWithCustomFields';
        if ($resubscribe) {
            $method = 'Subscriber.AddAndResubscribeWithCustomFields';
        }

        if (!is_array($fields)) {
            $fields = array();
        }

        $customFields = array('SubscriberCustomField' => array());
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $nestedValue) {
                    $customFields['SubscriberCustomField'][] = array('Key' => $key, 'Value' => $nestedValue);
                }
            } else {
                $customFields['SubscriberCustomField'][] = array('Key' => $key, 'Value' => $value);
            }
        }

        try {
            $result = $this->invoke(
                $method,
                array(
                    self::OPTION_PARAMETERS => array(
                        'ListID' => $listId,
                        'Email' => $email,
                        'Name' => $name,
                        'CustomFields' => $customFields,
                   )
               )
           );

           $this->setMethod($invokeMethod);
        } catch (Exception $exception) {
           $this->setMethod($invokeMethod);
           throw $exception;
        }

        return $result;
    }

    /**
    * Same as subscriberAddRedundant() except with CustomFields.
    *
    * @param string $email Email address.
    * @param string $name User's name.
    * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    */
    public function subscriberAddWithCustomFieldsRedundant($email, $name, $fields, $listId = null) {
        $added = $this->subscriberAddWithCustomFields($email, $name, $fields, $listId);

        if ($added && $added['Code'] == '0') {
            $subscribed = $this->subscribersGetIsSubscribed($email);
            if ($subscribed == 'False') {
                $added = $this->subscriberAddWithCustomFields($email, $name, $fields, $listId, true);
            }
        }

        return $added;
    }

    /**
    * @param string $email Email address.
    * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
    * @param boolean $check_subscribed If true, does the Subscribers.GetIsSubscribed API method instead.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Subscriber.Unsubscribe.aspx
    */
    public function subscriberUnsubscribe($email, $listId = null, $checkSubscribed = false) {
        return $this->subscriberGenericEmail('Subscriber.Unsubscribe', $email, $listId);
    }

    /**
    * @return string A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Subscribers.GetIsSubscribed.aspx
    */
    public function subscriberGetIsSubscribed($email, $listId = null) {
        $result = $this->subscriberGenericEmail('Subscribers.GetIsSubscribed', $email, $listId);

        if (isset($result['anyType'])) {
            if ($result['anyType'] == 'True') {
                return true;
            } elseif ($result['anyType'] == 'False') {
                return false;
            }
        }

        throw new CampaignMonitorException('Invalid response recieved');
    }

    /**
    * Given an array of lists, indicate whether the $email is subscribed to each of those lists.
    *
    * @param string $email User's email
    * @param mixed $lists An associative array of lists to check against. Each key should be a List ID
    * @param boolean $noAssoc If true, only returns an array where each value indicates that the user is subscribed
    *        to that particular list. Otherwise, returns a fully associative array of $listId => true | false.
    * @return mixed An array corresponding to $lists where true means the user is subscribed to that particular list.
    */
    public function checkSubscriptions($email, $lists, $noAssoc = true) {
        $result = array();

        foreach ($lists as $lid => $misc) {
            $val = $this->subscribersGetIsSubscribed($email, $lid);
            $val = $val != 'False';
            if ($noAssoc && $val) {
                $result[] = $lid;
            } elseif (!$noAssoc) {
                $result[$lid] = $val;
            }
        }

        return $result;
    }

    /**
    * @param string $email Email address.
    * @param string $name User's name.
    * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
    * @see http://www.campaignmonitor.com/api/Subscriber.AddAndResubscribe.aspx
    */
    public function subscriberAddAndResubscribe($email, $name, $listId = null) {
        return $this->subscriberAdd($email, $name, $listId, true);
    }

    /**
    * @param string $email Email address.
    * @param string $name User's name.
    * @param mixed $fields Should only be a single-dimension array of key-value pairs.
    * @param int $listId (Optional) A valid List ID to check against. If not given, the default class property is used.
    * @param boolean $resubscribe If true, does an equivalent 'AndResubscribe' API method.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Subscriber.AddAndResubscribeWithCustomFields.aspx
    */
    public function subscriberAddAndResubscribeWithCustomFields($email, $name, $fields, $listId = null) {
        return $this->subscriberAddWithCustomFields($email, $name, $fields, $listId, true);
    }

    /**
     * Returns the details of a particular subscriber.
     * @param $email The subscriber's email address
     * @param $listId The ID of the list to which the subscriber belongs
     * @return mixed A parsed response from the server, or null if something failed
     * @see http://www.campaignmonitor.com/api/method/subscribers-get-single-subscriber/
     */
    public function subscriberGetSingleSubscriber($email, $listId = null) {
        $listId = $this->getListId($listId);

        return $this->invoke(
            'Subscribers.GetSingleSubscriber',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId,
                    'EmailAddress' => $email
               )
           )
       );
    }

    /*
    * A generic wrapper to feed Client.* calls.
    *
    * @param string $method The API method to call.
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    */
    private function clientGeneric($method, $clientId = null) {
        $clientId = $this->getClientId($clientId);

        return $this->invoke(
            'Client.' . $method,
            array(
                self::OPTION_PARAMETERS => array(
                    'ClientID' => $clientId
               )
            )
       );
    }

    /**
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Client.GetLists.aspx
    */
    public function clientGetLists($clientId = null) {
        return $this->clientGeneric('GetLists', $clientId);
    }

    /**
    * Creates an associative array with list_id => List_label pairings.
    *
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    */
    public function clientGetListsDropdown($clientId = null) {
        $lists = $this->clientGetLists($clientId);

        if (!isset($lists['List'])) {
            return null;
        } else {
            $lists = $lists['List'];
        }

        $result = array();

        if (isset($lists[0])) {
            foreach ($lists as $list) {
                $result[$list['ListID']] = $list['Name'];
            }
        } else {
            $result[$lists['ListID']] = $lists['Name'];
        }

        return $result;
    }

    /**
    * Creates an associative array with list_id:List_Label => (list_id) List_label pairings.
    * Remember that you'll need to split the key on ':' only once to get the appropriate ListID
    * and Segment Name.
    *
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    */
    public function clientGetSegmentsDropdown($clientId = null) {
        $lists = $this->clientGetSegments($clientId);

        if (!isset($lists['List'])) {
            return null;
        } else {
            $lists = $lists['List'];
        }

        $result = array();

        if (isset($lists[0])) {
            foreach ($lists as $list) {
                $result[$list['ListID'].':'.$list['Name']] = '(' . $list['ListID'] . ') ' . $list['Name'];
            }
        } else {
            $result[$lists['ListID'].':'.$lists['Name']] = '(' . $lists['ListID'] . ') ' . $lists['Name'];
        }

        return $result;
    }

    /**
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Client.GetCampaigns.aspx
    */
    public function clientGetCampaigns($clientId = null) {
        return $this->clientGeneric('GetCampaigns', $clientId);
    }

    /**
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Client.GetSegments.aspx
    */

    public function clientGetSegments($clientId = null) {
        return $this->clientGeneric('GetSegments', $clientId);
    }

    /**
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/client-getsuppressionlist/
    */
    public function clientGetSuppressionList($clientId = null) {
        return $this->clientGeneric('GetSuppressionList', $clientId);
    }

    /**
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/client-gettemplates/
    */
    public function clientGetTemplates($clientId = null) {
        return $this->clientGeneric('GetTemplates', $clientId);
    }

    /**
    * @param int $clientId (Optional) A valid Client ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/client-getdetail/
    */
    public function clientGetDetail($clientId = null) {
        return $this->clientGeneric('GetDetail', $clientId);
    }

    /**
    * @param string $companyName (CompanyName) Company name of the client to be added
    * @param string $contactName (ContactName) Contact name of the client to be added
    * @param string $emailAddress (EmailAddress) Email Address of the client to be added
    * @param string $country (Country) Country of the client to be added
    * @param string $timezone (Timezone) Timezone of the client to be added
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/client-create/
    */
    public function clientCreate($companyName, $contactName, $emailAddress, $country, $timezone) {
        return $this->invoke(
            'Client.Create',
            array(
                self::OPTION_PARAMETERS => array(
                    'CompanyName' => $companyName,
                    'ContactName' => $contactName,
                    'EmailAddress' => $emailAddress,
                    'Country' => $country,
                    'Timezone' => $timezone,
                )
            )
        );
    }

    /**
    * @param int $clientId (ClientID) ID of the client to be updated
    * @param string $companyName (CompanyName) Company name of the client to be updated
    * @param string $contactName (ContactName) Contact name of the client to be updated
    * @param string $emailAddress (EmailAddress) Email Address of the client to be updated
    * @param string $country (Country) Country of the client to be updated
    * @param string $timezone (Timezone) Timezone of the client to be updated
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/client-create/
    */
    public function clientUpdateBasics($clientId, $companyName, $contactName, $emailAddress, $country, $timezone) {
        return $this->invoke(
            'Client.UpdateBasics',
            array(
                self::OPTION_PARAMETERS => array(
                    'ClientID' => $clientId,
                    'CompanyName' => $companyName,
                    'ContactName' => $contactName,
                    'EmailAddress' => $emailAddress,
                    'Country' => $country,
                    'Timezone' => $timezone,
                )
            )
        );
    }

    /**
    * @param int $clientId (ClientID) ID of the client to be updated
    * @param string $accessLevel (AccessLevel) AccessLevel of the client
    * @param string $username (Username) Clients username
    * @param string $password (Password) Password of the client
    * @param string $billingType (BillingType) BillingType that the client will be set as
    * @param string $currency (Currency) Currency that the client will pay in
    * @param string $deliveryFee (DeliveryFee) Per campaign deliivery fee for the campaign
    * @param string $costPerRecipient (CostPerRecipient) Per email fee for the client
    * @param string $designAndSpamTestFee (DesignAndSpamTestFee) Amount the client will
    *               be charged if they have access to send design/spam tests
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/client-updateaccessandbilling/
    */
    public function clientUpdateAccessAndBilling($clientId, $accessLevel, $username, $password, $billingType, $currency, $deliveryFee, $costPerRecipient, $designAndSpamTestFee) {
        return $this->invoke(
            'Client.UpdateAccessAndBilling',
            array(
                self::OPTION_PARAMETERS => array(
                    'ClientID' => $clientId,
                    'AccessLevel' => $accessLevel,
                    'Username' => $username,
                    'Password' => $password,
                    'BillingType' => $billingType,
                    'Currency' => $currency,
                    'DeliveryFee' => $deliveryFee,
                    'CostPerRecipient' => $costPerRecipient,
                    'DesignAndSpamTestFee' => $designAndSpamTestFee,
                )
            )
        );
    }

    /**
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/User.GetClients.aspx
    */
    public function userGetClients() {
        return $this->invoke('User.GetClients');
    }

    /**
    * @return string A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/User.GetSystemDate.aspx
    */
    public function userGetSystemDate() {
        return $this->invoke('User.GetSystemDate');
    }

    /**
    * @return string A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/user-gettimezones/
    */
    public function userGetTimezones() {
        return $this->invoke('User.GetTimezones');
    }

    /**
    * @return string A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/user-getcountries/
    */
    public function userGetCountries() {
        return $this->invoke('User.GetCountries');
    }

    /**
     * Gets the API key for a Campaign Monitor user, given site URL, username,
     * password. If the user has not already had their API key generated at
     * the time this method is called, the user�s API key will be generated
     * and returned by this method.
     *
     * @param $siteUrl The base URL of the site you use to login to
     * Campaign Monitor. e.g. http://example.createsend.com/
     * @param $username The username you use to login to Campaign Monitor.
     * @param $password The password you use to login to Campaign Monitor.
     * @return mixed A parsed response from the server, or null if something
     * failed.
     * @see http://www.campaignmonitor.com/api/method/user-getapikey/
     */
    public function userGetApiKey($siteUrl, $username, $password) {
        return $this->invoke(
            'User.GetApiKey',
            array(
                self::OPTION_PARAMETERS => array(
                    'SiteUrl' => $siteUrl,
                    'Username' => $username,
                    'Password' => $password,
                )
            )
        );
    }

    /**
    * A generic wrapper to feed Campaign.* calls.
    *
    * @param string $method The API method to call.
    * @param int $campaignId (Optional) A valid Campaign ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    */
    private function campaignGeneric($method, $campaignId = null) {
        $campaignId = $this->getCampaignId($campaignId);

        return $this->invoke(
            'Campaign.' . $method,
            array(
                self::OPTION_PARAMETERS => array(
                    'CampaignID' => $campaignId
                )
            )
        );
    }

    /**
    * @param int $campaignId (Optional) A valid Campaign ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Campaign.GetSummary.aspx
    */
    public function campaignGetSummary($campaignId = null) {
        return $this->campaignGeneric('GetSummary', $campaignId);
    }

    /**
    * @param int $campaignId (Optional) A valid Campaign ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Campaign.GetOpens.aspx
    */
    public function campaignGetOpens($campaignId = null) {
        return $this->campaignGeneric('GetOpens', $campaignId);
    }

    /**
    * @param int $campaignId (Optional) A valid Campaign ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Campaign.GetBounces.aspx
    */
    public function campaignGetBounces($campaignId = null) {
        return $this->campaignGeneric('GetBounces', $campaignId);
    }

    /**
    * @param int $campaignId (Optional) A valid Campaign ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Campaign.GetSubscriberClicks.aspx
    */
    public function campaignGetSubscriberClicks($campaignId = null) {
        return $this->campaignGeneric('GetSubscriberClicks', $campaignId);
    }

    /**
    * @param int $campaignId (Optional) A valid Campaign ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Campaign.GetUnsubscribes.aspx
    */
    public function campaignGetUnsubscribes($campaignId = null) {
        return $this->campaignGeneric('GetUnsubscribes', $campaignId);
    }

    /**
    * @param int $campaignId (Optional) A valid Campaign ID to check against. If not given, the default class property is used.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Campaign.GetLists.aspx
    */
    public function campaignGetLists($campaignId = null) {
        return $this->campaignGeneric('GetLists', $campaignId);
    }

    /**
    * @param int $clientId The ClientID you wish to use; set it to null to use the default class property.
    * @param string $name (CampaignName) Name of campaign
    * @param string $subject (CampaignSubject) Subject of campaign mailing
    * @param string $fromName (FromName) The From name of the sender
    * @param string $fromEmail (FromEmail) The email of the sender
    * @param string $replyTo (ReplyTo) An alternate email to send replies to
    * @param string $htmlUrl (HtmlUrl) Location of HTML body of email
    * @param string $textUrl (TextUrl) Location of plaintext body of email
    * @param array $subscriberListIds (SubscriberListIDs) An array of ListIDs. This will automatically be converted to the right format
    * @param array $listSegments (ListSegments) An array of segment names and their corresponding ListIDs. Each element needs to
    *        be an associative array with keys ListID and Name.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/Campaign.Create.aspx
    */
    public function campaignCreate($clientId, $name, $subject, $fromName, $fromEmail, $replyTo, $htmlUrl, $textUrl, $subscriberListIds = null, $listSegments = null) {
        $invokeMethod = $this->getMethod();
        $this->setMethod(self::METHOD_SOAP);

        $clientId = $this->getClientId($clientId);

        $listIds = '';
        if ($subscriberListIds) {
            $listIds = array('string' => array());
            if (is_array($subscriberListIds)) {
                foreach ($subscriberListIds as $listId) {
                    $listIds['string'][] = $listId;
                }
            }
        }

        $segments = '';
        if ($listSegments) {
            $segments = array('List' => array());

            if (is_array($listSegments)) {
                foreach ($listSegments as $segment) {
                    $segments['List'][] = $segment;
                }
            }
        }

        try {
            $result = $this->invoke(
                'Campaign.Create',
                array(
                    self::OPTION_PARAMETERS => array(
                        'ClientID' => $clientId,
                        'CampaignName' => $name,
                        'CampaignSubject' => $subject,
                        'FromName' => $fromName,
                        'FromEmail' => $fromEmail,
                        'ReplyTo' => $replyTo,
                        'HtmlUrl' => $htmlUrl,
                        'TextUrl' => $textUrl,
                        'SubscriberListIDs' => $listIds,
                        'ListSegments' => $segments,
                    )
                )
            );

            $this->setMethod($invokeMethod);
        } catch (Exception $exception) {
            $this->setMethod($invokeMethod);
            throw $exception;
        }

        return $result;
    }

    /**
    * @param int $campaignId The CampaignID you wish to use; set it to null to use the default class property
    * @param string $confirmEmail (ConfirmationEmail) Email address to send confirmation of campaign send to
    * @param string $sendDate (SendDate) The timestamp to send the campaign. It must be formatted as YYY-MM-DD HH:MM:SS
    *               and should correspond to user's timezone.
    */
    public function campaignSend($campaignId, $confirmEmail, $sendDate) {
        $campaignId = $this->getCamapaignId($campaignId);

        return $this->invoke(
            'Campaign.Send',
            array(
                self::OPTION_PARAMETERS => array(
                    'CampaignID' => $campaignId,
                    'ConfirmationEmail' => $confirmEmail,
                    'SendDate' => $sendDate,
                )
            )
        );
    }

    /**
     * Delete a campaign.
     * @param $campaignId The ID of the campaign to delete.
     * @return A Status code indicating success or failure.
     * @see http://www.campaignmonitor.com/api/method/campaign-delete/
     */
    public function campaignDelete($campaignId) {
        return $this->campaignGeneric('Delete', $campaignId);
    }

    /**
    * @param int $clientId (ClientID) ID of the client the list will be created for
    * @param string $title (Title) Name of the new list
    * @param string $unsubscribePage (UnsubscribePage) URL of the page users will be
    *               directed to when they unsubscribe from this list.
    * @param string $confirmOptIn (ConfirmOptIn) If true, the user will be sent a confirmation
    *               email before they are added to the list. If they click the link to confirm
    *               their subscription they will be added to the list. If false, they will be
    *               added automatically.
    * @param string $confirmationSuccessPage (ConfirmationSuccessPage) URL of the page that
    *               users will be sent to if they confirm their subscription. Only required when
                    $confirmOptIn is true.
    * @see http://www.campaignmonitor.com/api/method/list-create/
    */
    public function listCreate($clientId, $title, $unsubscribePage, $confirmOptIn, $confirmationSuccessPage) {
        if ($confirmOptIn == 'false') {
            $confirmationSuccessPage = '';
        }

        return $this->invoke(
            'List.Create',
            array(
                self::OPTION_PARAMETERS => array(
                    'ClientID' => $clientId,
                    'Title' => $title,
                    'UnsubscribePage' => $unsubscribePage,
                    'ConfirmOptIn' => $confirmOptIn,
                    'ConfirmationSuccessPage' => $confirmationSuccessPage,
                )
            )
        );
    }

    /**
    * @param int $listId (List) ID of the list to be updated
    * @param string $title (Title) Name of the new list
    * @param string $unsubscribePage (UnsubscribePage) URL of the page users will be
    *               directed to when they unsubscribe from this list.
    * @param string $confirmOptIn (ConfirmOptIn) If true, the user will be sent a confirmation
    *               email before they are added to the list. If they click the link to confirm
    *               their subscription they will be added to the list. If false, they will be
    *               added automatically.
    * @param string $confirmationSuccessPage (ConfirmationSuccessPage) URL of the page that
    *               users will be sent to if they confirm their subscription. Only required when
                    $confirmOptIn is true.
    * @see http://www.campaignmonitor.com/api/method/list-update/
    */
    public function listUpdate($listId, $title, $unsubscribePage, $confirmOptIn, $confirmationSuccessPage) {
        if ($confirmOptIn == 'false') {
            $confirmationSuccessPage = '';
        }

        return $this->invoke(
            'List.Update',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId,
                    'Title' => $title,
                    'UnsubscribePage' => $unsubscribePage,
                    'ConfirmOptIn' => $confirmOptIn,
                    'ConfirmationSuccessPage' => $confirmationSuccessPage,
                )
            )
        );
    }

    /**
    * @param int $listId (List) ID of the list to be deleted
    * @see http://www.campaignmonitor.com/api/method/list-delete/
    */
    public function listDelete($listId) {
        return $this->invoke(
            'List.Delete',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId
                )
            )
        );
    }

    /**
    * @param int $listId (List) ID of the list to be deleted
    * @see http://www.campaignmonitor.com/api/method/list-getdetail/
    */
    public function listGetDetail($listId) {
        return $this->invoke(
            'List.GetDetail',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId
                )
            )
        );
    }

    /**
     * Gets statistics for a subscriber list
     * @param $listId The ID of the list whose statistics will be returned.
     * @return mixed A parsed response from the server, or null if something
     * @see http://www.campaignmonitor.com/api/method/list-getstats/
     */
    public function listGetStats($listId) {
        return $this->invoke(
            'List.GetStats',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId
                )
            )
        );
    }

    /**
    * @param int $listId (ListID) A valid list ID to check against.
    * @param string $fieldName (FieldName) Name of the new custom field
    * @param string $dataType (DataType) Data type of the field. Options are Text, Number,
    *               MultiSelectOne, or MultiSelectMany
    * @param string $Options (Options) The available options for a multi-valued custom field.
    *               Options should be separated by a double pipe "||". This field must be null
    *               for Text and Number custom fields
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/list-createcustomfield/
    */
    public function listCreateCustomField($listId, $fieldName, $dataType, $options) {
        if ($dataType == 'Text' || $dataType == 'Number') {
            $options = null;
        }

        return $this->invoke(
            'List.CreateCustomField',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId,
                    'FieldName' => $fieldName,
                    'DataType' => $dataType,
                    'Options' => $options,
                )
            )
        );
    }

    /**
    * @param int $listId (ListID) A valid list ID to check against.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/list-getcustomfields/
    */
    public function listGetCustomFields($listId) {
        return $this->invoke(
            'List.GetCustomFields',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId
                )
            )
        );
    }

    /**
    * @param int $listId (ListID) A valid list ID to check against.
    * @param int $key (Key) The Key of the field we want to delete.
    * @return mixed A parsed response from the server, or null if something failed.
    * @see http://www.campaignmonitor.com/api/method/list-deletecustomfield/
    */
    public function listDeleteCustomField($listId, $key) {
        return $this->invoke(
            'List.DeleteCustomField',
            array(
                self::OPTION_PARAMETERS => array(
                    'ListID' => $listId,
                    'Key' => $key,
                )
            )
        );
    }

    /**
     * @param int $clientId (ClientID) ID of the client the template will be created for
     * @param string $templateName (TemplateName) Name of the new template
     * @param string $htmlUrl (HTMLPageURL) URL of the HTML page you have created for the template
     * @param string $zipUrl (ZipFileURL) URL of a zip file containing any other files required by the template
     * @param string $screenshotUrl (ScreenshotURL) URL of a screenshot of the template
     * @see http://www.campaignmonitor.com/api/method/template-create/
     */
    public function templateCreate($clientId, $templateName, $htmlUrl, $zipUrl, $screenshotUrl) {
        return $this->invoke(
            'Template.Create', array(
            self::OPTION_PARAMETERS => array(
                'ClientID' => $clientId,
                'TemplateName' => $templateName,
                'HTMLPageURL' => $htmlUrl,
                'ZipFileURL' => $zipUrl,
                'ScreenshotURL' => $screenshotUrl
           ))
       );
    }

    /**
     * @param string $templateId (TemplateID) ID of the template whose details are being requested
     * @see http://www.campaignmonitor.com/api/method/template-getdetail/
     */
    public function templateGetDetail($templateId) {
        return $this->invoke(
            'Template.GetDetail',
            array(
                self::OPTION_PARAMETERS => array(
                    'TemplateID' => $templateId
                )
            )
       );
    }

    /**
     * @param string $templateId (TemplateID) ID of the template to be updated
     * @param string $templateName (TemplateName) Name of the template
     * @param string $htmlUrl (HTMLPageURL) URL of the HTML page you have created for the template
     * @param string $zipUrl (ZipFileURL) URL of a zip file containing any other files required by the template
     * @param string $screenshotUrl (ScreenshotURL) URL of a screenshot of the template
     * @see http://www.campaignmonitor.com/api/method/template-update/
     */
    public function templateUpdate($templateId, $templateName, $htmlUrl, $zipUrl, $screenshotUrl) {
        return $this->invoke(
            'Template.Update',
            array(
                self::OPTION_PARAMETERS => array(
                    'TemplateID' => $templateId,
                    'TemplateName' => $templateName,
                    'HTMLPageURL' => $htmlUrl,
                    'ZIPFileURL' => $zipUrl,
                    'ScreenshotURL' => $screenshotUrl,
                )
            )
        );
    }

    /**
     * @param string $templateId (TemplateID) ID of the template to be deleted
     * @see http://www.campaignmonitor.com/api/method/template-delete/
     */
    public function templateDelete($templateId) {
        return $this->invoke(
            'Template.Delete',
            array(
                self::OPTION_PARAMETERS => array(
                    'TemplateID' => $templateId
                )
            )
        );
    }

}