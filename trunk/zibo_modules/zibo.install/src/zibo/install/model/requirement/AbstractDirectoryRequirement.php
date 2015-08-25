<?php

namespace zibo\install\model\requirement;

use zibo\core\Zibo;

use zibo\library\filesystem\File;

use zibo\ZiboException;

/**
 * Requirement implementation to check if a directory exists and is writable
 */
class AbstractDirectoryRequirement extends AbstractRequirement {

    /**
     * The path to check, relative to the root
     * @var string
     */
    private $path;

    /**
     * Translation key for when the directory does not exist
     * @var string
     */
    private $messageExists;

    /**
     * Translation key for when the directory does not exist
     * @var string
     */
    private $messageWritable;

    /**
     * Constructs a new requirement
     * @return null
     */
    public function __construct($path, $name, $messageExists, $messageWritable = null) {
        parent::__construct($name, null);

        $this->path = $path;

        $this->messageExists = $messageExists;
        $this->messageWritable = $messageWritable;
    }

    /**
     * Checks if the application directory exists and is writable
     * @return null
     */
    public function performCheck() {
        $root = Zibo::getInstance()->getRootPath();
        $directory = new File($root, $this->path);

        $this->isMet = false;

        if (!$directory->exists()) {
            try {
                $directory->create();
            } catch (ZiboException $exception) {
                $this->message = $this->messageExists;
                return;
            }
        }

        if ($this->messageWritable && !$directory->isWritable()) {
            $this->message = $this->messageWritable;
            return;
        }

        $this->isMet = true;
    }

}