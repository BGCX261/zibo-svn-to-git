<?php

namespace zibo\library\archive;

use zibo\library\archive\exception\ArchiveException;
use zibo\library\filesystem\File;

/**
 * Abstract implementation for an archive
 */
class AbstractArchive implements Archive {

    /**
     * The file of the archive
     * @var zibo\library\filesystem\File
     */
    protected $file;

    /**
     * Constructs a new archive
     * @param zibo\library\filesystem\File $file The file of the archive
     * @return null
     */
    public function __construct(File $file) {
        $this->file = $file;
    }

    /**
     * Compresses a file or combination of files in the archive. This implementation will always throw an exception, implement it to override.
     * @param array|zibo\library\filesystem\File $source The source(s) of the file(s) to compress
     * @param zibo\library\filesystem\File $prefix Path in the archive
     * @return null
     */
    public function compress($source, File $prefix = null) {
        throw new ArchiveException('Unsupported action');
    }

    /**
     * Uncompresses the archive to the provided destination. This implementation will always throw an exception, implement it to override.
     * @param zibo\library\filesystem\File $destination Destination of the uncompressed files
     * @return null
     */
    public function uncompress(File $destination) {
        throw new ArchiveException('Unsupported action');
    }

}