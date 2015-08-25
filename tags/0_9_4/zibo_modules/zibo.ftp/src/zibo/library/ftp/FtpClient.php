<?php

namespace zibo\library\ftp;

use zibo\library\filesystem\File;
use zibo\library\ftp\exception\FtpException;

/**
 * A FTP client
 */
class FtpClient {

    /**
     * The host of the FTP server
     * @var string
     */
    protected $host;

    /**
     * The port of the FTP server
     * @var integer
     */
    protected $port;

    /**
     * Flag to see if this FTP connection uses SSL
     * @var boolean
     */
    protected $ssl;

    /**
     * The username of authentication
     * @var string
     */
    protected $username;

    /**
     * The password of the user
     * @var string
     */
    protected $password;

    /**
     * The handle of the FTP connection
     * @var resource
     */
    protected $handle;

    /**
     * Constructs a new FTP client
     * @param string $host The host of the FTP server
     * @param string $username The username if not connecting anonymously
     * @param string $password The password of the user
     * @param string $port The port of the FTP server
     * @param boolean $ssl Set to true to enable SSL on this connection
     * @return null
     */
    public function __construct($host, $username = 'anonymous', $password = '', $port = 21, $ssl = false) {
        $this->host = $host;
        $this->port = $port;
        $this->ssl = $ssl;

        $this->username = $username;
        $this->password = $password;

        $this->handle = null;
    }

    /**
     * Destructs the FTP client, makes sure the FTP connection is closed
     * @return null
     */
    public function __destruct() {
        $this->disconnect();
    }

    /**
     * Connects to the FTP server
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when no connection could be made
     */
    public function connect() {
        if ($this->ssl) {
            $this->handle = @ftp_ssl_connect($this->host, $this->port);
        } else {
            $this->handle = @ftp_connect($this->host, $this->port);
        }

        if (!$this->handle) {
            throw new FtpException('Could not connect to ' . $this->host);
        }

        if (!@ftp_login($this->handle, $this->username, $this->password)) {
            throw new FtpException('Could not authenticate with ' . $this->host);
        }
    }

    /**
     * Disconnects from the FTP server
     * @return null
     */
    public function disconnect() {
        if ($this->handle) {
            @ftp_close($this->handle);
        }
    }

    /**
     * Checks if the connection with the FTP server has been made
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     */
    protected function checkConnected() {
        if (!$this->handle) {
            throw new FtpException('Not connected to the FTP server');
        }
    }

    /**
     * Turns passive mode on or off
     * @param boolean $flag True to turn passive mode on, false to turn it off
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     */
    public function setPassiveMode($flag) {
        $this->checkConnected();

        if (!@ftp_pasv($this->handle, $flag)) {
            throw new FtpException('Could not turn passive mode on or off');
        }
    }

    /**
     * Sets the network timeout of this FTP connection
     * @param integer $timeout Timeout in seconds of this FTP connection
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the timeout could not be set
     */
    public function setTimeout($timeout) {
        $this->checkConnected();

        if (!@ftp_set_option($this->handle, FTP_TIMEOUT_SEC, $timeout)) {
            throw new FtpException('Could not set the timeout to ' . $timeout);
        }
    }

    /**
     * Downloads a file from the FTP server
     * @param string $remoteFile The source file on the FTP server
     * @param zibo\library\filesystem\File $localFile The destination to save the source in
     * @param integer $mode ASCII or binary (constants FTP_ASCII or FTP_BINARY)
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the file could not be downloaded
     */
    public function get($remoteFile, File $localFile, $mode = null) {
        $this->checkConnected();

        if ($localFile->exists() && $localFile->isDirectory()) {
            throw new FtpException('Could not download ' . $remoteFile . ': Destination ' . $localFile . ' is a directory');
        } elseif (!$localFile->isWritable()) {
            throw new FtpException('Could not download ' . $remoteFile . ': Destination ' . $localFile . ' is a not writable');
        }

        if (!$mode) {
            $mode = FTP_BINARY;
        }

        if (!@ftp_get($this->handle, $localFile->getPath(), $remoteFile, $mode)) {
            throw new FtpException('Could not download ' . $remoteFile . ': A problem occured while downloading');
        }
    }

    /**
     * Uploads a file to the FTP server
     * @param zibo\library\filesystem\File $localFile The file to upload
     * @param string $remoteFile The destination file on the FTP server
     * @param integer $mode ASCII or binary (constants FTP_ASCII or FTP_BINARY)
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the file could not be uploaded
     */
    public function put(File $localFile, $remoteFile, $mode = null) {
        $this->checkConnected();

        if (!$localFile->exists()) {
            throw new FtpException('Could not upload ' . $localFile->getName() . ': Source does not exist');
        } elseif ($localFile->isDirectory()) {
            throw new FtpException('Could not upload ' . $localFile->getName() . ': Source is a directory');
        }

        if (!$mode) {
            $mode = FTP_BINARY;
        }

        if (!@ftp_put($this->handle, $remoteFile, $localFile->getAbsolutePath(), $mode)) {
            throw new FtpException('Could not upload ' . $localFile->getName() . ': A problem occured while uploading');
        }
    }

    /**
     * Creates a new directory on the FTP server
     * @param string $path The path of the new directory
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the directory could not be created
     */
    public function createDirectory($path) {
        $this->checkConnected();

        $tokens = explode('/', $path);
        $currentPath = '';

        foreach ($tokens as $token) {
            $currentPath .= '/' . $token;
            if (!@ftp_chdir($this->handle, $currentPath)) {
                @ftp_chdir($this->handle, '/');
                if (!ftp_mkdir($this->handle, $currentPath)) {
                    throw new FtpException('Could not create directory ' . $path);
                }
            }
        }
    }

    /**
     * Deletes a directory on the FTP server
     * @param string $path The path to the directory
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the directory could not be deleted
     */
    public function deleteDirectory($path) {
        $this->checkConnected();

        if (!@ftp_rmdir($this->handle, $path)) {
            throw new FtpException('Could not delete directory ' . $path);
        }
    }

    /**
     * Deletes a file on the FTP server
     * @param string $path The path to the file
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the file could not be deleted
     */
    public function deleteFile($path) {
        $this->checkConnected();

        if (!@ftp_delete($this->handle, $path)) {
            throw new FtpException('Could not delete file ' . $path);
        }
    }

    /**
     * Renames or moves a file or directory on the FTP server
     * @param string $oldPath The path of the file or directory to rename
     * @param string $newPath The new path for the file or directory
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the file could not be renamed or moved
     */
    public function rename($oldPath, $newPath) {
        $this->checkConnected();

        $size = @ftp_size($newPath);
        if ($size != -1) {
            throw new FtpException('Could not rename ' . $oldPath . ' to ' . $newPath . ': Destination already exists');
        }

        if (!@ftp_rename($this->handle, $oldPath, $newPath)) {
            throw new FtpException('Could not rename ' . $oldPath . ' to ' . $newPath);
        }
    }

    /**
     * Changes the permissions of a file or directory
     * @param string $path Path to the file or directory
     * @param integer $mode Octal value of the file permissions (eg 0644)
     * @return null
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the permissions could not be set
     */
    public function chmod($path, $mode) {
        $this->checkConnected();

        if (!@ftp_chmod($this->handle, $mode, $path)) {
            throw new FtpException('Could not set the permissions of ' . $path . ' to ' . $mode);
        }
    }

    /**
     * Gets the directories in the provided directory
     * @param string $path Path of the directory
     * @return array Array containing the names of the directories in the provided path
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the path could not be read
     */
    public function getDirectories($path) {
        $list = $this->readPath($path);

        $directories = array();
        foreach ($list as $index => $path) {
            if (@ftp_size($this->handle, $path) != -1) {
                continue;
            }

            $directories[] = $path;
        }

        return $directories;
    }

    /**
     * Gets the files in the provided directory
     * @param string $path Path of the directory
     * @return array Array containing the names of the files in the provided path
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the path could not be read
     */
    public function getFiles($path) {
        $list = $this->readPath($path);

        $files = array();
        foreach ($list as $index => $path) {
            if (@ftp_size($this->handle, $path) == -1) {
                continue;
            }

            $files[] = $path;
        }

        return $files;
    }

    /**
     * Gets the files and directories in the provided directory
     * @param string $path Path of the directory
     * @return array Array containing the names of the files and directories in the provided path
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the path could not be read
     */
    protected function readPath($path) {
        $this->checkConnected();

        if (!@ftp_chdir($this->handle, $path)) {
            throw new FtpException('Could not list the contents of ' . $path . ': Invalid path or path does not exist');
        }

        $list = @ftp_nlist($this->handle, '.');
        if ($list === false) {
            throw new FtpException('Could not list the contents of ' . $path);
        }

        return $list;
    }

    /**
     * Gets the modification time of a file
     * @param string $path Path of the file
     * @return integer UNIX timestamp of the modification time
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the modification time could not be read
     */
    public function getModificationTime($path) {
        $this->checkConnected();

        $time = @ftp_mdtm($this->handle, $path);
        if ($time == -1) {
            throw new FtpException('Could not get the modification time of ' . $path);
        }

        return $time;
    }

    /**
     * Gets the size of a file
     * @param string $path Path of the file
     * @return integer The size of the file in bytes
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     * @throws zibo\library\ftp\exception\FtpException when the size could not be read
     */
    public function getSize($path) {
        $this->checkConnected();

        $size = @ftp_size($this->handle, $path);
        if ($size == -1) {
            throw new FtpException('Could not get the size of ' . $path);
        }

        return $size;
    }

    /**
     * Executes a command on the FTP server
     * @param string $command The command to execute
     * @return array The server's response as an array of strings
     * @throws zibo\library\ftp\exception\FtpException when not connected to the FTP server
     */
    public function execute($command) {
        $this->checkConnected();

        $command = trim($command);

        return ftp_raw($this->handle, $command);
    }

}