<?php

namespace zibo\api\model;

use zibo\api\model\doc\DocParser;

use zibo\core\Zibo;

use zibo\library\filesystem\File;

/**
 * Zibo live API browser using PHP's reflection interface
 */
class ApiBrowser {

    /**
     * Separator between namespace parts
     * @var string
     */
    const NAMESPACE_SEPARATOR = '/';

    /**
     * Parser for the doc comments
     * @var zibo\api\model\doc\DocParser
     */
    private static $docParser;

    /**
     * Extensions for the files considered as PHP sources
     * @var array
     */
    private $sourceExtensions = array('php', 'inc');

    /**
     * Get the parser for doc comments
     * @return zibo\api\model\doc\DocParser
     */
    public static function getDocParser() {
        if (!self::$docParser) {
            self::$docParser = new DocParser();
        }

        return self::$docParser;
    }

    /**
     * Get all the namespaces, based on the file system
     * @param string $namespace get all the namespaces which are in this provided namespace (optional)
     * @return array Ordered array with the namespace as key and value
     */
    public function getNamespaces($namespace = null) {
        $namespaces = array();

        $includePaths = Zibo::getInstance()->getIncludePaths();
        foreach ($includePaths as $includePath) {
            $sourcePath = new File($includePath, Zibo::DIRECTORY_SOURCE);
            if ($namespace) {
                $sourcePath = new File($sourcePath, $namespace);
                $namespaces = $this->readNamespacesFromPath($sourcePath, $namespaces, $namespace . self::NAMESPACE_SEPARATOR);
            } else {
                $namespaces = $this->readNamespacesFromPath($sourcePath, $namespaces);
            }
        }

        ksort($namespaces);

        return $namespaces;
    }

    /**
     * Get the classes of a namespace, based on the file system
     * @param string $namespace namespace of the classes
     * @param boolean $recursive look in subdirectories
     * @param string $query case insensitive, file name based search query
     * @return array Ordered array with namespace and class name as key and the class name as value
     */
    public function getClassesForNamespace($namespace, $recursive = false, $query = null) {
        $namespace = str_replace('\\', self::NAMESPACE_SEPARATOR, $namespace);
        $classes = array();

        $includePaths = Zibo::getInstance()->getIncludePaths();
        foreach ($includePaths as $includePath) {
            $sourcePath = new File($includePath, Zibo::DIRECTORY_SOURCE);

            if ($namespace) {
                $namespacePath = new File($sourcePath, $namespace);
                if ($namespace[strlen($namespace) - 1] != self::NAMESPACE_SEPARATOR) {
                    $namespace .= self::NAMESPACE_SEPARATOR;
                }
            } else {
                $namespacePath = $sourcePath;
            }

            $classes = $this->readClassesFromPath($namespacePath, $classes, $namespace, $recursive, $query);
        }

        ksort($classes);

        return $classes;
    }

    /**
     * Get the reflection class object of a class
     * @param string $namespace namespace of the class
     * @param string $class name of the class
     * @return zibo\api\model\ReflectionClass
     */
    public function getClass($namespace, $class) {
        $className = str_replace(self::NAMESPACE_SEPARATOR, '\\', $namespace) . '\\' . $class;

        $reflectionClass = new ReflectionClass($className);

        return $reflectionClass;
    }

    /**
     * Read all the classes in a path
     * @param zibo\library\filesystem\File $path path to read
     * @param array $classes already found classes
     * @param string $namespace look for classes in the provided namespace
     * @param boolean $recursive look in subdirectories
     * @param string $query only return class names which match this query (optional)
     * @return array Array with namespace and class name as key and the class name as value
     */
    private function readClassesFromPath(File $path, array $classes, $namespace, $recursive, $query = null) {
        if (!$path->exists()) {
            return $classes;
        }

        $files = $path->read();
        foreach ($files as $file) {
            if ($file->isDirectory()) {
                if ($recursive) {
                    $classes = $this->readClassesFromPath($file, $classes, $namespace . $file->getName() . self::NAMESPACE_SEPARATOR, $recursive, $query);
                }
                continue;
            }

            $extension = $file->getExtension();
            if (!in_array($extension, $this->sourceExtensions)) {
                continue;
            }

            if ($query && stripos($file->getName(), $query) === false) {
                continue;
            }

            $name = substr($file->getName(), 0, -4);
            $classes[$namespace . $name] = $name;
        }

        return $classes;
    }

    /**
     * Read all the namespaces in a path
     * @param zibo\library\filesystem\File $path path to read
     * @param array $namespaces already found namespaces
     * @param string $prefix namespace prefix for the results
     * @return array Array with namespaces as key and as value
     */
    private function readNamespacesFromPath(File $path, array $namespaces, $prefix = null) {
        if (!$path->exists()) {
            return $namespaces;
        }

        $files = $path->read();
        foreach ($files as $file) {
            if (!$file->isDirectory()) {
                continue;
            }

            $name = $prefix . $file->getName();
            $namespaces[$name] = $name;
            $namespaces = $this->readNamespacesFromPath($file, $namespaces, $name . self::NAMESPACE_SEPARATOR);
        }

        return $namespaces;
    }

}