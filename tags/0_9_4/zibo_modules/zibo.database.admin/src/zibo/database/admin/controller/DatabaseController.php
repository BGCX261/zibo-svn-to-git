<?php

namespace zibo\database\admin\controller;

use zibo\admin\controller\AbstractController;
use zibo\admin\view\DownloadView;

use zibo\core\Zibo;

use zibo\database\admin\form\ConnectionDefaultForm;
use zibo\database\admin\form\ConnectionForm;
use zibo\database\admin\model\ConnectionModel;
use zibo\database\admin\table\ConnectionTable;
use zibo\database\admin\view\ConnectionFormView;
use zibo\database\admin\view\ConnectionsView;
use zibo\database\admin\Module;

use zibo\library\archive\ArchiveFactory;
use zibo\library\config\Config;
use zibo\library\filesystem\File;
use zibo\library\database\DatabaseManager;
use zibo\library\database\Dsn;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\ValidationError;

use \Exception;

/**
 * Controller for the database connection administration
 */
class DatabaseController extends AbstractController {

    /**
     * Action to add a new connection
     * @var string
     */
    const ACTION_ADD = 'add';

    /**
     * Action to set the default connection
     * @var string
     */
    const ACTION_DEFAULT = 'default';

    /**
     * Action to edit a connection
     * @var string
     */
    const ACTION_EDIT = 'edit';

    /**
     * Action to export a database
     * @var string
     */
    const ACTION_EXPORT = 'export';

    /**
     * Action to save a connection
     * @var string
     */
    const ACTION_SAVE = 'save';

    /**
     * Class name of the archive factory
     * @var string
     */
    const CLASS_ARCHIVE_FACTORY = 'zibo\library\archive\ArchiveFactory';

    /**
     * Configuration key for a static export filename
     * @var string
     */
    const CONFIG_EXPORT_FILENAME = 'database.admin.export.filename';

    /**
     * Translation key for the convert to UTF8 button
     * @var string
     */
	const TRANSLATION_BUTTON_CONVERT_UTF8 = 'database.button.convert.utf8';

    /**
     * Translation key for the delete button
     * @var string
     */
	const TRANSLATION_BUTTON_DELETE = 'button.delete';

	/**
	 * Translation key for the connection delete error
	 * @var string
	 */
	const TRANSLATION_CONNECTION_NOT_DELETED = 'database.error.connection.not.deleted';

	/**
	 * Translation key for the connection not found error
	 * @var string
	 */
	const TRANSLATION_CONNECTION_NOT_FOUND = 'database.error.connection.not.found';

	/**
	 * Translation key for the connection deleted information
	 * @var string
	 */
	const TRANSLATION_CONNECTION_DELETED = 'database.information.connection.deleted';

	/**
	 * Translation key for the connection converted to UTF8 information
	 * @var string
	 */
	const TRANSLATION_CONNECTION_CONVERTED_UTF8 = 'database.information.connection.converted.utf8';

	/**
	 * Translation key for the connection not converted to UTF8 error
	 * @var string
	 */
	const TRANSLATION_CONNECTION_NOT_CONVERTED_UTF8 = 'database.error.connection.not.converted.utf8';

	/**
	 * Translation key for the connection saved information
	 * @var string
	 */
	const TRANSLATION_CONNECTION_SAVED = 'database.information.connection.saved';

	/**
	 * Translation key for the default connection information
	 * @var string
	 */
	const TRANSLATION_CONNECTION_DEFAULT = 'database.information.connection.default';

	/**
	 * Translation key for the add connection title
	 * @var string
	 */
	const TRANSLATION_TITLE_ADD = 'database.action.connection.add';

	/**
	 * Translation key for the edit connection title
	 * @var string
	 */
	const TRANSLATION_TITLE_EDIT = 'database.action.connection.edit';

	/**
	 * Instance of the database connection model
	 * @var zibo\database\model\ConnectionModel
	 */
	private $model;

	/**
	 * Hook before every action
	 * @return null
	 */
	public function preAction() {
		$this->model = new ConnectionModel();
	}

	/**
	 * Action to show an overview of the database connections, the available protocols and a default connection form
	 * @return null
	 */
	public function indexAction() {
		$connections = $this->model->getConnections();
		$protocols = $this->model->getProtocols();

		$translator = $this->getTranslator();
		$convertUtf8Label = $translator->translate(self::TRANSLATION_BUTTON_CONVERT_UTF8);
		$deleteLabel = $translator->translate(self::TRANSLATION_BUTTON_DELETE);

		$basePath = $this->request->getBasePath();

		$urlAdd = $basePath . '/' . self::ACTION_ADD;

		$defaultConnection = null;
		$defaultForm = null;
		if ($connections) {
			$defaultConnection = $this->model->getDefaultConnection();
            $defaultForm = new ConnectionDefaultForm($basePath . '/' . self::ACTION_DEFAULT, $connections, $defaultConnection);
            $defaultConnection = $defaultConnection->getName();
		}

		$connectionAction = $basePath . '/' . self::ACTION_EDIT . '/';
		$exportAction = $basePath . '/' . self::ACTION_EXPORT . '/';

		$table = new ConnectionTable($connections, $basePath, $connectionAction, $exportAction, $defaultConnection);
		$table->addAction($convertUtf8Label, array($this, 'convertUtf8Action'));
		$table->addAction($deleteLabel, array($this, 'deleteAction'));
		$table->processForm();

		if ($this->response->willRedirect()) {
		    return;
		}

		$view = new ConnectionsView($table, $protocols, $urlAdd, $defaultForm);
		$view->setPageTitle(Module::TRANSLATION_ADMIN, true);

		$this->response->setView($view);
	}

	/**
	 * Action to show an empty connection form
	 * @return null
	 */
	public function addAction() {
		$form = new ConnectionForm($this->request->getBasePath() . '/' . self::ACTION_SAVE);

		$view = new ConnectionFormView($form, self::TRANSLATION_TITLE_ADD);
		$this->response->setView($view);
	}

	/**
	 * Action to show the definition of a connection in the form
	 * @param string $name Name of the connection
	 * @return null
	 */
	public function editAction($name = null) {
		if (empty($name)) {
			$this->response->setRedirect($this->request->getBasePath());
			return;
		}

		$connection = $this->model->getConnection($name);
		if (!$connection) {
			$this->addError(self::TRANSLATION_CONNECTION_NOT_FOUND, array('name' => $name));
			$this->response->setRedirect($this->request->getBasePath());
			return;
		}

		$dsn = $connection->getDriver()->getDsn()->__toString();
		$form = new ConnectionForm($this->request->getBasePath() . '/' . self::ACTION_SAVE, $name, $dsn, $name);

		$view = new ConnectionFormView($form, self::TRANSLATION_TITLE_EDIT);
		$this->response->setView($view);
	}

	/**
	 * Action to save the definition of a connection
	 * @return null
	 */
	public function saveAction() {
		$form = new ConnectionForm($this->request->getBasePath() . '/' . self::ACTION_SAVE);
		if (!$form->isSubmitted() || $form->isCancelled()) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
		}

		try {
            $form->validate();

            $name = $form->getName();
            $dsn = $form->getDsn();
            $oldName = $form->getOldName();

            $this->model->saveConnection($name, $dsn, $oldName);

            $this->addInformation(self::TRANSLATION_CONNECTION_SAVED, array('name' => $name));

            $this->response->setRedirect($this->request->getBasePath());
            return;
		} catch (ValidationException $exception) {
			$form->setValidationException($exception);
		}

		if ($form->getOldName()) {
			$title = self::TRANSLATION_TITLE_EDIT;
		} else {
			$title = self::TRANSLATION_TITLE_ADD;
		}

        $view = new ConnectionFormView($form, $title);
        $this->response->setView($view);
	}

	/**
	 * Processes the default connection form
	 * @return null
	 */
	public function defaultAction() {
		$basePath = $this->request->getBasePath();
		$connections = $this->model->getConnections();
        $defaultConnection = $this->model->getDefaultConnection();

        $form = new ConnectionDefaultForm($basePath . '/' . self::ACTION_DEFAULT, $connections, $defaultConnection);

        if ($form->isSubmitted()) {
        	$name = $form->getDefaultConnectionName();
        	$this->model->setDefaultConnection($name);
            $this->addInformation(self::TRANSLATION_CONNECTION_DEFAULT, array('name' => $name));
        }

        $this->response->setRedirect($basePath);
	}

	public function convertUtf8Action($connections) {
        if (!is_array($connections)) {
            $connections = array($connections);
        }

        foreach ($connections as $connection) {
            try {
                $driver = $connection->getDriver();
                $driver->connect();

                $definer = $driver->getDefiner();

                if (!method_exists($definer, 'convertDatabaseToUTF8')) {
                    throw new Exception('Database connection has no conversion support');
                }

                $definer->convertDatabaseToUTF8();

                $this->addInformation(self::TRANSLATION_CONNECTION_CONVERTED_UTF8, array('name' => $connection->getName()));
            } catch (Exception $exception) {
                Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
                $this->addError(self::TRANSLATION_CONNECTION_NOT_CONVERTED_UTF8, array('name' => $connection->getName()));
            }
        }

        $this->response->setRedirect($this->request->getBasePath());
	}

	/**
	 * Deletes a connection from the list
	 * @param string|array $connections String or array with connection names
	 * @return null
	 */
	public function deleteAction($connections) {
        if (!is_array($connections)) {
        	$connections = array($connections);
        }

        try {
            $this->model->deleteConnections($connections);
            $this->addInformation(self::TRANSLATION_CONNECTION_DELETED);
        } catch (Exception $exception) {
        	Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, $exception->getMessage(), $exception->getTraceAsString(), 1);
            $this->addError(self::TRANSLATION_CONNECTION_NOT_DELETED);
        }

        $this->response->setRedirect($this->request->getBasePath());
	}

	/**
	 * Exports the database of a connection to SQL
	 * @param string $databaseName Name of the connection to export
	 * @param string $dateFormat Format for the date used in the filename
	 * @return null
	 */
    public function exportAction($databaseName = null, $dateFormat = 'Ymd') {
        $databaseManager = DatabaseManager::getInstance();

        $database = $databaseManager->getConnection($databaseName);
        if (!$databaseName) {
            $databaseName = $databaseManager->getDefaultConnectionName();
        }

        $timestamp = date($dateFormat);
        $fileName = Zibo::getInstance()->getConfigValue(self::CONFIG_EXPORT_FILENAME, $databaseName . '-' . $timestamp);

        $directory = new File(Zibo::DIRECTORY_APPLICATION, Zibo::DIRECTORY_DATA);

        $sqlFile = new File($directory, $fileName . '.sql');

        $sqlParent = $sqlFile->getParent();
        $sqlParent->create();

        $database->export($sqlFile);

        if (class_exists(self::CLASS_ARCHIVE_FACTORY)) {
            $this->exportFile = new File($directory, $fileName . '.zip');

            $archive = ArchiveFactory::getInstance()->getArchive($this->exportFile);
            $archive->compress($sqlFile);

            $sqlFile->delete();
        } else {
            $this->exportFile = $sqlFile;
        }

        $view = new DownloadView($this->exportFile);
        $this->response->setView($view);

        Zibo::getInstance()->registerEventListener(Zibo::EVENT_POST_RESPONSE, array($this, 'deleteExportDownload'));
    }

    /**
     * Deletes the file generated by the export action
     * @return null
     */
    public function deleteExportDownload() {
        if ($this->exportFile) {
            $this->exportFile->delete();
        }
    }

}