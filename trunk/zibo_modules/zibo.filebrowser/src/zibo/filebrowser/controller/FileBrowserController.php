<?php

namespace zibo\filebrowser\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\view\BaseView;
use zibo\admin\view\DownloadView;

use zibo\core\Response;
use zibo\core\Zibo;

use zibo\filebrowser\form\DirectoryForm;
use zibo\filebrowser\form\EditorForm;
use zibo\filebrowser\form\RenameForm;
use zibo\filebrowser\form\UploadForm;
use zibo\filebrowser\model\FileBrowser;
use zibo\filebrowser\table\BrowserTable;
use zibo\filebrowser\table\ClipboardTable;
use zibo\filebrowser\view\BrowserView;
use zibo\filebrowser\view\ClipboardView;
use zibo\filebrowser\view\CreateView;
use zibo\filebrowser\view\EditorView;
use zibo\filebrowser\view\RenameView;
use zibo\filebrowser\view\UploadView;
use zibo\filebrowser\Module;

use zibo\library\archive\Archive;
use zibo\library\archive\ArchiveFactory;
use zibo\library\filesystem\File;
use zibo\library\html\meta\RefreshMeta;
use zibo\library\html\table\Table;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;
use zibo\library\String;
use zibo\library\Structure;

use zibo\ZiboException;

use \Exception;

/**
 * Controller of the file browser application
 */
class FileBrowserController extends AbstractController {

    /**
     * Action to create a directory
     * @var string
     */
    const ACTION_CREATE = 'create';

    /**
     * Action to download a file
     * @var string
     */
    const ACTION_DOWNLOAD = 'download';

    /**
     * Action to edit a file
     * @var string
     */
    const ACTION_EDIT = 'edit';

    /**
     * Action to view a path
     * @var string
     */
    const ACTION_PATH = 'path';

    /**
     * Action to rename a file
     * @var string
     */
    const ACTION_RENAME = 'rename';

    /**
     * Action to upload a file
     * @var string
     */
    const ACTION_UPLOAD = 'upload';

    /**
     * The default archive extension
     * @var string
     */
    const ARCHIVE_EXTENSION = 'zip';

    /**
     * URL query argument for the order field
     * @var string
     */
    const ARGUMENT_ORDER_FIELD = 'orderField';

    /**
     * URL query argument for the order direction
     * @var string
     */
    const ARGUMENT_ORDER_DIRECTION = 'orderDirection';

    /**
     * Full class name of the archive factory
     * @var string
     */
    const CLASS_ARCHIVE_FACTORY = 'zibo\\library\\archive\\ArchiveFactory';

    /**
     * Configuration key for the extension of editable files
     * @var string
     */
    const CONFIG_EXTENSIONS = 'filebrowser.extension';

    /**
     * Configuration key for the root path of the file browser
     * @var string
     */
    const CONFIG_PATH = 'filebrowser.path';

    /**
     * Name of the event before running the file browser
     * @var string
     */
    const EVENT_PRE_ACTION = 'filebrowser.action.pre';

    /**
     * Session key for the contents of the clipboard
     * @var string
     */
    const SESSION_CLIPBOARD = 'filebrowser.clipboard';

    /**
     * Session key for the download filename
     * @var string
     */
    const SESSION_DOWNLOAD = 'filebrowser.download';

    /**
     * Session key for the field of the order
     * @var string
     */
    const SESSION_ORDER_FIELD = 'filebrowser.order.field';

    /**
     * Session key for the direction of the order
     * @var string
     */
    const SESSION_ORDER_DIRECTION = 'filebrowser.order.direction';

    /**
     * Translation key for the create directory button
     * @var string
     */
    const TRANSLATION_CREATE_DIRECTORY = 'filebrowser.button.create.directory';

    /**
     * Translation key for the create file button
     * @var string
     */
    const TRANSLATION_CREATE_FILE = 'filebrowser.button.create.file';

    /**
     * Translation key for the table action to copy items to the clipboard
     * @var string
     */
    const TRANSLATION_CLIPBOARD_COPY = 'filebrowser.button.copy.clipboard';

    /**
     * Translation key for the table action to remove items from the clipboard
     * @var string
     */
    const TRANSLATION_CLIPBOARD_REMOVE = 'filebrowser.button.remove.clipboard';

    /**
     * Translation key for the table action to copy items from the clipboard
     * @var string
     */
    const TRANSLATION_COPY = 'filebrowser.button.copy';

    /**
     * Translation key for the table action to move items from the clipboard
     * @var string
     */
    const TRANSLATION_MOVE = 'filebrowser.button.move';

    /**
     * Translation key for the table action to delete items
     * @var string
     */
    const TRANSLATION_DELETE = 'filebrowser.button.delete';

    /**
     * Translation key for the delete confirmation message
     * @var string
     */
    const TRANSLATION_CONFIRM_DELETE = 'filebrowser.label.delete.confirm';

    /**
     * Translation key for the table action to download the selected items in a archive
     * @var string
     */
    const TRANSLATION_DOWNLOAD_ARCHIVE = 'filebrowser.button.download.archive';

    /**
     * Translation key for the name field in the order selection
     * @var string
     */
    const TRANSLATION_NAME = 'filebrowser.label.name';

    /**
     * Translation key for the extension field in the order selection
     * @var string
     */
    const TRANSLATION_EXTENSION = 'filebrowser.label.extension';

    /**
     * Translation key for the size field in the order selection
     * @var string
     */
    const TRANSLATION_SIZE = 'filebrowser.label.size';

    /**
     * Translation key for the error message when a path exists already
     * @var string
     */
    const TRANSLATION_ERROR_EXIST = 'filebrowser.error.exist';

    /**
     * Translation key for the error message when a path does not exist
     * @var string
     */
    const TRANSLATION_ERROR_EXIST_NOT = 'filebrowser.error.exist.not';

    /**
     * Translation key for the error message when a path is not writable
     * @var string
     */
    const TRANSLATION_ERROR_WRITABLE = 'filebrowser.error.writable';

    /**
     * Translation key for the error message when the changed path is a file
     * @var string
     */
    const TRANSLATION_ERROR_PATH_FILE = 'filebrowser.error.path.file';

    /**
     * Translation key for the error message when deleting files
     * @var string
     */
    const TRANSLATION_ERROR_DELETED = 'filebrowser.error.deleted';

    /**
     * Translation key for the information message when creating a directory
     * @var string
     */
    const TRANSLATION_INFORMATION_CREATED = 'filebrowser.information.created';

    /**
     * Translation key for the information message when renaming a file or directory
     * @var string
     */
    const TRANSLATION_INFORMATION_RENAMED = 'filebrowser.information.renamed';

    /**
     * Translation key for the information message when saving a file
     * @var string
     */
    const TRANSLATION_INFORMATION_SAVED = 'filebrowser.information.saved';

    /**
     * Translation key for the information message when uploading files
     * @var string
     */
    const TRANSLATION_INFORMATION_UPLOADED = 'filebrowser.information.uploaded';

    /**
     * Translation key for the information message when deleting files
     * @var string
     */
    const TRANSLATION_INFORMATION_DELETED = 'filebrowser.information.deleted';

    /**
     * Extensions which are editable
     * @var array
     */
    private $extensions;

    /**
     * Files of the clipboard
     * @var array
     */
    private $clipboard;

    /**
     * Instance of a file browser
     * @var zibo\filebrowser\model\FileBrowser
     */
    private $fileBrowser;

    /**
     * The current path
     * @var zibo\library\filesystem\File
     */
    private $path;

    /**
     * Constructs a new file browser controller
     * @return null
     */
    public function __construct() {
        $zibo = Zibo::getInstance();

        $rootPath = $zibo->getConfigValue(self::CONFIG_PATH);
        if (!$rootPath) {
            $rootPath = $zibo->getRootPath();
        } else {
            $rootPath = new File($rootPath);
        }

        $this->fileBrowser = new FileBrowser($rootPath);
        $this->extensions = $zibo->getConfigValue(self::CONFIG_EXTENSIONS, array());
    }

    /**
     * Sets the extensions which are editable
     * @param array $extensions
     * @return null
     */
    public function setExtensions(array $extensions) {
    	$this->extensions = $extensions;
    }

    /**
     * Gets the extensions which are editable
     * @return array
     */
    public function getExtensions() {
        return $this->extensions;
    }

    /**
     * Sets the root path of the browser
     * @param zibo\library\filesystem\File $root
     * @return null
     */
    public function setRoot(File $root) {
        $this->fileBrowser->setRoot($root);
    }

    /**
     * Gets the root path of the browser
     * @return zibo\library\filesystem\File
     */
    public function getRoot() {
        return $this->fileBrowser->getRoot();
    }

    /**
     * Hook before every action, reads the clipboard and the current path
     * @return null
     */
    public function preAction() {
        $session = $this->getSession();
        $this->clipboard = $session->get(self::SESSION_CLIPBOARD, array());

        Zibo::getInstance()->runEvent(self::EVENT_PRE_ACTION, $this);
    }

    /**
     * Hook after every action, stores the clipboard and the current path
     * @return null
     */
    public function postAction() {
        $session = $this->getSession();
        $session->set(self::SESSION_CLIPBOARD, $this->clipboard);
    }

    /**
     * Default action of the file browser
     * @return null
     */
    public function indexAction() {
        $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_PATH);
    }

    /**
     * Action to view the contents of a path

     * Every argument to this method is a part of the path. eg.
     * $fb->pathAction('application', 'config') would display application/config
     * @return null
     */
    public function pathAction() {
        $pieces = func_get_args();
        $path = $this->getFileFromPieces($pieces, false);
        $absolutePath = new File($this->fileBrowser->getRoot(), $path);

        $this->path = $path = $this->fileBrowser->getPath($path);

        if ($absolutePath) {
            if (!$absolutePath->exists()) {
                $this->addError(self::TRANSLATION_ERROR_EXIST_NOT, array('path' => $path));
                $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
            } elseif (!$absolutePath->isDirectory()) {
                $this->addError(self::TRANSLATION_ERROR_PATH_FILE, array('path' => $path));
                $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
            }

            if ($this->response->getStatusCode() == Response::STATUS_CODE_NOT_FOUND) {
                $this->response->setView(new BaseView());
                return;
            }
        }

        $basePath = $this->request->getBasePath() . '/';

        $pathAction = $basePath . self::ACTION_PATH;
        $renameAction = $basePath . self::ACTION_RENAME . '/';
        $editAction = $basePath . self::ACTION_EDIT . '/';
        $tableAction = $basePath . self::ACTION_PATH . ($path ? '/' . $path : '');
        $downloadAction = $basePath . self::ACTION_DOWNLOAD . ($path ? '/' . $path : '');
        $createDirectoryAction = $basePath . self::ACTION_CREATE . ($path ? '/' . $path : '');
        $createFileAction = $basePath . self::ACTION_EDIT . ($path ? '/' . $path : '');

        $uploadForm = new UploadForm($tableAction, $absolutePath);

        if ($uploadForm->isSubmitted()) {
            try {
                $uploadForm->validate();

                $files = $uploadForm->getFiles();
                foreach ($files as $file) {
                    $file = $this->fileBrowser->getPath(new File($file), false);
                    $this->addInformation(self::TRANSLATION_INFORMATION_UPLOADED, array('path' => $file));
                }

                $this->response->setRedirect($tableAction);
                return;
            } catch (ValidationException $e) {

            }
        }

        $orderField = $this->getArgument(self::ARGUMENT_ORDER_FIELD);
        $orderDirection = $this->getArgument(self::ARGUMENT_ORDER_DIRECTION);

        $browserTable = $this->getBrowserTable($tableAction, $path, $pathAction, $downloadAction, $renameAction, $editAction, $orderField, $orderDirection);

        if ($this->response->willRedirect() || $this->response->getView()) {
            return;
        }

        $view = new BrowserView($browserTable);
        $view->setPageTitle(Module::TRANSLATION_FILE_BROWSER, true);

        if ($this->getSession()->get(self::SESSION_DOWNLOAD)) {
            // an archive is waiting, show the page but redirect to the download itself
            $view->addMeta(new RefreshMeta($basePath . self::ACTION_DOWNLOAD, 0));
        }

        $clipboardTable = null;
        if ($this->clipboard) {
            $clipboardTable = $this->getClipboardTable($tableAction);
        }

        if (!$absolutePath->isWritable()) {
            $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $path));
            $uploadForm->setIsDisabled(true, UploadForm::BUTTON_SUBMIT);
        }

        $sidebar = $view->getSidebar();
        $sidebar->addAction($createDirectoryAction, self::TRANSLATION_CREATE_DIRECTORY, true);
        $sidebar->addAction($createFileAction, self::TRANSLATION_CREATE_FILE, true);
        $sidebar->addPanel(new UploadView($uploadForm));
        $sidebar->addPanel(new ClipboardView($clipboardTable));

        $this->response->setView($view);
    }

    /**
     * Action to download a file.
     *
     * Every argument to this method is a part of the file name. eg.
     * $fb->downloadAction('application', 'config', 'system.ini') would access application/config/system.ini
     * @return null
     */
    public function downloadAction() {
        $pieces = func_get_args();
        if (!$pieces) {
            $session = $this->getSession();

            $downloadFile = $session->get(self::SESSION_DOWNLOAD);
            if ($downloadFile) {
                $session->set(self::SESSION_DOWNLOAD);
                $this->setDownloadView($downloadFile);
            } else {
                $this->setError404();
            }

            return;
        }

        $file = $this->getFileFromPieces($pieces);

        $view = new DownloadView($file);

        $this->response->setView($view);
    }

    /**
     * Action to create a new directory
     *
     * Every argument to this method is a part of the create path. eg.
     * $fb->createAction('application', 'test') would create application/test
     * @return null
     */
    public function createAction() {
        $pieces = func_get_args();
        $path = $this->getFileFromPieces($pieces, false);
        $absolutePath = new File($this->fileBrowser->getRoot(), $path);

        if (!$absolutePath->exists()) {
            $this->addError(self::TRANSLATION_ERROR_EXIST_NOT, array('path' => $this->fileBrowser->getPath($path)));

            $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
            $this->response->setView(new BaseView());
            return;
        }

        $basePath = $this->request->getBasePath();
        $redirectUrl = $basePath . '/' . self::ACTION_PATH . ($path ? '/' . $path : '');

        $form = new DirectoryForm($basePath . '/' . self::ACTION_CREATE . ($path ? '/' . $path : ''), $path);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($redirectUrl);
                return;
            }

            try {
                $form->validate();

                $name = $form->getFileName();
                $name = String::safeString($name);

                $newPath = new File($absolutePath, $name);
                $newPath = $newPath->getCopyFile();
                $newPath->create();

                $this->addInformation(self::TRANSLATION_INFORMATION_CREATED, array('path' => $this->fileBrowser->getPath($newPath, false)));
                $this->response->setRedirect($redirectUrl);
                return;
            } catch (ValidationException $e) {

            } catch (Exception $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);

                $this->addError(self::TRANSLATION_ERROR, array('error' => $exception->getMessage()));
                $this->response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
            }
        }

        if (!$absolutePath->isWritable()) {
            $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $this->fileBrowser->getPath($path)));
            $form->setIsDisabled(true, DirectoryForm::BUTTON_SUBMIT);
        }

        $view = new CreateView($form, $this->fileBrowser->getPath($path));
        $view->setPageTitle(Module::TRANSLATION_FILE_BROWSER, true);

        $this->response->setView($view);
    }

    /**
     * Action to rename a file or directory
     *
     * Every argument to this method is a part of the rename path. eg.
     * $fb->renameAction('application', 'data', 'test.txt') would rename to application/data/test.txt
     * @return null
     */
    public function renameAction() {
        $pieces = func_get_args();
        $file = $this->getFileFromPieces($pieces, false);

        if (!$file) {
            $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
        } else {
            $absoluteFile = new File($this->fileBrowser->getRoot(), $file);

            if (!$absoluteFile->exists()) {
                $this->addError(self::TRANSLATION_ERROR_EXIST_NOT, array('path' => $file));
                $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
            }
        }

        if ($this->response->getStatusCode() == Response::STATUS_CODE_NOT_FOUND) {
            $this->response->setView(new BaseView());
            return;
        }

        $basePath = $this->request->getBasePath();
        $parent = $absoluteFile->getParent();
        $redirectUrl = $basePath . '/' . self::ACTION_PATH . '/' . $this->fileBrowser->getPath($parent, false);;

        $form = new RenameForm($basePath . '/' . self::ACTION_RENAME . '/' . $file, $file);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
                $this->response->setRedirect($redirectUrl);
                return;
            }

            try {
                $form->validate();

                $name = $form->getFileName();
                $name = String::safeString($name);
                $destination = new File($parent, $name);

                if ($destination->getAbsolutePath() != $absoluteFile->getPath() && $destination->exists()) {
                    $error = new ValidationError(self::TRANSLATION_ERROR_EXIST, '%path% exists already', array('path' => $this->fileBrowser->getPath($destination, false)));
                    $exception = new ValidationException();
                    $exception->addErrors(RenameForm::FIELD_NAME, array($error));
                    throw $exception;
                }

                $absoluteFile->move($destination);

                $this->addInformation(self::TRANSLATION_INFORMATION_RENAMED, array('old' => $file->getName(), 'new' => $name));
                $this->response->setRedirect($redirectUrl);
                return;
            } catch (ValidationException $exception) {
                $form->setValidationException($exception);
            } catch (Exception $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);

                $this->addError(self::TRANSLATION_ERROR, array('error' => $exception->getMessage()));
                $this->response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
            }
        }

        if (!$absoluteFile->isWritable() || !$absoluteFile->getParent()->isWritable()) {
            $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $file));
            $form->setIsDisabled(true, RenameForm::BUTTON_SUBMIT);
        }

        $view = new RenameView($form, $file);
        $view->setPageTitle(Module::TRANSLATION_FILE_BROWSER, true);

        $this->response->setView($view);
    }

    /**
     * Action to edit or create a file
     *
     * Every argument to this method is a part of the file to edit. eg.
     * $fb->editAction('application', 'data', 'test.txt') would show the editor for application/data/test.txt
     *
     * To create a new file in a directory, give the arguments to a directory instead of a file.
     * @return null
     */
    public function editAction() {
        $pieces = func_get_args();
        $path = $this->getFileFromPieces($pieces, false);

        $absolutePath = new File($this->fileBrowser->getRoot(), $path);

        $basePath = $this->request->getBasePath() . '/';
        $saveAction = $basePath . self::ACTION_EDIT . ($path ? '/' . $path : '');

        $name = null;
        $content = null;

        if ($path) {
            if (!$absolutePath->exists()) {
                $this->addError(self::TRANSLATION_ERROR_EXIST_NOT, array('path' => $this->fileBrowser->getPath($path)));

                $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
                $this->response->setView(new BaseView());
                return;
            }

            if (!$absolutePath->isDirectory()) {
                $name = $absolutePath->getName();
                $path = $absolutePath->getParent();
                $content = $absolutePath->read();
            } else {
                $path = $absolutePath;
            }
        } else {
            $path = $absolutePath;
        }

        $isWritable = $absolutePath->isWritable();
        $path = $this->fileBrowser->getPath($path, false);
        $form = new EditorForm($saveAction, $path, $name, $content);

        if ($form->isSubmitted()) {
            $path = $path->getPath();
            $redirectUrl = $basePath . self::ACTION_PATH . ($path != '.' ? '/' . $path : '');

            if ($form->isCancelled()) {
                $this->response->setRedirect($redirectUrl);
                return;
            }

            try {
                $form->validate();

                $content = $form->getFileContent();
                $name = $form->getFileName();
                $path = $form->getFilePath();

                $file = new File($this->fileBrowser->getRoot(), $path . '/' . $name);
                if ($file->isWritable()) {
                    $file->write($content);

                    $this->addInformation(self::TRANSLATION_INFORMATION_SAVED, array('path' => $this->fileBrowser->getPath($file, false)));
                    $this->response->setRedirect($redirectUrl);
                } else {
                    $this->addError(self::TRANSLATION_ERROR_WRITABLE, array('path' => $this->fileBrowser->getPath($file, false)));
                    $form->setIsDisabled(true, EditorForm::BUTTON_SUBMIT);
                    $isWritable = true;
                }
            } catch (ValidationException $exception) {

            } catch (Exception $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);

                $this->addError(self::TRANSLATION_ERROR, array('error' => $exception->getMessage()));
                $this->response->setStatusCode(Response::STATUS_CODE_SERVER_ERROR);
            }
        }

        if (!$isWritable) {
            $form->setIsDisabled(true, EditorForm::BUTTON_SUBMIT);
            $this->addWarning(self::TRANSLATION_ERROR_WRITABLE, array('path' => $path . ($name ? '/' . $name : '')));
        }

        $view = new EditorView($form, $path);
        $view->setPageTitle(Module::TRANSLATION_FILE_BROWSER, true);

        $this->response->setView($view);
    }

    /**
     * Action to delete a file
     * @param string|array $files String with the filename, relative to the root path of theor an array of filename'
     * @return null
     */
    public function deleteAction($files = null) {
        if ($files == null) {
            return;
        }

        if (!is_array($files)) {
            $files = array($files);
        }

        foreach ($files as $file) {
            $file = new File($this->fileBrowser->getRoot(), $file);

            try {
                $file->delete();
            } catch (Exception $exception) {
                $this->addError(self::TRANSLATION_ERROR, array('error' => $exception->getMessage()));
            }

            $path = $this->fileBrowser->getPath($file, false)->getPath();
            if (array_key_exists($path, $this->clipboard)) {
                unset($this->clipboard[$path]);
            }

            $this->addInformation(self::TRANSLATION_INFORMATION_DELETED, array('path' => $path));
        }

        $this->response->setRedirect($this->getReferer());
    }

    /**
     * Action to download a set of files an directories in an archive
     * @param array $files The files and directories to download
     * @return null
     */
    public function archiveAction(array $files = null) {
        if ($files == null) {
            $this->response->setRedirect($this->getReferer());
            return;
        }

        $path = $this->fileBrowser->getPath($this->path);

        $name = $path->getName();
        if ($name == FileBrowser::DEFAULT_PATH) {
            $translator = $this->getTranslator();
            $name = $translator->translate(BrowserTable::TRANSLATION_NAVIGATION_HOME);
        }
        $name .= '.' . self::ARCHIVE_EXTENSION;

        $rootPath = Zibo::getInstance()->getRootPath();
        $archivePath = Zibo::DIRECTORY_APPLICATION . '/' . Zibo::DIRECTORY_DATA;
        $archiveFile = new File($rootPath, $archivePath . '/' . $name);
        $archiveFile = $archiveFile->getCopyFile();

        $archive = ArchiveFactory::getInstance()->getArchive($archiveFile);
        $browserRootPath = $this->fileBrowser->getRoot();

        foreach ($files as $file) {
            $file = new File($browserRootPath, $file);
            $archive->compress($file);
        }

        $this->getSession()->set(self::SESSION_DOWNLOAD, $archiveFile);
    }

    /**
     * Action to set files and directories to the clipboard
     * @param array $files Array with relative paths
     * @return null
     */
    public function clipboardAction(array $files = null) {
        $this->response->setRedirect($this->getReferer());

        if ($files == null) {
            return;
        }

        foreach ($files as $file) {
            $file = new File($this->fileBrowser->getRoot(), $file);

            if (!$file->exists()) {
                continue;
            }

            $file = $this->fileBrowser->getPath($file, false);

            $this->clipboard[$file->getPath()] = $file;
        }
    }

    /**
     * Action to remove files and directories from the clipboard
     * @param array $files Array with relative paths
     * @return null
     */
    public function clipboardRemoveAction(array $files = null) {
        $this->response->setRedirect($this->getReferer());

        if ($files == null) {
            return;
        }

        foreach ($files as $file) {
            $file = new File($file);
            $file = $file->getPath();

            if (array_key_exists($file, $this->clipboard)) {
                unset($this->clipboard[$file]);
            }
        }
    }

    /**
     * Action to copy files and directories from the clipboard to the current path
     * @param array $files Array with the files to copy
     * @return null
     */
    public function clipboardCopyAction(array $files = null) {
        $this->clipboardFileAction('copy', $files);
    }

    /**
     * Action to move files and directories from the clipboard to the current path
     * @param array $files Array with the files to copy
     * @return null
     */
    public function clipboardMoveAction(array $files = null) {
        $this->clipboardFileAction('move', $files);
    }

    /**
     * Action to process files and directories from the clipboard to the current path
     * @param string $action The method to invoke (copy or move)
     * @param array $files Array with the files to copy
     * @return null
     */
    private function clipboardFileAction($action, array $files = null) {
        $this->response->setRedirect($this->getReferer());

        if ($files == null) {
            return;
        }

        $root = $this->fileBrowser->getRoot();
        $baseDestination = new File($root, $this->path);

        foreach ($files as $file) {
            $file = new File($file);
            $path = $file->getPath();

            if (!array_key_exists($path, $this->clipboard)) {
                continue;
            }

            $source = new File($root, $file);
            $destination = new File($baseDestination, $file->getName());
            if (!$destination->isWritable()) {
                $this->addError(self::TRANSLATION_ERROR_WRITABLE, array('path' => $this->fileBrowser->getPath($destination)));
                continue;
            }

            $source->$action($destination);

            unset($this->clipboard[$path]);
        }
    }

    /**
     * Gets the table of the browser
     * @param string $action URL to the action of the table form
     * @param zibo\library\filesystem\File $path The current path
     * @param string $pathAction URL to change the current path
     * @param string $downloadAction URL to download a file
     * @param string $renameAction URL to rename a file or directory
     * @param string $editAction URL to edit a file
     * @param string $orderField The label of the order method
     * @param string $orderDirection The order direction
     * @return zibo\filebrowser\table\BrowserTable
     */
    private function getBrowserTable($action, File $path = null, $pathAction = null, $downloadAction = null, $renameAction = null, $editAction = null, $orderField = null, $orderDirection = null) {
        $table = new BrowserTable($action, $this->fileBrowser, $path, $pathAction, $downloadAction);
        $table->addRenameAction($renameAction);
        $table->addEditAction($editAction, $this->getExtensions());

        $translator = $this->getTranslator();

        if (class_exists(self::CLASS_ARCHIVE_FACTORY)) {
            $table->addAction($translator->translate(self::TRANSLATION_DOWNLOAD_ARCHIVE), array($this, 'archiveAction'));
        }
        $table->addAction($translator->translate(self::TRANSLATION_CLIPBOARD_COPY), array($this, 'clipboardAction'));
        $table->addAction($translator->translate(self::TRANSLATION_DELETE), array($this, 'deleteAction'), $translator->translate(self::TRANSLATION_CONFIRM_DELETE));

        $table->addOrderMethod($translator->translate(self::TRANSLATION_NAME), array($this->fileBrowser, 'orderByNameAscending'), array($this->fileBrowser, 'orderByNameDescending'));
        $table->addOrderMethod($translator->translate(self::TRANSLATION_EXTENSION), array($this->fileBrowser, 'orderByExtensionAscending'), array($this->fileBrowser, 'orderByExtensionDescending'));
        $table->addOrderMethod($translator->translate(self::TRANSLATION_SIZE), array($this->fileBrowser, 'orderBySizeAscending'), array($this->fileBrowser, 'orderBySizeDescending'));

        $session = $this->getSession();

        if (!$orderDirection) {
            $orderDirection = $session->get(self::SESSION_ORDER_DIRECTION, BrowserTable::ORDER_DIRECTION_ASC);
        }
        if (!$orderField) {
            $orderField = $session->get(self::SESSION_ORDER_FIELD, $translator->translate(self::TRANSLATION_NAME));
        }

        $table->setOrderDirection($orderDirection);
        $table->setOrderMethod($orderField);

        $table->processForm();

        $orderField = $table->getOrderMethod();

        $session->set(self::SESSION_ORDER_FIELD, $orderField);
        $session->set(self::SESSION_ORDER_DIRECTION, $orderDirection);

        if ($orderDirection == BrowserTable::ORDER_DIRECTION_ASC) {
            $orderDirection = BrowserTable::ORDER_DIRECTION_DESC;
        } else {
            $orderDirection = BrowserTable::ORDER_DIRECTION_ASC;
        }
        $orderQuery = '?' . self::ARGUMENT_ORDER_FIELD . '=' . $orderField . '&' . self::ARGUMENT_ORDER_DIRECTION . '=' . $orderDirection;

        $table->setOrderDirectionUrl($action . $orderQuery);

        return $table;
    }

    /**
     * Gets the table of the clipboard
     * @param string $action URL to the action of the table form
     * @return zibo\filebrowser\table\ClipboardTable
     */
    private function getClipboardTable($action) {
        $table = new ClipboardTable($action, $this->fileBrowser->getRoot(), $this->clipboard);

        $translator = $this->getTranslator();

        $table->addAction($translator->translate(self::TRANSLATION_COPY), array($this, 'clipboardCopyAction'));
        $table->addAction($translator->translate(self::TRANSLATION_MOVE), array($this, 'clipboardMoveAction'));
        $table->addAction($translator->translate(self::TRANSLATION_CLIPBOARD_REMOVE), array($this, 'clipboardRemoveAction'));

        $table->processForm();

        return $table;
    }

    /**
     * Gets the file object from the provided pieces
     * @param array $pieces Pieces of the file name from the request
     * @param boolean $addRoot Flag to see if the root of the browser should be added
     * @return zibo\library\filesystem\File The file object of the pieces
     * @throws zibo\ZiboException when the path of the file is not in the root of the browser
     * @throws zibo\ZiboException when the file does not exist and the $checkExistance argument is true
     */
    private function getFileFromPieces(array $pieces, $addRoot = true) {
        if (empty($pieces)) {
            return null;
        }

        $path = implode(File::DIRECTORY_SEPARATOR, $pieces);

        if ($addRoot) {
            $file = new File($this->fileBrowser->getRoot(), $path);
        } else {
            $file = new File($path);
        }

        return $file;
    }

}