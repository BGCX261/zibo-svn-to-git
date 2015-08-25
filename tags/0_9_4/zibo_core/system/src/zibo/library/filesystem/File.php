<?php

namespace zibo\library\filesystem;

use zibo\library\filesystem\exception\FileSystemException;
use zibo\library\String;

/**
 * File data object, facade for the filesystem library
 */
class File {

    /**
     * Suffix for a lock file
     * @var string
     */
    const LOCK_SUFFIX = '.lock';

    /**
     * Directory separator
     * @var string
     */
    const DIRECTORY_SEPARATOR = '/';

    /**
     * Path of this file object
     * @var string
     */
    private $path;

    /**
     * Flag to see if this path is a root path
     * @var boolean
     */
    private $isRootPath;

    /**
     * The lock file of this file
     * @var File
     */
    private $lockFile;

    /**
     * Construct a file object
     * @param string|File $path
     * @param string|File $child file in the provided path (optional)
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when the path is empty
     * @throws zibo\library\filesystem\exception\FileSystemException when the child is absolute
     */
    public function __construct($path, $child = null) {
        $this->isRootPath = false;
        $this->path = self::retrievePath($path, $this->isRootPath);

        if ($child != null) {
            $child = new File($child);
            if ($child->isAbsolute()) {
                throw new FileSystemException('Child ' . $child->getPath() . ' cannot be absolute');
            }

            $childPath = $child->getPath();
            if ($child->hasPharProtocol()) {
                $childPath = substr($childPath, 7);
            }

            if (!$this->isRootPath) {
                $this->path .= self::DIRECTORY_SEPARATOR;
            }

            $this->path .= $childPath;
        }

        if ($this->isInPhar() && !$this->hasPharProtocol()) {
            $this->path = 'phar://' . $this->path;
        }
    }

    /**
     * Get a string representation of this file
     * @return string the path of the file
     */
    public function __toString() {
        return $this->path;
    }

    /**
     * Retrieve the path from a string or File object
     * @param string|File $path
     * @return string the path in a string format
     */
    private static function retrievePath($path, &$isRootPath) {
        if ($path instanceof self) {
            $path = $path->getPath();
        }
        if (String::isEmpty($path)) {
            throw new FileSystemException('Cannot create a file with an empty path');
        }

        $fs = FileSystem::getInstance();

        $path = str_replace('\\', self::DIRECTORY_SEPARATOR, $path);

        $isRootPath = $fs->isRootPath($path);
        if (!$isRootPath) {
            $path = rtrim($path, self::DIRECTORY_SEPARATOR);
        }

        return $path;
    }

    /**
     * Get the name of the file
     *
     * If you provide a path like /var/www/yoursite, the name will be yoursite
     * @return string
     */
    public function getName() {
        $lastSeparator = strrpos($this->path, self::DIRECTORY_SEPARATOR);
        if ($lastSeparator === false) {
            return $this->path;
        }

        return substr($this->path, $lastSeparator + 1);
    }

    /**
     * Get the parent of the file
     *
     * If you provide a path like /var/www/yoursite, the parent will be /var/www
     * @return File the parent of the file
     */
    public function getParent() {
        $fs = FileSystem::getInstance();
        return $fs->getParent($this);
    }

    /**
     * Get the extension of the file
     * @return string if the file has an extension, you got it, else an empty string
     */
    public function getExtension() {
        $name = $this->getName();
        $extensionSeparator = strrpos($name, '.');
        if ($extensionSeparator === false) {
            return '';
        }

        return strtolower(substr($name, $extensionSeparator + 1));
    }

    /**
     * Check if the file has one of the provided extensions
     * @param string|array $extension an extension as a string or an array of extensions
     * @return true if the file has the provided extension, or one of if the $extension var is an array
     */
    public function hasExtension($extension) {
        if (!is_array($extension)) {
            $extension = array($extension);
        }
        return in_array($this->getExtension(), $extension);
    }

    /**
     * Get the path of this file
     * @return string
     */
    public function getPath() {
       return $this->path;
    }

    /**
     * Get a safe file name for a copy in order to not overwrite existing files
     *
     * When you are trying to copy a file document.txt to /tmp and your /tmp contains document.txt,
     * the copy file will be /tmp/document-1.txt. If this file also exists, it will be /tmp/document-2.txt and on and on...
     * @return File a file object containing a safe file name for a copy
     */
    public function getCopyFile() {
        if (!$this->exists()) {
            return $this;
        }

        $baseName = $this->getName();
        $parent = $this->getParent();
        $extension = $this->getExtension();
        if ($extension != '') {
            $baseName = substr($baseName, 0, (strlen($extension) + 1) * -1);
            $extension = '.' . $extension;
        }

        $index = 0;
        do {
            $index++;
            $copyFile = new File($parent, $baseName . '-' . $index . $extension);
        } while ($copyFile->exists());

        return $copyFile;
    }

    /**
     * Get the absolute path of your file
     * @return string
     */
    public function getAbsolutePath() {
        $fs = FileSystem::getInstance();
        return $fs->getAbsolutePath($this);
    }

    /**
     * Check whether this file has an absolute path
     * @return boolean true if the file has an absolute path, false if not
     */
    public function isAbsolute() {
        $fs = FileSystem::getInstance();
        return $fs->isAbsolute($this);
    }

    /**
     * Check whether this file is a root path (/, c:/, //server)
     * @return boolean true if the file is a root path, false if not
     */
    public function isRootPath() {
        return $this->isRootPath;
    }

    /**
     * Checks if the file exists
     * @return boolean true if the file exists, false if not
     */
    public function exists() {
        $fs = FileSystem::getInstance();
        return $fs->exists($this);
    }

    /**
     * Checks if the file is a directory
     * @return boolean true if the file is a directory, false if not
     */
    public function isDirectory() {
        $fs = FileSystem::getInstance();
        return $fs->isDirectory($this);
    }

    /**
     * Checks if the file is readable
     * @return boolean true if the file is readable, false if not
     */
    public function isReadable() {
        $fs = FileSystem::getInstance();
        return $fs->isReadable($this);
    }

    /**
     * Checks if the file is writable
     * @return boolean true if the file is writable, false if not
     */
    public function isWritable() {
        $fs = FileSystem::getInstance();
        return $fs->isWritable($this);
    }

    /**
     * Checks if the file is a phar, based on the extension of the file
     * @return boolean true if the file is a phar, false if not
     */
    public function isPhar() {
        return $this->getExtension() == 'phar';
    }

    /**
     * Checks if the file is in a phar, checks the path for .phar/
     * @return boolean true if the file is in a phar, false if not
     */
    public function isInPhar() {
        $match = null;

        $positionPhar = strpos($this->path, '.phar' . self::DIRECTORY_SEPARATOR);
        if ($positionPhar === false) {
            return false;
        }

        $phar = substr($this->path, 0, $positionPhar + 5);

        if ($this->hasPharProtocol($phar)) {
            $phar = substr($phar, 7);
        }

        return new File($phar);
    }

    /**
     * Checks if a path has been prefixed with the phar protocol (phar://)
     * @param string $path if none provided, the path of the file is assumed
     * @return boolean true if the protocol is prefixed, false otherwise
     */
    public function hasPharProtocol($path = null) {
        if ($path == null) {
            $path = $this->path;
        }

        return String::startsWith($path, 'phar://');
    }

    /**
     * Get the time the file was last modified
     * @return int timestamp of the modification time
     */
    public function getModificationTime() {
        $fs = FileSystem::getInstance();
        return $fs->getModificationTime($this);
    }

    /**
     * Get the size of a file
     * @return int size of a file in bytes
     */
    public function getSize() {
        $fs = FileSystem::getInstance();
        return $fs->getSize($this);
    }

    /**
     * Get the permissions of a file
     * @return int an octal value of the permissions. eg. 0755
     */
    public function getPermissions() {
        $fs = FileSystem::getInstance();
        return $fs->getPermissions($this);
    }

    /**
     * Set the permissions of a file
     * @param int an octal value of the permissions. eg. 0755
     */
    public function setPermissions($permissions) {
        $fs = FileSystem::getInstance();
        return $fs->setPermissions($this, $permissions);
    }

    /**
     * Read the file or directory
     * @param boolean $recursive When reading a directory: true to read subdirectories, false to read only the direct children
     * @return string|array if the file is not a directory, the contents of the file will be returned
     *          in a string, else the files in the directory will be returned in an array
     * @throws zibo\library\filesystem\exception\FileSystemException when the file or directory could not be read
     */
    public function read($recursive = false) {
        $fs = FileSystem::getInstance();
        return $fs->read($this, $recursive);
    }

    /**
     * Write contents to this file
     * @param string $content
     * @param boolean $append set to true to append to the file, false to overwrite (default)
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when the file could not be written
     */
    public function write($content = '', $append = false) {
        $fs = FileSystem::getInstance();
        $fs->write($this, $content, $append);
    }

    /**
     * Create a new directory from this file
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when the directory could not be created
     */
    public function create() {
        $fs = FileSystem::getInstance();
        $fs->create($this);
    }

    /**
     * Delete this file
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when the file or directory could not be deleted
     */
    public function delete() {
        $fs = FileSystem::getInstance();
        $fs->delete($this);
    }

    /**
     * Gets the lock file of this file
     * @return File The lock file of this file
     */
    public function getLockFile() {
        if (!$this->lockFile) {
            $this->lockFile = new File($this->path . self::LOCK_SUFFIX);
        }

        return $this->lockFile;
    }

    /**
     * Locks this file, locks have to be checked manually
     * @param boolean $waitForLock True to keep trying to get the lock, false to throw an exception when the file is locked
     * @param integer $waitTime Time in microseconds to wait between the lock checks
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when $waitForLock is false and the file is locked
     */
    public function lock($waitForLock = true, $waitTime = 10000) {
        $fs = FileSystem::getInstance();
        $fs->lock($this, $waitForLock, $waitTime);
    }

    /**
     * Unlocks this file
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when the file is not locked
     */
    public function unlock() {
        $fs = FileSystem::getInstance();
        $fs->unlock($this);
    }

    /**
     * Checks whether this file is locked
     * @return boolean True if the file is locked, false otherwise
     */
    public function isLocked() {
        $fs = FileSystem::getInstance();
        return $fs->isLocked($this);
    }

    /**
     * Wait until this file is unlocked
     * @param integer $waitTime Time in microseconds to wait between the lock checks
     * @return null
     */
    public function waitForUnlock($waitTime = 10000) {
        $fs = FileSystem::getInstance();
        $fs->waitForUnlock($this, $waitTime);
    }

    /**
     * Copy this file
     * @param File $destination
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when the file could not be copied
     */
    public function copy(File $destination) {
        $fs = FileSystem::getInstance();
        $fs->copy($this, $destination);
    }

    /**
     * Move this file
     * @param File $destination
     * @return null
     * @throws zibo\library\filesystem\exception\FileSystemException when the file could not be moved
     */
    public function move(File $destination) {
        $fs = FileSystem::getInstance();
        $fs->move($this, $destination);
    }

    /**
     * Pass the file through to the output
     * @return null
     */
    public function passthru() {
        while (ob_get_level() !== 0) {
            ob_end_clean();
        }

        if (!$this->isReadable()  || connection_status() != 0) {
            return false;
        }

        set_time_limit(0);

        if ($handle = fopen($this->getAbsolutePath(), 'rb')) {
            while (!feof($handle) && connection_status() == 0) {
                print(fread($handle, 1024*8));
                flush();
            }
            fclose($handle);
        }

        return connection_status() == 0 && !connection_aborted();
    }

}