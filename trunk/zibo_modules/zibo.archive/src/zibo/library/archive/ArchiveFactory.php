<?php

namespace zibo\library\archive;

use zibo\core\Zibo;

use zibo\library\archive\exception\ArchiveException;
use zibo\library\filesystem\File;
use zibo\library\String;

use \ReflectionClass;
use \ReflectionException;

/**
 * Factory for archive objects
 */
class ArchiveFactory {

    /**
     * Configuration key for archive types
     * @var string
     */
    const CONFIG_TYPES = 'archive';

    /**
     * Class name of the archive interface
     * @var string
     */
    const INTERFACE_ARCHIVE = 'zibo\\library\\archive\\Archive';

    /**
     * Array with the archive file extension as key and the class name as value
     * @var array
     */
    private $types;

    /**
     * Constructs a new archive factory
     * @return null
     */
    public function __construct(Zibo $zibo) {
        $this->loadTypes($zibo);
    }

    /**
     * Creates an archive object for the provided file
     * @param zibo\library\filesystem\File $file File of the archive
     * @return Archive
     * @throws zibo\library\archive\exception\ArchiveException when the provided file is not a supported archive, based on extension
     */
    public function getArchive(File $file) {
        $extension = $file->getExtension();

        if (!isset($this->types[$extension])) {
            throw new ArchiveException('Unsupported archive: ' . $extension);
        }

        $className = $this->types[$extension];

        $reflection = new ReflectionClass($className);
        $archive = $reflection->newInstance($file);

        return $archive;
    }

    /**
     * Registers a archive implementation
     * @param string $extension File extension of the archive files
     * @param string $className Name of the archive implementation
     * @return null
     */
    public function register($extension, $className) {
        if (!String::isString($extension, String::NOT_EMPTY)) {
            throw new ArchiveException('Provided extension is empty');
        }
        if (!String::isString($className, String::NOT_EMPTY)) {
            throw new ArchiveException('Provided class name is empty');
        }

        try {
            $reflection = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            throw new ArchiveException('Class ' . $className . ' does not exist');
        }
        if (!$reflection->implementsInterface(self::INTERFACE_ARCHIVE)) {
            throw new ArchiveException('Class ' . $className . ' does not implement the Archive interface');
        }

        $this->types[strtolower($extension)] = $className;
    }

    /**
     * Loads the archive types from the Zibo configuration
     * @return null
     */
    private function loadTypes(Zibo $zibo) {
        $this->types = array();

        $types = $zibo->getConfigValue(self::CONFIG_TYPES, array());
        foreach ($types as $typeName => $className) {
            $this->register($typeName, $className);
        }
    }

}