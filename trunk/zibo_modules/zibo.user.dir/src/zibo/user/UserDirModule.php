<?php

namespace zibo\user;

use zibo\core\Zibo;

use zibo\filebrowser\controller\FileBrowserController;
use zibo\filebrowser\filter\ExtensionFilter;
use zibo\filebrowser\FileBrowser;

use zibo\library\filesystem\File;

use zibo\library\security\model\User;
use zibo\library\security\SecurityManager;

use zibo\tinymce\controller\TinyMCEController;

/**
 * Userdir module initializer
 */
class UserDirModule {

    /**
     * The configuration key for the user home path
     * @param string
     */
    const CONFIG_PATH = 'user.dir.path';

    /**
     * Default user home path
     * @var string
     */
    const DEFAULT_PATH = 'application/web/users';

    /**
     * Permission to decide which users gets limited to the user dir
     * @var string
     */
    const PERMISSION_USERDIR = 'user.dir.enabled';

    /**
     * The user home path
     * @var string
     */
    private $path;

    /**
     * The security manager
     * @var zibo\library\security\SecurityManager
     */
    private $securityManager;

    /**
     * Initializes this module
     * @return null
     */
    public function initialize() {
        $zibo = Zibo::getInstance();
        $zibo->registerEventListener(FileBrowserController::EVENT_PRE_ACTION, array($this, 'initializeFileBrowser'));
        $zibo->registerEventListener(TinyMCEController::EVENT_PRE_IMAGE_LIST, array($this, 'addImagesToTinyMCE'));
        $zibo->registerEventListener(TinyMCEController::EVENT_PRE_LINK_LIST, array($this, 'addLinksToTinyMCE'));

        $this->path = $zibo->getConfigValue(self::CONFIG_PATH, self::DEFAULT_PATH);
        $this->securityManager = SecurityManager::getInstance();
    }

    /**
     * Initializes the file browser to set the root path to the user directory
     * @param zibo\filebrowser\controller\FileBrowserController $controller The file browser controller
     * @return null
     */
    public function initializeFileBrowser(FileBrowserController $controller) {
        if (!$this->isUserDirectoryEnabled()) {
            return;
        }

        $userDirectory = $this->getUserDirectory();

        $controller->setRoot($userDirectory);
    }

    /**
     * Hook with TinyMCE to add images from the user directory to the images field
     * @param zibo\tinymce\controller\TinyMCEController $controller The TinyMCE controller
     * @return null
     */
    public function addImagesToTinyMCE(TinyMCEController $controller) {
        if (!$this->isUserDirectoryEnabled()) {
            return;
        }

        $filters = array(new ExtensionFilter(array('jpg', 'jpeg', 'gif', 'png')));

        $files = $this->getTinyMCEFiles($filters);

        foreach ($files as $image => $label) {
            $controller->addImage($image, $label);
        }
    }

    /**
     * Hook with TinyMCE to add files from the user directory to the anchor field
     * @param zibo\tinymce\controller\TinyMCEController $controller The TinyMCE controller
     * @return null
     */
    public function addLinksToTinyMCE(TinyMCEController $controller) {
        if (!$this->isUserDirectoryEnabled()) {
            return;
        }

        $files = $this->getTinyMCEFiles();

        foreach ($files as $link => $label) {
            $controller->addLink($link, $label);
        }
    }

    /**
     * Gets all the files from the user directory
     * @param zibo\library\security\SecurityManager $securityManager The security manager
     * @param array $filters Filters for the file browser when reading the contents of the user directory
     * @return array Array with the URL of the file as key and the label as value
     */
    private function getTinyMCEFiles(SecurityManager $securityManager, array $filters = array()) {
        // read the files
        $userDirectory = $this->getUserDirectory();

        $fileBrowser = new FileBrowser($userDirectory);
        $files = $fileBrowser->readDirectory(null, $filters, true);

        // initializes variables needed to generate the tinymce list
        $zibo = Zibo::getInstance();

        $rootPath = $zibo->getRootPath();
        $applicationPath = new File($rootPath, Zibo::DIRECTORY_APPLICATION);
        $applicationPath = $applicationPath->getAbsolutePath();

        $request = $zibo->getRequest();
        $baseUrl = $request->getBaseUrl();

        // generate the tinymce list
        $list = array();
        foreach ($files as $path => $file) {
            $file = new File($userDirectory, $file);

            $link = str_replace($applicationPath, $baseUrl, $file->getAbsolutePath());
            if (str_pos($baseUrl, $link) === false) {
                // the file is not in the root of the installation, skip it
                continue;
            }

            $label = $fileBrowser->getPath($file);

            $list[$link] = $label;
        }

        // we're done here
        return $list;
    }

    /**
     * Gets the user directory for the current user
     * @return null|zibo\library\filesystem\File The user directory
     */
    public function getUserDirectory() {
        $user = $this->securityManager->getUser();

        if (!$user) {
            return null;
        }

        $id = $user->getUserId();

        $directory = new File($this->path, $id);
        $directory->create();

        return $directory;
    }

    /**
     * Checks if the current user uses the user directory
     * @return boolean
     */
    public function isUserDirectoryEnabled() {
        $isAllowed = $this->securityManager->isPermissionAllowed(self::PERMISSION_USERDIR);

        if (!$isAllowed) {
            return false;
        }

        $user = $this->securityManager->getUser();

        // skip anonymous users and super users from the ORM security model
        if (!$user || (method_exists($user, 'isSuperUser') && $user->isSuperUser())) {
            return false;
        }

        return true;
    }

}