<?php

namespace zibo\library\exchange;

use zibo\library\exchange\service\CreateFolder;
use zibo\library\exchange\service\CreateItem;
use zibo\library\exchange\service\DeleteFolder;
use zibo\library\exchange\service\DeleteItem;
use zibo\library\exchange\service\FindFolder;
use zibo\library\exchange\service\FindItem;
use zibo\library\exchange\service\GetFolder;
use zibo\library\exchange\service\UpdateItem;
use zibo\library\soap\curl\Client as SoapClient;

use zibo\core\Zibo;

use zibo\ZiboException;

use \SoapHeader;

/**
 * Client for a Microsoft Exchange server
 * @see http://msdn.microsoft.com/en-us/library/bb204119%28EXCHG.80%29.aspx
 */
class Client {

    /**
     * Default requested Exchange server version
     * @var string
     */
    CONST DEFAULT_VERSION = 'Exchange2007_SP1';

    /**
     * XML Schema for the Exchange types
     */
    const SCHEMA_TYPES = 'http://schemas.microsoft.com/exchange/services/2006/types';

    /**
     * Relative path to the server of the Exchange services
     * @var string
     */
    CONST SERVICE_PATH = '/EWS/Exchange.asmx';

    /**
     * Relative path to the wsdl file of the Exchange services
     * @var string
     */
    CONST SERVICE_WSDL = '/../../../../config/exchange/services.wsdl';

    /**
     * Code for response witherrors
     * @var string
     */
    const RESPONSE_ERROR = 'Error';

    /**
     * Code for response without errors
     * @var string
     */
    const RESPONSE_NO_ERROR = 'NoError';

    /**
     * Hostname or IP address of the Exchange server
     * @var string
     */
    protected $server;

    /**
     * Username to authenticate with the Exchange server
     * @var string
     */
    protected $username;

    /**
     * Password to authenticate with the Exchange server
     * @var string
     */
    protected $password;

    /**
     * Flag to set whether the SOAP client should trace errors
     * @var boolean
     */
    protected $willTrace;

    /**
     * Requested Exchange server version
     * @var string
     */
    protected $version;

    /**
     * The SOAP client
     * @var zibo\library\soap\curl\Client;
     */
    protected $soapClient;

    /**
     * Constructs a new Exchange client
     * @param string $server Hostname or IP adress of the Exchange server
     * @param string $username Username to authenticate with the Exchange server
     * @param string $password Password to authenticate with the Exchange server
     * @param boolean $willTrace Flag to set whether the SOAP client should trace errors
     * @param string $version Requested Exchange server version
     * @return null
     * @throws zibo\ZiboException when the provided version is empty
     */
    public function __construct($server, $username, $password, $willTrace = true, $version = null) {
        $this->setVersion($version);

        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
        $this->willTrace = $willTrace;
    }

    /**
     * Sets the requested Exchange server version
     * @param string $version
     * @return null
     * @throws zibo\ZiboException when the provided version is empty
     */
    protected function setVersion($version) {
        if ($version === null) {
            $version = self::DEFAULT_VERSION;
        } elseif (String::isEmpty($version)) {
            throw new ZiboException('Provided version is empty');
        }

        $this->version = $version;
    }

    /**
     * Creates a new item
     * @param zibo\library\exchange\service\CreateItem $request Request for the CreateItem service
     * @return object The response from the Exchange server
     */
    public function createItem(CreateItem $request) {
        $response = $this->getSOAPClient()->CreateItem($request);

        return $response->ResponseMessages->CreateItemResponseMessage;
    }

    /**
     * Updates a existing item
     * @param zibo\library\exchange\service\UpdateItem $request Request for the UpdateItem service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occured
     */
    public function updateItem(UpdateItem $request) {
        try {
            $response = $this->getSOAPClient()->UpdateItem($request);
        } catch (\Exception $e) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $e->getMessage(), $this->getSOAPClient()->__getLastRequest());
            throw $e;
        }

        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $this->getSOAPClient()->__getLastRequest());

        if ($response->ResponseMessages->UpdateItemResponseMessage->ResponseClass == self::RESPONSE_ERROR) {
            throw new ZiboException($response->ResponseMessages->UpdateItemResponseMessage->MessageText);
        }

        return $response->ResponseMessages->UpdateItemResponseMessage;
    }

    /**
     * Deletes a existing item
     * @param zibo\library\exchange\service\DeleteItem $request Request for the DeleteItem service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occured
     */
    public function deleteItem(DeleteItem $request) {
        $response = $this->getSOAPClient()->DeleteItem($request);

        if ($response->ResponseMessages->DeleteItemResponseMessage->ResponseCode == self::RESPONSE_NO_ERROR) {
            return true;
        }

        throw new ZiboException($response->ResponseMessages->DeleteItemResponseMessage->MessageText);
    }

    /**
     * Finds items
     * @param zibo\library\exchange\service\FindItem $request Request for the FindItem service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occurred
     */
    public function findItem(FindItem $request) {
        try {
            $response = $this->getSOAPClient()->FindItem($request);
        } catch (\Exception $e) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $this->getSOAPClient()->__getLastRequest());
            throw $e;
        }

        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $this->getSOAPClient()->__getLastRequest());

        if ($response->ResponseMessages->FindItemResponseMessage->ResponseClass == self::RESPONSE_ERROR) {
            throw new ZiboException($response->ResponseMessages->FindItemResponseMessage->MessageText);
        }

        return $response->ResponseMessages->FindItemResponseMessage->RootFolder;
    }

    /**
     * Gets items
     * @param zibo\library\exchange\service\GetItem $request Request for the GetItem service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occurred
     */
    public function getItem(GetItem $request) {
        try {
            $response = $this->getSOAPClient()->GetItem($request);
        } catch (\Exception $e) {
            Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $this->getSOAPClient()->__getLastRequest());
            throw $e;
        }

        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $this->getSOAPClient()->__getLastRequest());

        if ($response->ResponseMessages->GetItemResponseMessage->ResponseClass == self::RESPONSE_ERROR) {
            throw new ZiboException($response->ResponseMessages->GetItemResponseMessage->MessageText);
        }

        return $response->ResponseMessages->GetItemResponseMessage->Items;
    }

    /**
     * Creates a new folder
     * @param zibo\library\exchange\service\CreateFolder $request Request for the CreateFolder service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occured
     */
    public function createFolder(CreateFolder $request) {
        $response = $this->getSOAPClient()->CreateFolder($request);

        if ($response->ResponseMessages->CreateFolderResponseMessage->ResponseClass == self::RESPONSE_ERROR) {
            throw new ZiboException($response->ResponseMessages->CreateFolderResponseMessage->MessageText);
        }

        return $response->ResponseMessages->CreateFolderResponseMessage->Folders;
    }

    /**
     * Deletes a existing folder
     * @param zibo\library\exchange\service\DeleteFolder $request Request for the DeleteFolder service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occured
     */
    public function deleteFolder(DeleteFolder $request) {
        $response = $this->getSOAPClient()->DeleteFolder($request);

        if ($response->ResponseMessages->DeleteFolderResponseMessage->ResponseCode == self::RESPONSE_NO_ERROR) {
            return true;
        }

        throw new ZiboException($response->ResponseMessages->DeleteFolderResponseMessage->MessageText);
    }

    /**
     * Finds folders
     * @param zibo\library\exchange\service\FindFolder $request Request for the FindFolder service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occured
     */
    public function findFolder(FindFolder $request) {
        $response = $this->getSOAPClient()->FindFolder($request);

        if (!isset($response->ResponseMessages->FindFolderResponseMessage->ResponseCode)) {
            throw new ZiboException('No response code found in the response of the Exchange server');
        }

        if ($response->ResponseMessages->FindFolderResponseMessage->ResponseCode != self::RESPONSE_NO_ERROR) {
            throw new ZiboException($response->ResponseMessages->FindFolderResponseMessage->MessageText);
        }

        return $response->ResponseMessages->FindFolderResponseMessage->RootFolder;
    }

    /**
     * Gets folders
     * @param zibo\library\exchange\service\GetFolder $request Request for the GetFolder service
     * @return object The response from the Exchange server
     * @throws zibo\ZiboException when an error occured
     */
    public function getFolder(GetFolder $request) {
        $response = $this->getSOAPClient()->GetFolder($request);

        if (!isset($response->ResponseMessages->GetFolderResponseMessage->ResponseCode)) {
            throw new ZiboException('No response code found in the response of the Exchange server');
        }

        if ($response->ResponseMessages->GetFolderResponseMessage->ResponseCode != self::RESPONSE_NO_ERROR) {
            throw new ZiboException('Error occured');
        }

        return $response->ResponseMessages->GetFolderResponseMessage->Folders;
    }

    /**
     * Gets the SOAP client of this Exchange client
     * @return zibo\library\soap\curl\Client
     */
    protected function getSOAPClient() {
        if ($this->soapClient) {
            return $this->soapClient;
        }

        $wsdl = __DIR__ . self::SERVICE_WSDL;

        $options = array(
            'curl' => $this->getCurlOptions(),
            'trace' => $this->willTrace,
            'location' => 'https://' . $this->server . self::SERVICE_PATH,
            'exceptions' => true,
            'typemap' => array(
                array(
                    'type_name' => 'NonEmptyArrayOfItemChangesType',
                    'type_ns' => self::SCHEMA_TYPES,
                    'to_xml' => array('zibo\library\exchange\type\NonEmptyArrayOfItemChanges', 'toXml'),
                ),
                array(
                    'type_name' => 'NonEmptyArrayOfAllItemsType',
                    'type_ns' => self::SCHEMA_TYPES,
                    'to_xml' => array('zibo\library\exchange\type\NonEmptyArrayOfAllItems', 'toXml'),
                ),
                array(
                    'type_name' => 'NonEmptyArrayOfBaseItemIdsType',
                    'type_ns' => self::SCHEMA_TYPES,
                    'to_xml' => array('zibo\library\exchange\type\NonEmptyArrayOfBaseItemIds', 'toXml'),
                ),
                array(
                    'type_name' => 'NonEmptyArrayOfBaseFolderIdsType',
                    'type_ns' => self::SCHEMA_TYPES,
                    'to_xml' => array('zibo\library\exchange\type\NonEmptyArrayOfBaseFolderIds', 'toXml'),
                ),
                array(
                    'type_name' => 'NonEmptyArrayOfFoldersType',
                    'type_ns' => self::SCHEMA_TYPES,
                    'to_xml' => array('zibo\library\exchange\type\NonEmptyArrayOfFolders', 'toXml'),
                ),
                array(
                    'type_name' => 'TargetFolderIdType',
                    'type_ns' => self::SCHEMA_TYPES,
                    'to_xml' => array('zibo\library\exchange\type\TargetFolderId', 'toXml'),
                ),
                array(
                    'type_name' => 'RestrictionType',
                    'type_ns' => self::SCHEMA_TYPES,
                    'to_xml' => array('zibo\library\exchange\type\Restriction', 'toXml'),
                ),
            ),
        );

        $this->soapClient = new SoapClient($wsdl, $options);

        $soapHeader = new SoapHeader(
            self::SCHEMA_TYPES,
            'RequestServerVersion Version="' . $this->version . '"'
        );

        $this->soapClient->__setSoapHeaders($soapHeader);

        return $this->soapClient;
    }

    /**
     * Gets the options for curl
     * @return array
     */
    protected function getCurlOptions() {
        return array(
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => true,
            CURLOPT_HTTPAUTH => CURLAUTH_NTLM,
            CURLOPT_USERPWD => $this->username . ':' . $this->password,
        );
    }

}