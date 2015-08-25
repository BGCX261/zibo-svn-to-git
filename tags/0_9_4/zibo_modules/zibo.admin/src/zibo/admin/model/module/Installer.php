<?php

namespace zibo\admin\model\module;

use zibo\admin\model\module\io\ModuleIO;
use zibo\admin\model\module\io\XmlModuleIO;

use zibo\core\Zibo;

use zibo\library\archive\ArchiveFactory;
use zibo\library\filesystem\File;
use zibo\library\Structure;

use \Exception;

/**
 * Module installer and uninstaller
 */
class Installer {

    /**
     * Class name of the archive factory
     * @var string
     */
    const CLASS_ARCHIVE_FACTORY = 'zibo\\library\\archive\\ArchiveFactory';

    /**
     * Event run before a module is installed
     * @var string
     */
    const EVENT_PRE_INSTALL_MODULE = 'module.install.pre';

    /**
     * Event run after a module is installed
     * @var string
     */
    const EVENT_POST_INSTALL_MODULE = 'module.install.post';

    /**
     * Event run before the module's install script is run
     * @var string
     */
    const EVENT_PRE_INSTALL_SCRIPT = 'module.install.script.pre';

    /**
     * Event run before a module is uninstalled
     * @var string
     */
    const EVENT_PRE_UNINSTALL_MODULE = 'module.uninstall.pre';

    /**
     * Event run after a module is uninstalled
     * @var string
     */
    const EVENT_POST_UNINSTALL_MODULE = 'module.uninstall.post';

    /**
     * Path to the install script, relative to the module's path
     * @var string
     */
    const INSTALL_SCRIPT = 'src/install.php';

    /**
     * Path to the uninstall script, relative to the module's path
     * @var string
     */
    const UNINSTALL_SCRIPT = 'src/uninstall.php';

    /**
     * Model of the installed modules
     * @var ModuleModel
     */
    private $model;

    /**
     * Input/output implementation for the module definition
     * @var zibo\admin\model\io\ModuleIO
     */
    private $io;

    /**
     * Constructs a new module installer
     * @param zibo\admin\model\io\ModuleIO $io
     * @param ModuleModel $model
     * @return null
     */
    public function __construct(ModuleIO $io = null, ModuleModel $model = null) {
        $this->setModuleIO($io);
        $this->setModuleModel($model);
        $this->readModules();
    }

    /**
     * Sets the module input/output implementation
     * @param zibo\admin\model\io\ModuleIO $io
     * @return null
     */
    private function setModuleIO(ModuleIO $io = null) {
        if ($io == null) {
            $io = new XmlModuleIO();
        }
        $this->io = $io;
    }

    /**
     * Sets the module model
     * @param zibo\admin\model\ModuleModel $model
     * @return null
     */
    private function setModuleModel(ModuleModel $model = null) {
        if ($model == null) {
            $model = new ModuleModel();
        }
        $this->model = $model;
    }

    /**
     * Checks if a module is installed
     * @param string $namespace
     * @param string $name
     * @return boolean true if the module is installed, false otherwise
     */
    public function hasModule($namespace, $name) {
        return $this->model->hasModule($namespace, $name);
    }

    /**
     * Gets a module from the register
     * @param string $namespace
     * @param string $name
     * @return Module
     * @throws zibo\admin\model\exception\ModuleNotFoundException when the module is not found
     */
    public function getModule($namespace, $name) {
        return $this->model->getModule($namespace, $name);
    }

    /**
     * Gets an array with all the installed modules
     * @return array Array with Module objects
     */
    public function getModules() {
        return $this->model->getModules();
    }

    /**
     * Installs a module directory or a module file.
     *
     * Runs the following events. All events have this instance of Installer, the path of the
     * modules and an array of the modules as argument:
     * <ul>
     *  <li>EVENT_PRE_INSTALL_MODULE: before any copying
     *  <li>EVENT_PRE_INSTALL_SCRIPT: after copying, before running the install script
     *  <li>EVENT_POST_INSTALL_MODULE: after running the install script
     * </ul>
     * @param zibo\library\filesystem\File $file
     * @return null
     */
    public function installModule(File $file) {
        $zibo = Zibo::getInstance();

        $modules = $this->getModulesFromPath($file);

        $zibo->runEvent(self::EVENT_PRE_INSTALL_MODULE, $this, $file, $modules);

        $modulePath = $this->copyFile($file);
        foreach ($modules as $module) {
            $module->setPath($modulePath);
        }

        $preInstallModel = clone($this->model);

        try {
            $this->model->addModules($modules);

            $zibo->resetFileBrowser();

            $zibo->runEvent(self::EVENT_PRE_INSTALL_SCRIPT, $this, $modulePath, $modules);

            $this->runInstallScript($modulePath);

            $zibo->runEvent(self::EVENT_POST_INSTALL_MODULE, $this, $modulePath, $modules);
        } catch (Exception $exception) {
            $this->model = $preInstallModel;

            $modulePath->delete();

            $zibo->resetFileBrowser();

            throw $exception;
        }
    }

    /**
     * Uninstalls a module directory or a module file.
     *
     * Runs the following events. All events have this instance of Installer, the path of the
     * modules and an array of the modules as argument:
     * <ul>
     *  <li>EVENT_PRE_UNINSTALL_MODULE: before any removing of files
     *  <li>EVENT_POST_UNINSTALL_MODULE: after the files have been removed and the uninstall script ran
     * </ul>
     * @param zibo\library\filesystem\File $file
     * @return null
     */
    public function uninstallModule(File $modulePath) {
        $zibo = Zibo::getInstance();

        $modules = $this->getModulesFromPath($modulePath);

        $preUninstallModel = clone($this->model);

        try {
            $this->model->removeModules($modules);

            $zibo->runEvent(self::EVENT_PRE_UNINSTALL_MODULE, $this, $modulePath, $modules);

            $this->runUninstallScript($modulePath);
            $modulePath->delete();

            $zibo->resetFileBrowser();

            $zibo->runEvent(self::EVENT_POST_UNINSTALL_MODULE, $this, $modulePath, $modules);
        } catch (Exception $exception) {
            $this->model = $preUninstallModel;

            throw $exception;
        }
    }

    /**
     * Updates the module model with the installed modules
     * @return null
     */
    private function readModules() {
        $modules = array();

        $path = new File(Zibo::DIRECTORY_MODULES);
        $files = $path->read();
        foreach ($files as $file) {
            // skip hidden files
            if (strncmp($file->getName(), '.', 1) === 0) {
                continue;
            }

            $fileModules = $this->getModulesFromPath($file);

            $modules = Structure::merge($modules, $fileModules);
        }

        $this->model->addModules($modules);
    }

    /**
     * Gets the modules from a path
     * @param zibo\library\filesystem\File $file
     * @return array Array with Module objects
     */
    private function getModulesFromPath(File $file) {
        $modules = $this->io->readModules($file);

        foreach ($modules as $module) {
            $module->setPath($file);
        }

        return $modules;
    }

    /**
     * Copies a file into the modules directory
     * @param zibo\library\filesystem\File $file
     * @return zibo\library\filesystem\File the destination file
     */
    private function copyFile(File $file) {
        if ($file->getExtension() == 'phar' && class_exists(self::CLASS_ARCHIVE_FACTORY)) {
            $module = new File(Zibo::DIRECTORY_MODULES, substr($file->getName(), 0, -5));

            $archive = ArchiveFactory::getInstance()->getArchive($file);
            $archive->uncompress($module);
        } else {
            $module = new File(Zibo::DIRECTORY_MODULES, $file->getName());

            if ($module->getAbsolutePath() != $file->getAbsolutePath()) {
                $file->copy($module);
            }
        }

        return $module;
    }

    /**
     * Includes the install script, if it exists
     * @param zibo\library\filesystem\File $path Path of the module
     * @return null
     */
    public function runInstallScript(File $path) {
        $this->runScript(new File($path, self::INSTALL_SCRIPT));
    }

    /**
     * Includes the uninstall script, if it exists
     * @param zibo\library\filesystem\File $path Path of the module
     * @return null
     */
    private function runUninstallScript(File $path) {
        $this->runScript(new File($path, self::UNINSTALL_SCRIPT));
    }

    /**
     * Includes a PHP script
     * @param zibo\library\filesystem\File $script Path to the script
     * @return null
     */
    private function runScript(File $script) {
        if ($script->exists()) {
            include($script->getPath());
        }
    }

}