<?php

namespace zibo\library\box;

/**
 * The Folder class will contain an array of files, but will also have
 * its own attributes. In addition. I've provided a series of CRUD operations that
 * can be performed on a folder.
 * @author Angelo R
 */
class BoxFolder {

    /**
     * The attributes of this folder
     * @var array
     */
    private $attributes;

    /**
     * The files in this folder
     * @var array
     */
    private $files;

    /**
     * The subfolders in this folder
     * @var array
     */
    private $folders;

    /**
     * Constructs a new folder object
     * @return null
     */
    public function __construct() {
        $this->attributes = array();
        $this->files = array();
        $this->folders = array();

    }

    /**
     * Gets or sets folder attributes.
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

    /**
     * Gets the files of this folder
     * @return array
     */
    public function getFiles() {
        return $this->files;
    }

    /**
     * Gets the subfolders of this folder
     * @return array
     */
    public function getFolders() {
        return $this->folders;
    }

    /**
     *
     * Imports the tree structure and allows us to provide some extended functionality
     * at some point. Don't run import manually. It expects certain things that are
     * delivered through the API. Instead, if you need a tree structure of something,
     * simply call Client->folder(folder_id); and it will automatically return
     * the right stuff.
     *
     * Due to an inconsistency with the Box.net ReST API, this section invovles a few
     * more checks than normal to ensure that all the necessary values are available
     * when doing the import.
     * @param array $tree
     * @return null
     */
    public function import(array $tree) {
        foreach ($tree['@attributes'] as $key => $val) {
            $this->attributes[$key] = $val;
        }

        if (array_key_exists('folders', $tree)) {
            if (array_key_exists('folder', $tree['folders'])) {
                if (array_key_exists('@attributes', $tree['folders']['folder'])) {
                    // this is the case when there is a single folder within the root
                    $boxFolder = new BoxFolder();
                    $boxFolder->import($tree['folders']['folder']);

                    $this->folders[] = $boxFolder;
                } else {
                    // this is the case when there are multiple folders within the root
                    foreach($tree['folders']['folder'] as $folder) {
                        $boxFolder = new BoxFolder();
                        $boxFolder->import($folder);

                        $this->folders[] = $boxFolder;
                    }
                }
            }
        }

        if (array_key_exists('files', $tree)) {
            if (array_key_exists('file', $tree['files'])) {
                if (array_key_exists('@attributes', $tree['files']['file'])) {
                    // this is the case when there is a single file within a directory
                    $boxFile = new BoxFile();
                    $boxFile->import($tree['files']['file']);

                    $this->files[] = $boxFile;
                } else {
                    // this is the case when there are multiple files in a directory
                    foreach ($tree['files']['file'] as $file) {
                        $boxFile = new BoxFile();
                        $boxFile->import($file);

                        $this->files[] = $boxFile;
                    }
                }
            }
        }
    }

}