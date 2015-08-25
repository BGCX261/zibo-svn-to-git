<?php

namespace zibo\library\box;

use zibo\library\box\authentication\Authentication;
use zibo\library\box\exception\BoxException;

/**
 *    ___  ____ _  _     ____ ____ ____ ___     ____ _    _ ____ _  _ ___
 *      |__] |  |  \/      |__/ |___ [__   |      |    |    | |___ |\ |  |
 *      |__] |__| _/\_ ___ |  \ |___ ___]  |  ___ |___ |___ | |___ | \|  |  v0.3
 *
 *
 * Special thanks to Angelo R for the initial build of this library
 *
 *  The Box_Rest_Client is a PHP library for accessing the Box.net ReST api. It
 *  provides a PHP cURL based interface that allows access to any number of
 *  api methods that are currently in place. The code is built in a way to
 *  ensure modularity, easy updates (everything is this one file) and aims to
 *  be a simple easy to use solution for working with the excellent Box api.
 *
 *  Each of the classes in this file was licensed under the MIT Licensing
 *  agreement located below this introductory comment block.
 *
 *  Dependencies:
 *      1) cURL: This library relies on cURL to perform the http verbs. Without
 *              it, this library will not function. There are plenty of tutorials for
 *              enabling cURL on your specific system out there on the web, just a
 *              short google away.
 *      2) SimpleXML: Results from the Box api currently return (sometimes
 *              malformed) xml. It is enabled by default unless you specifically
 *              disabled it during install.. in which case you probably know what
 *              you're doing.
 *      3) This code has only been tested on the following versions of PHP:
 *              5.3.5,
 *
 *              If you have tested this code and believe it to be working on a
 *              different version, please drop me an email.
 */

// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

/**
 * This is the main API class. This is what you will be invoking when you are dealing with the
 * API.
 *
 * I would suggest reading up the example.php file instead of trying to peruse through this
 * file as it's a little much to take in at once. The example.php file provides you the basics
 * of getting started.
 *
 * If you want to inspect what various api-calls will return check out inspector.php which
 * provides a nice little interface to do just that.
 *
 * That being said, here's a quick intro to how to use this class.
 *
 * - If you are utilizing it on more than one page, definitely set the api_key within the
 *      class. It will save you a lot of time. I am going to assume that you did just that.
 * - I am assuming that you have !NOT! configured the Box_Rest_Client_Auth->store()
 *      method and it is default. Therefore, it will just return the auth_token.
 *
 * $box_rest_client = new Box_Rest_Client();
 * if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth']) {
 *  $box_rest_client->authenticate();
 * }
 * else {
 *  $_SESSION['auth'] = $box_rest_client->authenticate();
 * }
 *
 * $box_rest_client->folder(0);
 *
 * The above code will give you a nice little tree-representation of your files.
 *
 * For more in-depth examples, either take a look at the example.php file or check out
 * inspector/index.php
 *
 * @todo Proper SSL support
 *              The current SSL setup is a bit of a hack. I've just disabled SSL verification
 *              on cURL. Instead, the better idea would be to implement something like this
 *              at some point:
 *
 *              http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
 *
 * @todo File Manipulation
 * @todo Folder Manipulation
 *
 * @author Angelo R
 *
 * @see http://developers.box.net/w/page/12923958/FrontPage
 */
class BoxClient {

    /**
     * Version of the used API
     * @var string
     */
    const API_VERSION = '1.0';

    /**
     * The base URL for the API calls
     * @var string
     */
    const BASE_URL = 'https://www.box.net/api';

    /**
     * The base URL for file uploads
     * @var string
     */
    const UPLOAD_URL = 'https://www.box.net/api';

    /**
     * The API key of the user
     * @var string
     */
    private $apiKey;

    /**
     * The authenication ticket
     * @var string
     */
    private $authTicket;

    /**
     * The authentication token
     * @var string
     */
    private $authToken;

    /**
     * Implementation of the authentication storage
     * @var Authentication
     */
    private $authentication;

    /**
     * Flag to see if this is a mobile device we're running on
     * @var boolean
     */
    private $isMobile = false;

    /**
     * You need to create the client with the API key that you received when
     * you signed up for your apps.
     * @param string $apiKey your API key
     * @return null
     */
    public function __construct($apiKey, Authentication $authentication) {
        if (empty($apiKey)) {
            throw new BoxException('Invalid API Key. Please provide an API Key when creating an instance of the class');
        }

        $this->apiKey = $apiKey;
        $this->authentication = $authentication;

        $this->authToken = $this->authentication->retrieve();
    }

    /**
     * Gets the authentication token
     * @return string
     */
    public function getAuthToken() {
        return $this->authentication->retrieve();
    }

    /**
     * Sets the authentication token
     * @param string $token
     * @return null
     */
    public function setAuthToken($token) {
        $this->authentication->store($token);
        $this->authToken = $token;
    }

    /**
     * Because the authentication method is an odd one, I've provided a wrapper for
     * it that should deal with either a mobile or standard web application. You
     * will need to set the callback url from your application on the developer
     * website and that is called automatically.
     *
     * When this method notices the "auth_token" query string, it will automatically
     * call the Box_Rest_Client_Auth->store() method. You can do whatever you want
     * with it in that method. I suggest you read the bit of documentation that will
     * be present directly above the class.
     */
    public function authenticate() {
        if (array_key_exists('auth_token', $_GET)) {
            $this->authToken = $_GET['auth_token'];
            return $this->authentication->store($this->authToken);
        }

        $result = $this->get('get_ticket');
        if ($result['status'] !== 'get_ticket_ok') {
            throw new BoxException($result['status']);
        }

        $this->ticket = $result['ticket'];

        if($this->isMobile) {
            header('location: https://m.box.net/api/1.0/auth/' . $this->ticket);
        } else {
            header('location: https://www.box.net/api/1.0/auth/' . $this->ticket);
        }
    }

    /**
     * This folder method is provided as it tends to be what a lot of people will most
     * likely try to do. It returns a list of folders/files utilizing our
     * Folder and File classes instead of the raw tree array that is normally returned.
     *
     * You can totally ignore this and instead rely entirely on get/post and parse the
     * tree yourself if it doesn't quite do what you want.
     *
     * The default params ensure that the tree is returned as quickly as possible. Only
     * the first level is returned and only in a simple format.
     *
     * @param int $folderId The id of the root directory that you want to load the tree from.
     * @param string $params Any additional params you want to pass, comma separated.
     * @return Folder|null
     */
    public function folder($folderId = 0 ,$params = array('params' => array('nozip', 'onelevel', 'simple'))) {
        $params['folder_id'] = $folderId;

        $result = $this->get('get_account_tree', $params);
        if (!array_key_exists('tree', $result)) {
            return null;
        }

        $folder = new BoxFolder();
        $folder->import($result['tree']['folder']);

        return $folder;
    }

    /**
     * Since we provide a way to get information on a folder, it's only fair that we
     * provide the same interface for a file. This will grab the info for a file and
     * push it back as a File. Note that this method (for some reason)
     * gives you less information than if you got the info from the tree view.
     *
     * @param int $fileId
     * @return File
     */
    public function file($fileId) {
        $result = $this->get('get_file_info',array('file_id' => $fileId));

        // For some reason the Box.net api returns two different representations
        // of a file. In a tree view, it returns the more attributes than
        // in a standard get_file_info view. As a result, we'll just trick the
        // implementation of import in File.
        $result['@attributes'] = $result['info'];

        $file = new BoxFile();
        $file->import($result);

        return $file;
    }

    /**
     * Creates a folder on the server with the specified attributes.
     * @param Folder $folder
     * @return The status
     */
    public function create(BoxFolder $folder) {
        $params = array(
            'name' => $folder->attribute('name'),
            'parent_id' => intval($folder->attribute('parent_id')),
            'share' => intval($folder->attribute('share'))
        );

        $result = $this->post('create_folder',$params);

        if ($result['status'] == 'create_ok') {
            foreach($result['folder'] as $key => $value) {
                $folder->attribute($key, $value);
            }
        }

        return $result['status'];
    }

    /**
     * Returns the url to upload a file to the specified parent folder. Beware!
     * If you screw up the type the upload will probably still go throguh properly
     * but the results may be unexpected. For example, uploading and overwriting a
     * end up doing two very different things if you pass in the wrong kind of id
     * (a folder id vs a file id).
     *
     * For the right circumstance to use each type of file, check this:
     * http://developers.box.net/w/page/12923951/ApiFunction_Upload%20and%20Download
     *
     * @param string $type One of upload | overwrite | new_copy
     * @param int $id The id of the file or folder that you are uploading to
     * @return string
     */
    public function getUploadUrl($type = 'upload', $id = 0) {
        $url = '';

        switch(strtolower($type)) {
            case 'upload':
                $url = self::UPLOAD_URL  . '/' . self::API_VERSION . '/upload/' . $this->authToken . '/' . $id;
                break;
            case 'overwrite':
                $url = self::UPLOAD_URL  . '/' . self::API_VERSION . '/overwrite/' . $this->authToken . '/' . $id;
                break;
            case 'new_copy':
                $url = self::UPLOAD_URL  . '/' . self::API_VERSION . '/new_copy/' . $this->authToken . '/' . $id;
                break;
        }

        return $url;
    }

    /**
     *
     * Uploads the file to the specified folder. You can set the parent_id
     * attribute on the file for this to work. Because of how the API currently
     * works, be careful!! If you upload a file for the first time, but a file
     * of that name already exists in that location, this will automatically
     * overwrite it.
     *
     * If you use this method of file uploading, be warned! The file will bounce!
     * This means that the file will FIRST be uploaded to your servers and then
     * it will be uploaded to Box. If you want to bypass your server, call the
     * "upload_url" method instead.
     *
     * @param BoxFile $file
     * @param array $params A list of valid input params can be found at the Download
     *                                          and upload method list at http://developers.box.net
     *
     */
    public function upload(BoxFile $file, array $params = array()) {
        if (array_key_exists('new_copy', $params) && $params['new_copy'] && intval($file->attribute('id')) !== 0) {
            // This is a valid file for new copy, we can new_copy
            $url = $this->getUploadUrl('new_copy', $file->attribute('id'));
        } elseif(intval($file->attr('file_id')) !== 0 && !$new_copy) {
            // This file is overwriting another
            $url = $this->getUploadUrl('overwrite', $file->attribute('id'));
        } else {
            // This file is a new upload
            $url = $this->getUploadUrl('upload', $file->attribute('folder_id'));
        }

        // assign a file name during construction OR by setting $file->attribute('filename');
        // manually
        $split = explode('\\', $file->attribute('localpath'));
        $split[count($split) - 1] = $file->attribute('filename');
        $newLocalPath = implode('\\', $split);

        if(!rename($file->attribute('localpath'), $newLocalPath)) {
            throw new BoxException('Uploaded file could not be renamed.');
        }

        $file->attribute('localpath', $newLocalPath);
        $params['file'] = '@' . $file->attribute('localpath');

        $result = Rest::post($url, $params);

        // delete the localfile
        unlink($file->attribute('localpath'));

        // This exists because the API returns malformed xml.. as soon as the API
        // is fixed it will automatically check against the parsed XML instead of
        // the string. When that happens, there will be a minor update to the library.
        $failCodes = array(
            'wrong auth token',
            'application_restricted',
            'upload_some_files_failed',
            'not_enough_free_space',
            'filesize_limit_exceeded',
            'access_denied',
            'upload_wrong_folder_id',
            'upload_invalid_file_name'
        );

        if (in_array($result, $failCodes)) {
            return $result;
        } else {
            $result = $this->parseResult($result);
        }

        // only import if the status was successful
        if ($result['status'] == 'upload_ok') {
            $file->import($result['files']['file']);
        }

        return $result['status'];
    }

    /**
     * Executes an api function using get with the required opts. It will attempt to
     * execute it regardless of whether or not it exists.
     * @param string $method The API function
     * @param array $options The options for the function (get parameters)
     * @return array
     */
    public function get($method, array $options = array()) {
        $options = $this->setOptions($options);
        $url = $this->buildUrl($method, $options);

        $data = Rest::get($url);

        return $this->parseResult($data);
    }

    /**
    * Executes an API function using post with the required options. It will
    * attempt to execute it regardless of whether or not it exists.
    * @param string $method The API function
    * @param array $options The options for the function (get parameters)
    * @param array $parameters The parameters for the post (post parameters)
    * @return array
    */
    public function post($method, array $options = array(), array $parameters = array()) {
        $options = $this->setOptions($options);
        $url = $this->buildUrl($method, $options);

        $data = Rest::post($url, $parameters);

        return $this->parseResult($data);
    }

    /**
     * To minimize having to remember things, get/post will automatically
     * call this method to set some default values as long as the default
     * values don't already exist.
     * @param array $options
     * @return array The provided options with the API key and the authentication token set if it was not set
     */
    private function setOptions(array $options) {
        if (!array_key_exists('api_key', $options)) {
            $options['api_key'] = $this->apiKey;
        }

        if (!array_key_exists('auth_token', $options)) {
            if (isset($this->authToken) && !empty($this->authToken)) {
                $options['auth_token'] = $this->authToken;
            }
        }

        return $options;
    }

    /**
     * Build the final API URL that we will be curling. This will allow us to
     * get the needed results.
     * @param string $method The API function to call
     * @param array $options Arguments for the function
     * @return string
     */
    private function buildUrl($method, array $options) {
        $base = self::BASE_URL . '/' . self::API_VERSION . '/rest';
        $base .= '?action=' . $method;

        foreach ($options as $key => $value) {
            if (is_array($value)) {
                foreach($value as $subvalue) {
                    $base .= '&' . $key . '[]=' . $subvalue;
                }
            } else {
                $base .= '&' . $key . '=' . $value;
            }
        }

        return $base;
    }

    /**
     * Converts the XML we received into an array for easier messing with.
     * Obviously this is a cheap hack and a few things are probably lost along
     * the way (types for example), but to get things up and running quickly,
     * this works quite well.
     * @param string $result
     * @return array
     */
    private function parseResult($result) {
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        return $array;
    }

}