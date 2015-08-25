<?php

namespace zibo\admin\model\module;

use zibo\admin\model\module\exception\ModuleDependencyNotInstalledException;
use zibo\admin\model\module\exception\ModuleDependencyVersionNotInstalledException;
use zibo\admin\model\module\exception\ModuleNotFoundException;
use zibo\admin\model\module\exception\ModuleStillInUseException;
use zibo\admin\model\module\exception\ModuleZiboVersionNeededException;
use zibo\admin\model\module\exception\ModuleZiboVersionNotInstalledException;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Model of the Zibo modules
 */
class ModuleModel {

    /**
     * Array with the modules
     * @var unknown_type
     */
    private $modules;

    /**
     * Construct a new module model
     * @return null
     */
    public function __construct() {
        $this->modules = array();
    }

    /**
     * Adds modules to the model
     * @param array $modules Array with Module objects
     * @return null
     * @throws zibo\admin\model\exception\ModuleZiboVersionNeededException when no needed Zibo version is set for a module
     * @throws zibo\admin\model\exception\ModuleZiboVersionNotInstalledException when the required Zibo version is not installed
     * @throws zibo\admin\model\exception\ModuleDependencyNotInstalledException when a dependency is not installed
     * @throws zibo\admin\model\exception\ModuleDependencyVersionNotInstalledException when a dependency is installed, but not the required version
     */
    public function addModules(array $modules) {
        $preInstallModules = $this->modules;

        $modules = $this->addModulesWithoutDependencies($modules);
        if (empty($modules)) {
            return;
        }

        $exception = $this->addModulesWithDependencies($modules);
        if (empty($exception)) {
            return;
        }

        $this->modules = $preInstallModules;

        throw $exception;
    }

    /**
     * Adds modules without dependencies to the model
     * @param array $modules Array with Module objects
     * @return array the provided modules without the added modules
     */
    private function addModulesWithoutDependencies(array $modules) {
        foreach ($modules as $index => $module) {
            if ($module->hasDependencies()) {
                continue;
            }

            $this->addSingleModule($module);

            unset($modules[$index]);
        }

        return $modules;
    }

    /**
     * Adds modules with dependencies to the model
     * @param array $modules Array with Module objects
     * @return null|zibo\ZiboException null when all modules were added, an exception when some error occured
     */
    private function addModulesWithDependencies(array $modules) {
        do {
            $hasAddedModules = false;
            $previousException = null;
            $moduleIndexes = array_keys($modules);

            do {
                $index = array_shift($moduleIndexes);
                if (is_null($index)) {
                    break;
                }

                $module = $modules[$index];
                try {
                    $this->addSingleModule($module, $previousException);

                    unset($modules[$index]);

                    $hasAddedModules = true;
                } catch (ZiboException $exception) {
                    $previousException = $exception;
                }
            } while (!is_null($index));

        } while ($hasAddedModules);

        return $previousException;
    }

    /**
     * Adds a single module to the model
     * @param Module $module
     * @param zibo\ZiboException $previousException
     * @return null
     * @throws zibo\admin\model\exception\ModuleZiboVersionNeededException when no needed Zibo version is set for the module
     * @throws zibo\admin\model\exception\ModuleZiboVersionNotInstalledException when the required Zibo version is not installed
     * @throws zibo\admin\model\exception\ModuleDependencyNotInstalledException when a dependency is not installed
     * @throws zibo\admin\model\exception\ModuleDependencyVersionNotInstalledException when a dependency is installed, but not the required version
     */
    private function addSingleModule(Module $module, ZiboException $previousException = null) {
        $namespace = $module->getNamespace();
        $name = $module->getName();
        $ziboVersion = $module->getZiboVersion();

        if (String::isEmpty($ziboVersion)) {
            throw new ModuleZiboVersionNeededException($module, $previousException);
        }
        if (version_compare($ziboVersion, Zibo::VERSION) == 1) {
            throw new ModuleZiboVersionNotInstalledException($module, $ziboVersion, $previousException);
        }

        $this->checkDependencies($module, $previousException);
        $this->addUsage($module);

        if (!isset($this->modules[$namespace])) {
              $this->modules[$namespace] = array();
        }
        $this->modules[$namespace][$name] = $module;

        Zibo::getInstance()->runEvent(Zibo::EVENT_LOG, 'Adding module ' . $name . ' from namespace ' . $namespace . ' to the modules register');
    }

    /**
     * Removes modules from the model
     * @param array $modules Array with Module objects
     * @return null
     * @throws zibo\admin\model\exception\ModuleStillInUseException when the module is still needed by another module
     */
    public function removeModules(array $modules) {
        $preUninstallModules = $this->modules;

        $uninstallModules = array();
        foreach ($modules as $module) {
            $uninstallModules[] = $this->getModule($module->getNamespace(), $module->getName());
        }

        $uninstallModules = $this->removeModulesWithoutUsage($uninstallModules);
        if (empty($uninstallModules)) {
            return;
        }

        $exception = $this->removeModulesWithUsage($uninstallModules);
        if (empty($exception)) {
            return;
        }

        $this->modules = $preUninstallModules;

        throw $exception;
    }

    /**
     * Removes modules without usage from the model
     * @param array $modules Array with Module objects
     * @return array The provided modules array without the removed modules
     */
    private function removeModulesWithoutUsage(array $modules) {
        foreach ($modules as $index => $module) {
            if ($module->hasUsage()) {
                continue;
            }

            $this->removeSingleModule($module);

            unset($modules[$index]);
        }

        return $modules;
    }

    /**
     * Removes modules with usage from the model
     * @param array $modules Array with Module objects
     * @return null|zibo\admin\model\exception\ModuleStillInUseException an exception when a module is still needed by another module
     */
    private function removeModulesWithUsage(array $modules) {
        do {
            $hasRemovedModules = false;
            $moduleIndexes = array_keys($modules);
            $previousException = null;

            do {
                $index = array_shift($moduleIndexes);
                if (is_null($index)) {
                    break;
                }

                $module = $modules[$index];
                try {
                    $this->removeSingleModule($module, $previousException);

                    unset($modules[$index]);

                    $hasRemovedModules = true;
                } catch (ZiboException $exception) {
                    $previousException = $exception;
                }
            } while (!is_null($index));
        } while ($hasRemovedModules);

        return $previousException;
    }

    /**
     * Removes a single module from the model
     * @param Module $module
     * @param zibo\ZiboException $previousException
     * @return null
     * @throws zibo\admin\model\exception\ModuleStillInUseException when the module is still needed by another module
     */
    private function removeSingleModule(Module $module, ZiboException $previousException = null) {
        $name = $module->getName();
        $namespace = $module->getNamespace();

        if ($module->hasUsage()) {
            throw new ModuleStillInUseException($module, $previousException);
        }

        $this->removeUsage($module);

        unset($this->modules[$namespace][$name]);
    }

    /**
     * Checks whether a module is in this model
     * @param string $namespace
     * @param string $name
     * @return boolean true if the module is registered, false otherwise
     */
    public function hasModule($namespace, $name) {
        return isset($this->modules[$namespace][$name]);
    }

    /**
     * Gets a module
     * @param string $namespace
     * @param string $name
     * @return Module
     * @throws zibo\admin\model\exception\ModuleNotFoundException when the module is not installed
     */
    public function getModule($namespace, $name) {
        if (!$this->hasModule($namespace, $name)) {
            throw new ModuleNotFoundException($namespace, $name);
        }

        return $this->modules[$namespace][$name];
    }

    /**
     * Gets an array with all the modules in this model
     * @return array Array with Module instances
     */
    public function getModules() {
        $modules = array();

        foreach ($this->modules as $namespace => $names) {
            foreach ($names as $module) {
                $modules[] = $module;
            }
        }

        return $modules;
    }

    /**
     * Checks if all the dependencies of the provided module are met
     * @param Module $module
     * @param zibo\ZiboException $previousException
     * @return null
     * @throws zibo\admin\model\exception\ModuleDependencyNotInstalledException when a dependency is not installed
     * @throws zibo\admin\model\exception\ModuleDependencyVersionNotInstalledException when a dependency is installed, but not the required version
     */
    private function checkDependencies(Module $module, ZiboException $previousException = null) {
        $moduleDependencies = $module->getDependencies();

        foreach ($moduleDependencies as $moduleDependency) {
            if (!$this->hasModule($moduleDependency->getNamespace(), $moduleDependency->getName())) {
                throw new ModuleDependencyNotInstalledException($module, $moduleDependency, $previousException);
            }

            $installedDependency = $this->getModule($moduleDependency->getNamespace(), $moduleDependency->getName());
            if (version_compare($moduleDependency->getVersion(), $installedDependency->getVersion()) == 1) {
                throw new ModuleDependencyVersionNotInstalledException($module, $moduleDependency, $installedDependency->getVersion(), $previousException);
            }
        }
    }

    /**
     * Adds the usage of the provided module to all the module's dependencies
     * @param Module $module
     * @return null
     */
    private function addUsage(Module $module) {
        $moduleDependencies = $module->getDependencies();

        foreach ($moduleDependencies as $moduleDependency) {
            $installedDependency = $this->getModule($moduleDependency->getNamespace(), $moduleDependency->getName());
            $installedDependency->addUsage($module);
        }
    }

    /**
     * Removes the usage of the provided module from all the module's dependencies
     * @param Module $module
     * @return null
     */
    private function removeUsage(Module $module) {
        $moduleDependencies = $module->getDependencies();

        foreach ($moduleDependencies as $moduleDependency) {
            $installedDependency = $this->getModule($moduleDependency->getNamespace(), $moduleDependency->getName());
            $installedDependency->removeUsage($module);
        }
    }

}