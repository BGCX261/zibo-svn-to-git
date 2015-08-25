<?php

namespace zibo\filebrowser\table;

use zibo\filebrowser\controller\FileBrowserController;
use zibo\filebrowser\model\filter\DirectoryFilter;
use zibo\filebrowser\model\FileBrowser;
use zibo\filebrowser\table\decorator\EditActionDecorator;
use zibo\filebrowser\table\decorator\FileDecorator;
use zibo\filebrowser\table\decorator\FileOptionDecorator;
use zibo\filebrowser\table\decorator\RenameActionDecorator;

use zibo\library\filesystem\File;
use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\html\table\HierarchicTable;
use zibo\library\i18n\I18n;
use zibo\library\Session;

/**
 * Table to show the contents of a directory
 */
class BrowserTable extends HierarchicTable {

    /**
     * The name of the table
     * @var string
     */
    const NAME = 'formBrowser';

    /**
     * Translation key for the label of the breadcrumbs
     * @var string
     */
    const TRANSLATION_NAVIGATION = 'filebrowser.label.navigation';

    /**
     * Translation key for the home crumb of the breadcrumbs
     * @var string
     */
    const TRANSLATION_NAVIGATION_HOME = 'filebrowser.label.navigation.home';

    /**
     * The file browser
     * @var zibo\filebrowser\model\FileBrowser
     */
    private $fileBrowser;

    /**
     * Constructs a new file browser table
     * @param string $action URL where the table form should point to
     * @param zibo\filebrowser\model\Filebrowser $fileBrowser
     * @param zibo\library\filesystem\File $path The path to display in the table
     * @param string $directoryAction URL to the action behind a directory
     * @param string $fileAction URL to the action behind a file
     */
    public function __construct($action, Filebrowser $fileBrowser, File $path = null, $directoryAction = null, $fileAction = null) {
        $this->fileBrowser = $fileBrowser;

        $paths = $fileBrowser->readDirectory($path);

        $directories = $fileBrowser->applyFilters($paths, array(new DirectoryFilter()));
        $files = $fileBrowser->applyFilters($paths, array(new DirectoryFilter(false)));

        unset($paths);

        parent::__construct($files, $directories, $action, self::NAME);

        $fileDecorator = new FileDecorator($fileBrowser);
        $fileDecorator->setDirectoryAction($directoryAction . ($path ? '/' . $path : ''));
        $fileDecorator->setFileAction($fileAction);
        $fileDecorator = new ZebraDecorator($fileDecorator);

        $this->addDecorator($fileDecorator);

        $translator = I18n::getInstance()->getTranslator();

        $breadcrumbs = $this->getBreadcrumbs();
        $breadcrumbs->setLabel($translator->translate(self::TRANSLATION_NAVIGATION));
        $breadcrumbs->addBreadcrumb($directoryAction, $translator->translate(self::TRANSLATION_NAVIGATION_HOME));
        $this->addBreadcrumbs($breadcrumbs, $directoryAction, $path);
    }

    /**
     * Adds the breadcrumbs for the current path
     * @param zibo\library\html\Breadcrumbs $breadcrumbs The breadcrumbs container
     * @param string $action URL to the path action
     * @param zibo\library\filesystem\File $path The path to add
     */
    private function addBreadcrumbs($breadcrumbs, $action, File $path = null) {
        if ($path == null) {
            return;
        }

        $pieces = explode(File::DIRECTORY_SEPARATOR, $path->getPath());

        $breadcrumbAction = $action;
        foreach ($pieces as $piece) {
            if (empty($piece) || $piece == FileBrowser::DEFAULT_PATH) {
                continue;
            }

            $breadcrumbAction .= '/' . $piece;
            $breadcrumbs->addBreadcrumb($breadcrumbAction, $piece);
        }
    }

    /**
     * Adds the rename action
     * @param string $action URL to the rename action
     * @return null
     */
    public function addRenameAction($action) {
        $this->addDecorator(new RenameActionDecorator($action));
    }

    /**
     * Adds the edit action for the provided file extensions
     * @param string $action URL to the edit action
     * @param array $extensions Array with the extensions of files which are editable
     * @return null
     */
    public function addEditAction($action, array $extensions) {
        $this->addDecorator(new EditActionDecorator($action, $this->fileBrowser, $extensions));
    }

    /**
     * Gets the HTML of this table. Makes sure there is a option decorator when there are actions attached to this table
     * @return string
     */
    public function getHtml() {
        if ($this->actions) {
            $optionDecorator = new FileOptionDecorator();
            $this->addDecorator($optionDecorator, null, true);
        }

        return parent::getHtml();
    }

}