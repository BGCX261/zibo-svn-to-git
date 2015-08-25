<?php

namespace zibo\library\box;

use zibo\library\filesystem\File;

/**
 * The File class will contain the attributes and tags that belong
 * to a single file. In addition, I've provided a series of CRUD operations that can
 * be performed on a file.
 * @author Angelo R
 */
class BoxFile {

    /**
     * Array with the attributes of this file
     * @var array
     */
    private $attributes;

    /**
     * Array with the tags of this file
     * @var array
     */
    private $tags;

    /**
     * During construction, you can specify a path to a file and a file name. This
     * will prep the File instance for an upload. If you do not wish
     * to upload a file, simply instantiate this class without any attributes.
     *
     * If you want to fill this class with the details of a specific file, then
     * get_file_info and it will be imported into its own Box_Client_File class.
     *
     * @param zibo\library\filesystem\File $file Provide a file if you want to upload a directory or a file
     * @return null
     */
    public function __construct(File $file = null) {
        $this->attributes = array();
        $this->tags = array();

        if ($file) {
            if ($file->isDirectory) {
                $this->attribute('localpath', $file->getPath());
            } else {
                $this->attribute('localpath', $file->getParent()->getPath());
                $this->attribute('filename', $file->getName());
            }
        }
    }

    /**
     * Imports the file attributes and tags. At some point we can add further
     * methods to make this a little more useful (a json method perhaps?)
     * @param array $file Array with the @attributes key and optionally the tags key
     * @return null
     */
    public function import(array $file) {
        foreach ($file['@attributes'] as $key => $val) {
            $this->attributes[$key] = $val;
        }

        if (array_key_exists('tags', $file)) {
            foreach ($file['tags'] as $i => $tag) {
                $tags[$i] = $tag;
            }
        }
    }

    /**
     * Gets or sets file attributes. For a complete list of attributes please
     * check the info object (get_file_info)
     * @param string $key
     * @param mixed $value
     * @return mixed The value of the requested attribute
     */
    public function attribute($key, $value = null) {
        if (empty($value) && array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        } else {
            $this->attributes[$key] = $value;
        }
    }

    public function tag() {

    }

    /**
     * Gets the download link to a particular file. You will need to manually pass in
     * the authentication token for the download link to work.
     * @param Client $box A reference to the client library.
     * @param int $version The version number of the file to download, leave blank if you want to download the latest version.
     * @return string $url The url link to the download
     */
    public function getDownloadUrl(Client $box, $version = 0) {
        $url = Client::BASE_URL . '/' . Client::API_VERSION;
        $authToken = $box->getAuthToken();

        if ($version == 0) {
            // not a specific version download
            $url .= '/download/' . $authToken.'/' . $this->attribute('id');
        } else {
            // downloading a certain version
            $url .= '/download_version/' . $authToken . '/' . $this->attribute('id') . '/' . $version;
        }

        return $url;
    }

}